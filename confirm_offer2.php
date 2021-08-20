<?php
/* ************************************************************************
' confirm_offer2.php -- Confirm offer #2 - Perform update & return offer complete
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
require_once(LIB_PATH. "content.php");

if (!defined('DB_SERVER') || $currentSite->downformaint && !$session->isLoggedIn()) {
	redirect_to("downformaint.php");
}

$url = $_SERVER['SCRIPT_NAME'];
$url = substr($url, strrpos($url, "/") + 1, strlen($url) - strrpos($url, "/"));

$contents = Content::findByURL($url, $cookieLanguageCode);
$content = array_shift($contents);

$transfer = Transfer::findByCode($_REQUEST['offerid']);
$entryid = $transfer->entryid;
$transfer->offerready = 1;
$transfer->save();

$entry = Entry::findById($entryid);
$race = Race::findById($entry->raceid);

// Send a confirmation email
$p_from = $race->emailfrom;
$p_to = $entry->email;
if (strtoupper($race->emailbcc) != '*NONE') {
	$p_bcc = $race->emailbcc;
} else {
	$p_bcc = $currentSite->entrybcc;
}

$emailcontents = Content::findByURL('emailofferconfirmed', $cookieLanguageCode);
$emailcontent = array_shift($emailcontents);
$p_subject = $emailcontent->text;
$p_subject = str_replace('*RACENAME', $race->name, $p_subject);
$emailcontent = array_shift($emailcontents);
$p_body = $emailcontent->text;
$p_body = str_replace('*FIRSTNAME', $entry->firstname, $p_body);
$p_body = str_replace('*RACENAME', $race->name, $p_body);
$p_body = str_replace('*RACENUMBER', $entry->racenumber, $p_body);
$p_body = str_replace('*SITEURL', $currentSite->url, $p_body);
$p_body = str_replace('*OFFERID', htmlspecialchars($_REQUEST['offerid'], ENT_QUOTES), $p_body);
$res = sendEMail($p_from, $p_to, $p_bcc, $p_subject, $p_body);

include(LIB_PATH. 'inc_matchwaitlist.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body>
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					echo $content->text; ?>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>