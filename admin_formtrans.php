<?php
/* ************************************************************************
' admin_formtrans.php -- Administration - Translate a Form
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
'		betap 2008-06-27 20:39:23
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'form.php');
require_once(LIB_PATH. 'formlang.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (isset($_REQUEST['form2']) && $_REQUEST['form2'] == "form2") {
	$_SESSION['languagecode'] = $_REQUEST['languagecode'];
}

$formlang = FormLang::findById($_REQUEST['id'], $_SESSION['languagecode']);

if (!empty($_POST['submit'])) {
	if (!$formlang) {
		$formlang = new FormLang();
	}
	$formlang->formid = $_POST['id'];
	$formlang->languagecode = $_SESSION['languagecode'];
	$formlang->name = $_POST['name'];
	$formlang->save();

	redirect_to("admin_formlist.php");
}

$languages = Language::findAll();
$form = Form::findById($_REQUEST['id']);

$p_name = $form->name;

if (!empty($formlang)) {
	$p_name = $formlang->name;
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
				if (document.form1.name.value == "") {
					missinginfo += "\nYou must enter a form name";
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

	<body onload="self.focus();document.form1.name.focus()">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Translate Form</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_formlist.php">form list</a>&nbsp;&gt;&nbsp;
						translate form<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form2" id="langform2" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Language Name:</div>
						<div class="formfield">
							<select name="languagecode" id="languagecode" onchange="document.form2.submit();">
								<?php
								foreach ($languages as $language) {
									if ($language->code != $currentSite->languagecode) {
										echo '<option value="'. $language->code. '"';
										if (empty($_SESSION['languagecode'])) {
											$_SESSION['languagecode'] = $language->code;
										}
										if ($_SESSION['languagecode'] == $language->code) {
											echo ' selected="selected"';
										}
										echo '>'. $language->name. '</option>';
									}
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<input type="hidden" name="form2" value="form2" />
						<input type="hidden" name="id" value="<?php echo $form->id; ?>" />
					</form>
					<form name="form1" method="post" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Form Name:</div>
						<div class="formfield"><input type="text" name="name" maxlength="50" style="width: 200px;" value="<?php echo $p_name; ?>" /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Add/Update" />
						<a href="admin_formlist.php"><input type="button" name="submit2" value="Cancel (back)" /></a>
						<input type="hidden" name="id" value="<?php echo $form->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>