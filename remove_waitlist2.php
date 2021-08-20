<?php
/* ************************************************************************
' remove_waitlist2.php -- Withdraw entry from waitlist #2 - Process and display confirmation
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

$url = $_SERVER['SCRIPT_NAME'];
$url = substr($url, strrpos($url, "/") + 1, strlen($url) - strrpos($url, "/"));

$contents = Content::findByURL($url, $cookieLanguageCode);
$content = array_shift($contents);

$entries = Entry::findByOrderNumber($_REQUEST['waitlist_code']);
$entry = array_shift($entries);

if ($entry) {
	$entry->waiting = 0;
	$entry->deleted = 1;
	$entry->save();

	// Release any offered entry as well
	$transfer = Transfer::findByOfferToForUpdate($entry->raceid, $entry->id);
	if ($transfer) {
		$transfer->offerready = 1;
		$transfer->offertoentryid = 0;
		$transfer->offereddate = 0;
		$transfer->save();
	}
}
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
			<div id="windowcontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<?php
						echo $content->text;	// <h2>Remove Waiting List Entry</h2>
						$content = array_shift($contents); ?><br /><br /><br />
					</div>
					<div class="floatright">
						<a href="#" onclick="window.close();" style="color:#000000; text-decoration:none"><?php echo $content->text; ?>&nbsp;<img src="images/close_icon.gif" width="8" height="8" alt="<?php echo $content->text; ?>" /></a>
					</div>
					<div class="clear"></div>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div> 
	</body>
</html>