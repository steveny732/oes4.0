<?php
/* ************************************************************************
' transfer4.php -- Transfer/Change a race entry #4 - Set up transfer & send emails
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
'		betap 2008-07-01 09:16:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once("includes/initialise.php");
require_once(LIB_PATH. "content.php");

if (!defined('DB_SERVER') || $currentSite->downformaint && !$session->isLoggedIn()) {
	redirect_to("downformaint.php");
}

// Place the form fields in the dictionary
foreach ($_REQUEST as $name => $value) {
	$p_params[$name] = htmlspecialchars($value, ENT_QUOTES);
}

$entry = Entry::findById($_REQUEST['id']);

// Affiliation can't change from affiliated (the form field is readonly, so force back the original)
if ($entry->affil) {
	$p_params['affil'] = $entry->affil;
}
$p_params['entryid'] = $entry->id;
$p_params['raceid'] = $entry->raceid;

$race = Race::findById($p_params['raceid']);

// Supercede all other transfers/changes
$result = Transfer::supercedeEntry($p_params['raceid'], $p_params['entryid']);

$transfer = new Transfer();
$transfer->code = htmlspecialchars($_REQUEST['transferno'], ENT_QUOTES);
$transfer->complete = 0;
$transfer->offerready = 0;

$columns = DatabaseMetadata::findMetadata('transfers');
foreach ($columns as $column) {
	$originalDbName = $column->Field;
	$dbName = $column->Field;
	
	// For offers, form an INSERT statement populating the 'transfer_old' columns from
	// entries values, leaving the 'transfer_new' columns empty at this stage
	// For normal transfers and changes, form an INSERT statement populating the 'transfer_old' columns from
	// entries values and the 'transfer_new' columns from the form
	if ($dbName != 'id' &&
		$dbName != 'code' &&
		$dbName != 'complete' &&
		$dbName != 'offerready' &&
		($_REQUEST['change'] != '2' ||
		substr($dbName, 0, 3) != 'new')) {

		if (substr($dbName, 0, 3) == 'old' || substr($dbName, 0, 3) == 'new') {
			$dbName = substr($dbName, 3);
		}
		if (substr($column->Field, 0, 3) == 'old') {
			$p_data = $entry->$dbName;
		} else {
			$p_data = isset($p_params[$dbName]) ? $p_params[$dbName] : '';
			if ($dbName == 'agecategorycode') {
				$agecategorytype = AgeCategoryType::findById($race->agecategorytypeid);
				$p_data = AgeCategory::calcAgeCategory($race->date, $agecategorytype->basis, $agecategorytype->id, $p_params['dob'], $p_params['gender']);
			}
			if ($column->Type == 'tinyint(1)') {
				$p_data = $p_data ? 1 : 0;
			}
		}
		$transfer->$originalDbName = $p_data;
	}
}

$transfer->save();

// Send an email
if ($entry->email == '*NONE') {
	$extrahtml = 'Email not available - Confirmation needed - CALL '. $p_params['telday']. ' (day) '. $p_params['televe']. ' (evening)<br /><br />';
} else {
	$extrahtml = '';
}
$p_from = $race->emailfrom;
$p_to = $entry->email;
if (strtoupper($race->emailbcc) != '*NONE') {
	$p_bcc = $race->emailbcc;
} else {
	$p_bcc = $currentSite->entrybcc;
}
if ($_REQUEST['change'] == '0') {
	$p_emailtype = 'emailconfirmtransfer';
} elseif ($_REQUEST['change'] == '1') {
	$p_emailtype = 'emailconfirmchange';
} elseif ($_REQUEST['change'] == '2') {
	$p_emailtype = 'emailconfirmoffer';
}
// Get entry email content
$contents = Content::findByURL($p_emailtype, $cookieLanguageCode);
$content = array_shift($contents);

if ($_REQUEST['change'] == '0') {
	$p_subject = $content->text;
	$p_subject = str_replace('*RACENAME', $race->name, $p_subject);
	$content = array_shift($contents);
	$p_body = $extrahtml. $content->text;
	$p_body = str_replace('*FIRSTNAME', $entry->firstname, $p_body);
	$p_body = str_replace('*RACENAME', $race->name, $p_body);
	$p_body = str_replace('*RACENUMBER', $entry->racenumber, $p_body);
	$p_body = str_replace('*TOFIRSTNAME', htmlspecialchars($_REQUEST['firstname'], ENT_QUOTES), $p_body);
	$p_body = str_replace('*TOSURNAME', htmlspecialchars($_REQUEST['surname'], ENT_QUOTES), $p_body);
	$p_body = str_replace('*SITEURL', $currentSite->url, $p_body);
	$p_body = str_replace('*TRANSFERNO', htmlspecialchars($_REQUEST['transferno'], ENT_QUOTES), $p_body);
	$res = sendEMail($p_from, $p_to, $p_bcc, $p_subject, $p_body);
} elseif ($_REQUEST['change'] == '1') {
	$p_subject = $content->text;
	$p_subject = str_replace("*RACENAME", $race->name, $p_subject);
	$content = array_shift($contents);
	$p_body = $extrahtml. $content->text;
	$p_body = str_replace('*FIRSTNAME', $entry->firstname, $p_body);
	$p_body = str_replace('*RACENAME', $race->name, $p_body);
	$p_body = str_replace('*SITEURL', $currentSite->url, $p_body);
	$p_body = str_replace('*TRANSFERNO', htmlspecialchars($_REQUEST['transferno'], ENT_QUOTES), $p_body);
	$res = sendEMail($p_from, $p_to, $p_bcc, $p_subject, $p_body);
} elseif ($_REQUEST['change'] == '2') {
	$p_subject = $content->text;
	$p_subject = str_replace("*RACENAME", $race->name, $p_subject);
	$content = array_shift($contents);
	$p_body = $content->text;
	$p_body = $extrahtml. $p_body;
	$p_body = str_replace('*FIRSTNAME', $entry->firstname, $p_body);
	$p_body = str_replace('*RACENAME', $race->name, $p_body);
	$p_body = str_replace('*RACENUMBER', $entry->racenumber, $p_body);
	$p_body = str_replace('*SITEURL', $currentSite->url, $p_body);
	$p_body = str_replace('*TRANSFERNO', htmlspecialchars($_REQUEST['transferno'], ENT_QUOTES), $p_body);
	$res = sendEMail($p_from, $p_to, $p_bcc, $p_subject, $p_body);
}

redirect_to('transfer5.php?email='. $entry->email. '&change='. htmlspecialchars($_REQUEST['change'], ENT_QUOTES));
?>