<?php
/* ************************************************************************
' confirm_transfer2.php -- Confirm transfer #2 - Perform update
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
'		betap 2008-06-29 21:22:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once("includes/initialise.php");
require_once(LIB_PATH. "transfer.php");

if (!defined('DB_SERVER') || $currentSite->downformaint && !$session->isLoggedIn()) {
	redirect_to("downformaint.php");
}

$transfer = Transfer::findByIdForUpdate($_REQUEST['transferid']);
$entry = Entry::findByIdForUpdate($transfer->entryid);

$clubname = "";
$columns = DatabaseMetadata::findMetadata('entries');
foreach ($columns as $column) {
	$dbName = $column->Field;
	// The following columns are not changed. As we're reading the entries table metadata, any columns we process
	// here will be overwritten regardless of their presence on the transfers table, so we could end up clearing
	// entries columns
	if ($dbName != 'id' &&
		$dbName != 'ordernumber' &&
		$dbName != 'paid' &&
		$dbName != 'racenumber' &&
		$dbName != 'raceid' &&
		$dbName != 'giftaid' &&
		$dbName != 'total' &&
		$dbName != 'online' &&
		$dbName != 'waiting' &&
		$dbName != 'entrantid' &&
		$dbName != 'offercount' &&
		$dbName != 'deleted' &&
		$dbName != 'discount' &&
		$dbName != 'waitdate' &&
		$dbName != 'charval') {
		$dbcolumn = DbColumn::findByName($dbName);
		$transferColName = "new". $dbName;
		/* One final check of the dbcolumn persistence flag and we're good to go. If the column is
		   on the transfer table, always update. If it isn't, and the transferpersist flag is NOT set
		   leave the entries column untouched. We're checking for transferpersist being not set as
		   the logic here is reversed - we don't have a transfers column to source data from so not
		   adding an entry here leaves the entries column untouched i.e. persisted */
		if (isset($transfer->$transferColName) ||
			(!isset($transfer->$transferColName) && !$dbcolumn->transferpersist)) {
			$p_data = $transfer->$transferColName;
			if ($dbName == "clubid" && !empty($p_data)) {
				$club = Club::findById($p_data);
				$clubname = $club->name;
			}
			if ($column->Type == 'tinyint(1)') {
				$p_data = $p_data ? 1 : 0;
			}
			if (((!empty($entry->$dbName) && $entry->$dbName <> '0000-00-00 00:00:00') || !empty($p_data)) &&
					$entry->$dbName != $p_data) {
				$change = new Change();
				$change->entryid = $entry->id;
				$change->source = 'Transfer '. $transfer->code;
				$change->column = $dbName;
				$change->oldvalue = $entry->$dbName;
				$change->newvalue = $p_data;
				$change->save();
				$entry->$dbName = $p_data;
			}
		}
	}
}

// Generate a new entrant for the transferee or link to an existing one
$entrant = Entrant::findByDetails($entry->surname, $entry->firstname, $entry->postcode, $entry->dob);
if (!$entrant) {
	$entrant = new Entrant();
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
			$p_data = $entry->$dbName;
		}
		$entrant->$dbName = $p_data;
	}
}
$entrant->save();

$entry->entrantid = $entrant->id;
$entry->save();

$transfer->complete = 1;
$transfer->save();

$race = Race::findById($transfer->raceid);

// This is only so the email routine 'sees' the club name
$entry->clubname = $clubname;
$res = sendEntryEmail('emailentry', $entry, $race, $cookieLanguageCode);

redirect_to('confirm_transfer3.php');
?>