<?php
/* ************************************************************************
' remove_waitlist1.php -- Withdraw from waitlist #1 - Accept and validate waitlist ID
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

if (!empty($_REQUEST['waitlist_code'])) {
	$entries = Entry::findByOrderNumber($_REQUEST['waitlist_code']);
	$entry = array_shift($entries);
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

	<?php
	$onloadjs = '';
	if (empty($_REQUEST['waitlist_code'])) {
		$onloadjs = 'self.focus();document.form1.waitlist_code.focus();';
	} ?>

	<body onload="<?php echo $onloadjs; ?>">
		<div id="pagecontainer">
			<div id="windowcontainer">
				<div class="windowcontentcolumn0">
					<?php
					echo $content->text;	// <h2>Remove Waiting List Entry</h2>
					$content = array_shift($contents);
					$p_notvalid = $content->text;
					$content = array_shift($contents);
					$p_alreadyproc = $content->text;
					$content = array_shift($contents);
					$p_confirm = $content->text;
					$content = array_shift($contents);
					$p_confirmbutton = $content->text;
					$content = array_shift($contents);
					$p_waitcode = $content->text;
					$content = array_shift($contents);
					$p_submitbutton = $content->text;
					if (!empty($_REQUEST['waitlist_code'])) {
						if (!$entry) {
							echo $p_notvalid;
						} else {
							if ($entry->paid || $entry->deleted) {
								echo $p_alreadyproc;
							} else {
								echo $p_confirm; ?>
								<form name="form2" method="post" action="remove_waitlist2.php">
									<input name="waitlist_code" type="hidden" id="waitlist_code" value="<?php echo $entry->ordernumber; ?>" />
									<div class="buttonblock">
										<div class="button"><a href="javascript:document.form2.submit();"><?php echo $p_confirmbutton; ?></a></div>
									</div>
								</form><br />
							<?php
							}
						}
					} else { ?>
						<div class="clear"></div><br /><br />
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<div class="formcaption"><?php echo $p_waitcode; ?></div>
							<div class="formfield"><input name="waitlist_code" type="text" id="waitlist_code" /></div>
							<div class="clear"></div><br /><br />
							<div class="buttonblock">
								<div class="button"><a href="javascript:document.form1.submit();"><?php echo $p_submitbutton; ?></a></div>
							</div>
						</form>
					<?php
					} ?>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>