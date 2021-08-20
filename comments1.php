<?php
/* ************************************************************************
' comments1.php -- Post race comments and feedback
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
'		betap 2008-09-05 12:24:22
'		New script.
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
		<script type="text/javascript">
			function checkform() {
				missinginfo = "";
				if (document.form1.name.value == "") {
					missinginfo += "\n<?php echo $content->text; ?>";
					<?php
					$content = array_shift($contents); ?>
				} 
				if (!((document.form1.email.value.indexOf(".") > 0) && (document.form1.email.value.indexOf("@") > 0))) {
					missinginfo += "\n<?php echo $content->text; ?>";
					<?php
					$content = array_shift($contents); ?>
				} 
				if (document.form1.text.value == "") {
					missinginfo += "\n<?php echo $content->text; ?>";
					<?php
					$content = array_shift($contents); ?>
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

	<body onload="self.focus();document.form1.name.focus();">
	 
		<div id="pagecontainer">
			<div id="windowcontainer">
				<div class="windowcontentcolumn0">
					<form action="comments2.php" method="post" name="form1">
						<div class="floatright">
							<a href="#" onclick="window.close();" style="color:#000000; text-decoration:none"><?php echo $content->text; ?>&nbsp;<img src="images/close_icon.gif" width="8" height="8" alt="<?php echo $content->text; ?>" /></a>
						</div>
						<div class="clear"></div>
						<?php
						$content = array_shift($contents);
						echo $content->text;
						$content = array_shift($contents); ?>
						<div class="formcaption">
							<?php
							echo $content->text;
							$content = array_shift($contents); ?>
						</div>
						<div class="formfield"><input name="name" type="text" id="name" style="width: 300px;" /></div>
						<div class="clear"></div>
						<div class="formcaption">
							<?php
							echo $content->text;
							$content = array_shift($contents); ?>
						</div>
						<div class="formfield"><input name="email" type="text" id="email" style="width: 300px;" />&nbsp;
							<?php
							echo $content->text;
							$content = array_shift($contents); ?>
						</div>
						<div class="clear"></div><br />
						<div class="formcaption">
							<?php
							echo $content->text;
							$content = array_shift($contents); ?>
						</div>
						<div class="formfield"><textarea name="text" cols="50" rows="7" id="text"></textarea></div>
						<div class="clear"></div><br /><br />
						<div class="buttonblock">
							<div class="button"><a href="javascript:checkform();"><?php echo $content->text; ?></a></div>
						</div>
						<input type="hidden" name="raceid" value="<?php echo htmlspecialchars($_REQUEST['raceid'], ENT_QUOTES); ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_windowfooter.php"); ?>
			</div>
		</div>
	</body>
</html>