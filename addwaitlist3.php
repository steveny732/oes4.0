<?php
/* ************************************************************************
' admin_addwaitlist3.php -- Add a waiting list entry #3
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
' Version: 1.0
' Last Modified By:
'  betap 2008-07-21 07:49:13
'   Exclude deleted entries from duplicate check.
'  betap 2008-07-16 09:29:45
'   Make entry_total numeric.
'  betap 2008-07-10 13:31:27
'   Remove erroneous yetToApplyOnlineFee (wrongly copied from enter3.php checking).
'  betap 2008-06-29 15:40:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once("includes/initialise.php");
require_once(LIB_PATH. "content.php");

if (!defined('DB_SERVER') || $currentSite->downformaint && !$session->isLoggedIn()) {
	redirect_to("downformaint.php");
}

$p_waitingListEntry = true;
$p_adminEntry = false;
$p_processasp = "";

// Race list is not open for selection, so automatically process the only race shown
$p_raceSelected = true;

include(LIB_PATH. 'inc_addentry.php');

// If an entry was made (if an available preallocated number existed for the entrant's club), skip the
// waiting list stuff and confirm the entry instead
if ($p_waitingListEntry) {
	// Get wait list email content
	$contents = Content::findByURL('emailwlconfirmed', $cookieLanguageCode);
	$content = array_shift($contents);
	
	// Send an email
	if (strtoupper($p_params['email']) == '*NONE') {
		$extrahtml = 'Email not available - Confirmation needed - CALL '. $p_params['telday']. ' (day) '. $p_params['televe']. ' (evening)<br /><br />';
	} else {
		$extrahtml = '';
	}
	$p_from = $race->emailfrom;
	$p_to = $p_params['email'];
	if (strtoupper($race->emailbcc) != '*NONE') {
		$p_bcc = $race->emailbcc;
	} else {
		$p_bcc = $currentSite->entrybcc;
	}
	$p_subject = $content->text;
	$p_subject = str_replace('*RACENAME', $race->name, $p_subject);
	$content = array_shift($contents);
	$p_body = $content->text;
	$p_body = $extrahtml. $p_body;
	$p_body = str_replace('*FIRSTNAME', $p_params['firstname'], $p_body);
	$p_body = str_replace('*RACENAME', $race->name, $p_body);
	$p_body = str_replace('*SITEURL', $currentSite->url, $p_body);
	$p_body = str_replace('*ORDERNO',$p_params['ordernumber'], $p_body);
	$res = sendEMail($p_from, $p_to, $p_bcc, $p_subject, $p_body);
	
	include(LIB_PATH. 'inc_matchwaitlist.php');
	redirect_to('addwaitlist4.php?raceid='. $p_raceid);

} else {
	$_SESSION['ordernumber'] = htmlspecialchars($_REQUEST['ordernumber'], ENT_QUOTES);
	redirect_to('enter4.php');
} ?>