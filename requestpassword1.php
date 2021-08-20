<?php
/* ************************************************************************
' requestpassword1.php -- Request Password #1
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

$p_emailerror = $content->text;
$content = array_shift($contents);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $currentSite->title; ?></title>
		<?php
		include(LIB_PATH. 'dojo.php'); ?>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script src="scripts/AC_RunActiveContent.js" type="text/javascript"></script>
		<script type="text/javascript">
			dojo.require("dojo.parser");
			dojo.require("dijit.form.TextBox");
	
			dojo.addOnLoad(function() {
				dijit.byId('email').focus();
			});
			
			function checkform() {
				missinginfo = "";
				if (document.form1.email.value == '') {
					missinginfo += "<?php echo $p_emailerror; ?>";
				}
				if (missinginfo != "") {
					alert(missinginfo);
				}	else { 
					document.form1.submit();
				}
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body class="tundra">
		<div id="pagecontainer">
			<div id="windowcontainer">
				<div class="windowcontentcolumn0">
					<div class="floatright">
						<a href="#" onclick="window.close();" style="color:#000000; text-decoration:none"><?php echo $content->text; ?>&nbsp;<img src="images/close_icon.gif" width="8" height="8" alt="<?php echo $content->text; ?>" /></a>
					</div>
					<div class="clear"></div>
					<form name="form1" in="form1" method="post" action="requestpassword2.php">
						<?php
						$content = array_shift($contents);
						echo $content->text;	// Enter your email address:
						$content = array_shift($contents); ?><br />
						<input name="email" id="email" type="text" style="width: 200px;" dojoType="dijit.form.TextBox" lowercase="true"><br /><br />
						<?php
						echo $content->text;	// Note: this should be ....etc
						$content = array_shift($contents); ?><br /><br />
						<div class="buttonblock">
							<div class="button"><a href="javascript:checkform();"><?php echo $content->text; ?></a></div>
						</div><br />
					</form>
				</div><br />
			</div>
		</div>
	</body>
</html>