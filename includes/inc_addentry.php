<?php
/* ************************************************************************
' inc_addentry.php -- Generic entry addition routine
'
' Copyright (c) 1999-2008 The openEntrySystem Project Team
'
'		This library is free software; you can redistribute it and/or
'		modify it under the terms of the GNU Lesser General Public
'		License as published by the Free Software Foundation; either
'		version 2.1 of the License, or (at your option) any later version.
'
'		This library is distributed in the hope that it will be useful,
'		but WITHOUT ANY WARRANTY; without even the implied warranty of
'		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
'		Lesser General Public License for more details.
'
'		You should have received a copy of the GNU Lesser General Public
'		License along with this library; if not, write to the Free Software
'		Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
'
'		For full terms see the file licence.txt.
'
'	Version: 1.0
'	Last Modified By:
'		betap 2008-07-16 18:32:55
'		Pass discount to add_entry routine and correct online flag on multiple race entries.
'		betap 2008-06-29 21:22:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once("includes/initialise.php");

// Place all the form fields in the dictionary
foreach ($_REQUEST as $key => $value) {
	$p_params[$key] = htmlspecialchars($value, ENT_QUOTES);
}
$p_params['paid'] = isset($p_params['paid']) && $p_params['paid'] == 'on' ? true : false;
$p_params['online'] = isset($p_params['online']) && $p_params['online'] == 'on' ? true : false;
$p_params['waiting'] = isset($p_params['waiting']) && $p_params['waiting'] == 'on' ? true : false;
$p_params['affil'] = isset($p_params['affil']) && $p_params['affil'] == 'on' ? true : false;
$p_params['giftaid'] = isset($p_params['giftaid']) && $p_params['giftaid'] == 'on' ? true : false;
$p_charval = isset($p_params['charval']) ? $p_params['charval'] : '';
$p_disctaken = isset($p_params['discount']) ? $p_params['discount'] : '';
$p_entrantid = isset($p_params['entrantid']) ? $p_params['entrantid'] : '';
$p_couponcode = isset($p_params['couponcode']) ? $p_params['couponcode'] : '';

// Iterate through the selected races and add entries for each
$p_yetToApplyOnlineFee = true;
$p_yetToApplyChipRentalFee = true;
$p_savedSeriesID = '';
$p_raceCount = 0;
$p_applyDiscount = false;
$p_discount = 0;
$p_total = 0;
$saved_seriesid = 0;

$p_racenumber = '0';

$races = Race::findAllBySeries('', $p_params['formid'], $p_processasp);

foreach ($races as $race) {
	$p_salesitems = '';
	$p_raceid = $race->id;
	$p_params['raceid_'. $p_raceid] = !empty($p_params['raceid_'. $p_raceid]);
	$p_raceSelected = $p_params['raceid_'. $p_raceid];
	$p_seriesid = $race->seriesid;
	if ($p_seriesid != $p_savedSeriesID) {
		$p_savedSeriesID = $p_seriesid;
		$p_raceCount = 0;
		$p_applyDiscount = false;
		$p_discount = 0;
	}
	if ($p_raceSelected) {
		$p_raceCount++;
	}
	if (!empty($race->discnoraces)) {
		if ($p_raceCount >= $race->discnoraces) {
			$p_applyDiscount = true;
		}
	}
	if ($p_applyDiscount) {
		$p_discount += $race->discount;
	}
	if ($p_adminEntry) {
		if (!empty($p_params['preallnos_'. $p_raceid])) {
			$p_racenumber = $p_params['preallnos_'. $p_raceid];
		}
	}
	if ($p_raceSelected) {
		//$race = Race::findWithPricingById($p_raceid);
		$p_coupondisc = 0;
		if (!empty($p_couponcode)) {
			$coupon = Coupon::findByRaceAndCouponCode($p_raceid, $p_couponcode);
			if (!empty($coupon)) {
				$p_coupondisc = $coupon->discount;
			}
		}

		$p_total = $race->racefee + $race->charityfee + $p_charval;
		$p_charval = 0;
		if (!$p_params['affil'] || (!$p_params['memberaffil'] && empty($p_params['regno']))) {
			$p_total += $race->affilfee;
		}
		if ($race->transportenabled && $p_params['transportreq']) {
			$p_total += $race->transportfee;
		}
		// Always apply online charges for public entries
		if (!empty($race->raceonlinefee) && ($p_params['online'] || !$p_adminEntry) && $p_yetToApplyOnlineFee) {
			$p_total += $race->raceonlinefee;
			$p_yetToApplyOnlineFee = false;
		} else {
			$p_params['online'] = false;
		}
		// Only apply chip rental fee once
		if (!empty($race->chiprentalfee) && empty($p_params['ownchipno']) && $p_yetToApplyChipRentalFee) {
			$p_total += $race->chiprentalfee;
			$p_yetToApplyChipRentalFee = false;
		}
		$p_discount += $p_disctaken + $p_coupondisc;
		// Only apply the entered discount once
		$p_disctaken = 0;
		$p_total -= $p_discount;

		// Apply any sales items
		$racesalesitems = RaceSalesItem::findAllForRaceId($p_raceid, $cookieLanguageCode);
		foreach ($racesalesitems as $racesalesitem) {
			if ($p_params['si_'. $p_raceid. '_'. $racesalesitem->id]) {
				$p_params['si_'. $p_raceid. '_'. $racesalesitem->id] = true;
			}
			if ($p_params['si_'. $p_raceid. '_'. $racesalesitem->id]) {
				if ($racesalesitem->allowquantity) {
					$qty = $p_params['siqty_'. $p_raceid. '_'. $racesalesitem->id];
				} else {
					$qty = 1;
				}
				$p_total += $racesalesitem->price * $qty;
				if (!empty($p_salesitems)) {
					$p_salesitems .= ',';
				}
				$p_salesitems .= $racesalesitem->id. ';'. $racesalesitem->price. ';'. $qty. ';'. $racesalesitem->allowquantity;
			}
		}

		if ($p_waitingListEntry) {
			// For waiting list adds, check if there are any available pre-allocation numbers for the entered club
			// if there are, we'll process this as a normal entry
			if (!empty($p_params['clubid'])) {
				$preallocation = Preallocation::countAllForClub($p_raceid, $p_params['clubid']);
				if ($preallocation > 0) {
					$p_waitingListEntry = false;
					$p_params['waiting'] = false;
				}
			}
		}

		// For waiting list entries, prevent someone registering multiple times
		if ($p_waitingListEntry) {
			$entrycheck = Entry::findWaitingListByDetails($p_raceid, $p_params['surname'], $p_params['firstname'], $p_params['postcode'], $p_params['dob']);
			if (!empty($entrycheck)) {
				redirect_to('addwaitlist4.php?error=duplicate');
			}
		}
		// ##### Add/Update entrant #######################################################################
		// if entrant not logged on, either add a new or update an existing entrant
		if (empty($p_entrantid)) {
			// Check if an entrant already exists with the entered firstname, surname, dob and postcode
			$entrant = Entrant::findByDetails($p_params['surname'], $p_params['firstname'], $p_params['postcode'], $p_params['dob']);
			if (!$entrant) {
				$entrant = new Entrant();
			}
		} else {
			$entrant = Entrant::findById($p_entrantid);
		}

		$columns = DatabaseMetadata::findMetadata('entrants');
		foreach ($columns as $column) {
			$dbName = $column->Field;
			if ($dbName != 'id' &&
			 $dbName != 'password' &&
			 $dbName != 'passwordref') {

				if ($dbName == 'mainentrantid' &&
					$_SESSION['entrantid'] != $p_params['id']) {
					$p_data = $_SESSION['entrantid'];
				} else {
					$p_data = isset($p_params[$dbName]) ? $p_params[$dbName] : '';
				}
				if ($column->Type == 'tinyint(1)') {
					$p_data = $p_data ? 1 : 0;
				}
				$entrant->$dbName = $p_data;
			}
		}
		$entrant->save();
		$p_entrantid = $entrant->id;

		// Don't store entrantid during admin sessions
		if (empty($_SESSION['entrantid']) && !$session->isLoggedIn()) {
			$_SESSION['entrantid'] = $p_entrantid;
			$_SESSION['registered'] = '';
		}
		// ##### Now add entry ###########################################################################
		if ($p_params['paid']) {
			$p_seriesracenumbers = false;
			if (!empty($p_seriesid)) {
				$serie = Series::findById($p_seriesid);
				if ($serie) {
					$p_seriesracenumbers = $serie->seriesracenumbers;
				}
			}
			$p_needNewNumber = false;

			// if this is an entry for a race not in a series or within a series without series-wide race numbers, or
			// part of a different series, we can allocate a new race number
			if (empty($p_seriesid) ||
				!$p_seriesracenumbers ||
				$p_seriesid != $saved_seriesid) {
				$saved_seriesid = $p_seriesid;
				$p_needNewNumber = true;
			}
			if ($p_needNewNumber) {
				$p_racenumber = allocateRaceNumber($p_seriesid, $p_seriesracenumbers, $p_raceid, $race->allocatenumbers, $p_params['clubid'], $p_params['paid'], $p_racenumber);
			}
		} else {
			$p_racenumber = 0;
		}

		$entry = new Entry();
		$entry->waitdate = date('c');

		$p_agecategorycode = '';
		$columns = DatabaseMetadata::findMetadata('entries');
		foreach ($columns as $column) {
			$dbName = $column->Field;
			if ($dbName != 'id' && ($dbName != 'waitdate' || !$p_waitingListEntry)) {
				if ($dbName == "tshirt" && !$race->tshirtsenabled ||
					 $dbName == 'team' && !$race->teamsenabled ||
					 $dbName == 'regno' && !$race->clubsenabled ||
					 $dbName == 'ownchipno' && !$race->ownchipallowed ||
					 $dbName == 'clubid' && !$race->clubsenabled) {
					$p_data = '';
				} elseif ($dbName == 'option1' && !$race->option1enabled ||
					$dbName == 'option2' && !$race->option2enabled) {
					$p_data = 0;
				} else {
					$p_data = isset($p_params[$dbName]) ? $p_params[$dbName] : '';
					if ($dbName == 'raceid') {
						$p_data = $p_raceid;
					} elseif ($dbName == 'entrantid') {
						$p_data = $p_entrantid;
					} elseif ($dbName == 'racenumber') {
						$p_data = $p_racenumber;
					} elseif ($dbName == 'total') {
						$p_data = $p_total;
					} elseif ($dbName == 'discount') {
						$p_data = $p_discount;
					} elseif ($dbName == 'agecategorycode') {
						if (empty($p_params['dob'])) {
							$p_agecategorycode = '';
						} else {
							$agecategorytype = AgeCategoryType::findById($race->agecategorytypeid);
							$p_agecategorycode = AgeCategory::calcAgeCategory($race->date, $agecategorytype->basis, $agecategorytype->id, $p_params['dob'], $p_params['gender']);
						}
						$p_data = $p_agecategorycode;
					}
				}
				if ($column->Type == 'tinyint(1)') {
					$p_data = $p_data ? 1 : 0;
				}
				$entry->$dbName = $p_data;
			}
		}
		if (!empty($p_salesitems)) {
			$entry->salesitems = $p_salesitems;
		}
		$entry->save();
		$change = new Change();
		$change->entryid = $entry->id;
		if ($_SERVER['SCRIPT_FILENAME'] == 'addwaitlist3.php') {
			$change->source = 'Wait list add';
			$change->userid = '*entrant';
		} elseif ($_SERVER['SCRIPT_FILENAME'] == 'enter3.php') {
			$change->source = 'Public entry';
			$change->userid = '*entrant';
		} else {
			$change->source = 'Online admin add';
			$change->userid = $session->id;
		}
		$change->save();

		if ($p_params['paid'] && strtoupper($p_params['email']) != '*NONE') {
			if (!empty($entry->clubid)) {
				$club = Club::findById($entry->clubid);
				$entry->clubname = $club->name;
			}
			$res = sendEntryEmail('emailentry', $entry, $race, $entry->languagecode);
		}
		$p_raceSelected = false;
	}
} ?>