<?php
/* ************************************************************************
' requestpassword2.php -- Request Password #2
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
'		betap 2008-07-01 09:00:00
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
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
					<div class="clear"></div>
					<?php
					$content = array_shift($contents);
					$p_emailerror = $content->text;
					$content = array_shift($contents);
					$p_emailok = $content->text;

					$entrant = Entrant::findByEmailWithUsername($_REQUEST['email']);
					if (!$entrant) {
						echo $p_emailerror;
					} else {
						$p_ref = createRandomCode(15);
						$entrant->passwordref = $p_ref;
						$entrant->save();

						echo $p_emailok;
						echo '<strong>'. htmlspecialchars($_REQUEST['email'], ENT_QUOTES). '</strong><br /><br />';

						// Get the 'from' email address from the any race associated with the current site
						$p_from = $currentSite->emailfrom;
						if (strtoupper($p_from) == "*NONE") {
							$races = Race::findAllForSite($currentSite->id);
							$race = array_shift($races);
							$p_from = $race->emailfrom;
						}
						$p_to = htmlspecialchars($_REQUEST['email'], ENT_QUOTES);
						$p_bcc = $currentSite->entrybcc;

						// Get email content
						$contents = Content::findByURL('emailreqpwd', $cookieLanguageCode);
						$content = array_shift($contents);
						$p_subject = $content->text;
						$p_subject = str_replace('*SITETITLE', $currentSite->title, $p_subject);
						$content = array_shift($contents);
						$p_body = $content->text;
						$p_body = str_replace('*FIRSTNAME', $entrant->firstname, $p_body);
						$p_body = str_replace('*SITETITLE', $currentSite->title, $p_body);
						$p_body = str_replace('*SITEURL', $currentSite->url, $p_body);
						$p_body = str_replace('*CODE', $p_ref, $p_body);
						$res = sendEMail($p_from, $p_to, $p_bcc, $p_subject, $p_body);
					} ?>
				</div>
			</div>
		</div>
	</body>
</html>