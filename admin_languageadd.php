<?php
/* ************************************************************************
' admin_languageadd.php -- Add a language
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
'		betap 2008-06-29 16:33:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'language.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$p_errorMessage = "";
if (!empty($_POST['submit'])) {

	// Check if the code already exists
	$chklanguage = Language::findByCode($_POST['code']);
	$p_errorMessage = "";
	if (!empty($chklanguage)) {
		$p_errorMessage = "This language/locale code already exists";
	} else {

		$language = new Language();
		$language->code = $_POST['code'];
		$language->name = $_POST['name'];
		$language->save();

		redirect_to('admin_languagelist.php');
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript">
			function checkme() {
				missinginfo = "";
				if (document.form1.code.value == "") {
					missinginfo += "\nYou must enter a language code";
				}
				if (document.form1.name.value == "") {
					missinginfo += "\nYou must enter a language name";
				}
				if (missinginfo != "") {
					alert(missinginfo);
					return false;
				} else { 
					return true;
				}
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onload="self.focus();document.form1.code.focus()">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Add Language/Locale</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_languagelist.php">language/locale list</a>&nbsp;&gt;&nbsp;
						add language/locale<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form method="post" name="form1" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Language/Locale Code:</div>
						<div class="formfield"><input type="text" name="code" maxlength="5" style="width: 50px;" value="<?php if (isset($_REQUEST["code"])) echo $_REQUEST["code"]; ?>" /></div>&nbsp;<?php echo '<span class="stylered">'. $p_errorMessage, '</span>'; ?>
						<div class="clear"></div><br />
						<div class="formcaption">Language/Locale Name:</div>
						<div class="formfield"><input type="text" name="name" maxlength="50" style="width: 200px;" value="<?php if (isset($_REQUEST["name"]))echo $_REQUEST["name"]; ?>" /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Add Language/Locale" />&nbsp;&nbsp;
						<a href="admin_languagelist.php"><input type="button" name="submit2" value="Cancel (back)" /></a>
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>