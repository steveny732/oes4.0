<?php
/* ************************************************************************
' admin_entryupdate2.php -- Administration - Update an entry #2
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
'		betap 2008-06-29 16:02:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'entry.php');
require_once(LIB_PATH. 'agecategorytype.php');
require_once(LIB_PATH. 'agecategory.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

// Place the form fields in the dictionary
foreach ($_REQUEST as $key => $value) {
	$formFieldName = $key;
	$formFieldValue = $value;
	$p_params[$formFieldName] = $formFieldValue;
}
if ($p_params['paid'] == 'on') {
	$p_params['paid'] = 1;
} else {
	$p_params['paid'] = 0;
}
// Rebuild the wait date/time from the individual fields (if displayed)
if (!empty($p_params['waitdate'])) {
	$p_params['waitdate'] .= ' '. substr($p_params['waittime'], 1, 8);
}

$entrybefore = Entry::findByIdForEntry($p_params['id']);

$p_raceid = $entrybefore->raceid;
$p_seriesid = $entrybefore->seriesid;
$p_seriesracenumbers = $entrybefore->seriesracenumbers;

$p_racenumber = $p_params['preallnos_'. $p_raceid];
if (empty($p_racenumber) || !$p_params['paid']) {
	$p_racenumber = '0';
}
$race = Race::findById($p_raceid);

// See if this is a waiting list confirmation
$offer = Transfer::findByOfferTo($p_raceid, $p_params['id']);

// Complete any waiting list transfer etc. (we're going to re-use the existing racenumber
// so we don't need to allocate a new one)
if (!empty($offer)) {

	if ($p_params['paid']) {
		$p_racenumber = $oldentry->racenumber;
		$p_params['racenumber'] = $p_racenumber;
		$p_originalentryid = $offer->entryid;

		$oldentry = findById($p_originalentryid);
		$oldentry->delete();

		$offer->complete = 1;
		$offer->offertoentryid = 0;
		$offer->offereddate = '';
		$offer->save();
	}
} else {

	// Allocate race number (if necessary)
	$p_racenumber = allocateRaceNumber($p_seriesid, $p_seriesracenumbers, $p_raceid, $race->allocatenumbers, $p_params['clubid'], $p_params['paid'], $p_racenumber);
}

$p_params['racenumber'] = $p_racenumber;
if (empty($p_params['racenumber']) && $race->allocatenumbers) {
	$p_params['paid'] = false;
}
if ($p_params['createentrant'] == 'on') {
	$entrant = Entrant::findByDetails($p_params['surname'], $p_params['firstname'], $p_params['postcode'], $p_params['dob']);
	if (!$entrant) {
		$entrant = new Entrant();
	}
} else {
	$p_params['entrantid'] = $entrybefore->entrantid;
	if (empty($p_params['entrantid'])) {
		$entrant = new Entrant();
	} else {
		$entrant = Entrant::findById($p_params['entrantid']);
	}
}

// Get entrants table metadata
$columns = DatabaseMetadata::findMetadata('entrants');
foreach ($columns as $column) {
	$dbName = $column->Field;
	if ($dbName != 'id' &&
		$dbName != 'username' &&
		$dbName != 'password' &&
		$dbName != 'passwordref' &&
		$dbName != 'mainentrantid') {

		if ($column->Type == 'tinyint(1)') {
			$entrant->$dbName = $p_params[$dbName] ? 1 : 0;
		} else {
			$entrant->$dbName = $p_params[$dbName];
		}
	}
}
$entrant->save();
$p_params['entrantid'] = $entrant->id;

$entry = Entry::findByIdForUpdate($p_params['id']);

// Get entries table metadata
$clubname = "";
$columns = DatabaseMetadata::findMetadata('entries');
foreach ($columns as $column) {
	$dbName = $column->Field;
	if ($dbName != 'id') {
		$p_data = $p_params[$dbName];
		if ($dbName == "agecategorycode") {
			if (!empty($p_params['overridecategory'])) {
				$p_agecategorycode = $p_params['overridecategory'];
			} else {
				$agecategorytype = AgeCategoryType::findById($race->agecategorytypeid);
				$p_agecategorycode = AgeCategory::calcAgeCategory($race->date, $agecategorytype->basis, $agecategorytype->id, $p_params['dob'], $p_params['gender']);
			}
			$p_data = $p_agecategorycode;
		} elseif ($dbName == "clubid" && !empty($p_data)) {
			$club = Club::findById($p_data);
			$clubname = $club->name;
		} elseif ($dbName == 'salesitems') {
			$p_data = '';
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
					if (!empty($p_data)) {
						$p_data .= ',';
					}
					$p_data .= $racesalesitem->id. ';'. $racesalesitem->price. ';'. $qty. ';'. $racesalesitem->allowquantity;
				}
			}
		}
		if ($column->Type == 'tinyint(1)') {
			$p_data = $p_data ? 1 : 0;
		}
		if (((!empty($entry->$dbName) && $entry->$dbName <> '0000-00-00 00:00:00' && $entry->$dbName <> '0000-00-00' && $entry->$dbName <> '0.00') || !empty($p_data)) &&
				$entry->$dbName != $p_data) {
			$change = new Change();
			$change->entryid = $entry->id;
			$change->source = 'Online admin change';
			$change->column = $dbName;
			$change->oldvalue = $entry->$dbName;
			$change->newvalue = $p_data;
			$change->userid = $session->id;
			$change->save();
			$entry->$dbName = $p_data;
		}
	}
}
$entry->save();

if (!$entrybefore->paid && $p_params['paid'] ||
	$p_params['sendemail'] == 'on') {
	// This is only so the email routine 'sees' the club name
	$entry->clubname = $clubname;
	$res = sendEntryEmail('emailentry', $entry, $race, $p_params['languagecode']);
}

redirect_to('admin_entrylist.php');
?>