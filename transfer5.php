<?php
/* ************************************************************************
' transfer5.php -- Transfer/Change a race entry #5 - Show confirmation
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body>
		<div id="pagecontainer">
			<div id="windowcontainer">
				<div class="windowcontentcolumn0">
					<div class="floatright">
						<a href="#" onclick="window.close();" style="color:#000000; text-decoration:none"><?php echo $content->text; ?>&nbsp;<img src="images/close_icon.gif" width="8" height="8" alt="<?php echo $content->text; ?>" /></a>
					</div>
					<div class="floatleft">
						<?php
						$content = array_shift($contents);
						$p_transferheading = $content->text;
						$content = array_shift($contents);
						$p_transfernoemail = $content->text;
						$content = array_shift($contents);
						$p_transferemail = $content->text;
						$content = array_shift($contents);
						$p_changeheading = $content->text;
						$content = array_shift($contents);
						$p_changenoemail = $content->text;
						$content = array_shift($contents);
						$p_changeemail = $content->text;
						$content = array_shift($contents);
						$p_offerheading = $content->text;
						$content = array_shift($contents);
						$p_offernoemail = $content->text;
						$content = array_shift($contents);
						$p_offeremail = $content->text;
						$content = array_shift($contents);
						$p_link = $content->text;
						if ($_REQUEST['change'] == '0') {
							echo $p_transferheading;
							if ($_REQUEST['email'] == '*NONE') {
								echo $p_transfernoemail;
							} else {
								echo str_replace('*EMAIL', htmlspecialchars($_REQUEST['email'], ENT_QUOTES), $p_transferemail);
							}
						} elseif ($_REQUEST['change'] == '1') {
							echo $p_changeheading;
							if ($_REQUEST['email'] == '*NONE') {
								echo $p_changenoemail;
							} else {
								echo str_replace('*EMAIL', htmlspecialchars($_REQUEST['email'], ENT_QUOTES), $p_changeemail);
							}
						} elseif ($_REQUEST['change'] == '2') {
							echo $p_offerheading;
							if ($_REQUEST['email'] == '*NONE') {
								echo $p_offernoemail;
							} else {
								echo str_replace('*EMAIL', htmlspecialchars($_REQUEST['email'], ENT_QUOTES), $p_offeremail);
							}
						}
						if ($_REQUEST['change'] == '0') { 
							$p_url = 'confirm_transfer1.php';
						} elseif ($_REQUEST['change'] == '1') {
							$p_url = 'confirm_change1.php';
						} else {
							$p_url = 'confirm_offer1.php';
						}
						echo str_replace('*CONFIRMURL', $p_url, $p_link); ?>
					</div><br /><br /><br /><br /><br /><br />
				</div>
				<?php
				include(LIB_PATH. "inc_windowfooter.php"); ?>
			</div>
		</div>
	</body>
</html>