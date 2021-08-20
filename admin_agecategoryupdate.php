<?php
/* ************************************************************************
' admin_agecategoryupdate.php -- Administration - Update an Age Category
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
'		betap 2008-06-29 16:02:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'agecategorytype.php');
require_once(LIB_PATH. 'agecategory.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$agecategorytype = AgeCategoryType::findById($_SESSION['agecategorytypeid']);
$agecategory = AgeCategory::findById($_REQUEST['id']);

if (!empty($_POST['submit'])) {

	$agecategory->code = $_POST['code'];
	$agecategory->maleonly = isset($_POST['maleonly']) ? 1 : 0;
	$agecategory->femaleonly = isset($_POST['femaleonly']) ? 1 : 0;
	$agecategory->lowervalue = $_POST['lowervalue'];
	$agecategory->uppervalue = $_POST['uppervalue'];
	$agecategory->save();

	redirect_to("admin_agecategorylist.php");
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
					missinginfo += "\nYou must enter an age category code";
				}
				// Value ranges are mandatory unless the basis is manual
				if (document.form1.agecategorytype_basis.value != 99) {
					if (document.form1.lowervalue.value == "") {
						missinginfo += "\nYou must enter a lower value";
					}
					if (document.form1.uppervalue.value == "") {
						missinginfo += "\nYou must enter a upper value";
					}
					if (!isNumeric(document.form1.lowervalue.value)) {
						missinginfo += "\nThe lower value must be numeric";
					}
					if (!isNumeric(document.form1.uppervalue.value)) {
						missinginfo += "\nThe upper value must be numeric";
					}
					if (missinginfo == "") {
						if (parseInt(document.form1.lowervalue.value) >= parseInt(document.form1.uppervalue.value)) {
							missinginfo += "\nThe upper value must be greater than the lower value";
						}
					}
					if (missinginfo == "") {
						if (document.form1.agecategorytype_basis.value == 3) {
							if (document.form1.lowervalue.value >= 0) {
								missinginfo += "\nThe lower year offset must be negative (leading sign)";
							}
							if (document.form1.uppervalue.value >= 0) {
								missinginfo += "\nThe upper year offset must be negative (leading sign)";
							}
						}
					}
				}
				if (missinginfo != "") {
					alert(missinginfo);
					return false;
				} else { 
					return true;
				}
			}
			function refreshyears() {
				if (isNumeric(document.form1.lowervalue.value) &&
					isNumeric(document.form1.uppervalue.value) &&
					isNumeric(document.form1.raceyear.value)) {
					document.getElementById("examples").style.display = '';
					MM_setTextOfLayer('fromyear', '', parseInt(document.form1.raceyear.value) + parseInt(document.form1.lowervalue.value));
					MM_setTextOfLayer('toyear', '', parseInt(document.form1.raceyear.value) + parseInt(document.form1.uppervalue.value));
				} else {
					document.getElementById("examples").style.display = 'none';
				}
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onLoad="self.focus();document.form1.code.focus();refreshyears();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Update Age Category</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_agecategorytypelist.php">age category type list</a>&nbsp;&gt;&nbsp;
						<a href="admin_agecategorylist.php">age category list</a>&nbsp;&gt;&nbsp;
						update age category<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form1" method="post" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Age Category Code:</div>
						<div class="formfield"><input type="text" name="code" size="10" value="<?php echo $agecategory->code; ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Male Only?:</div>
						<div class="formfield"><input type="checkbox" name="maleonly"<?php if ($agecategory->maleonly) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Female Only?:</div>
						<div class="formfield"><input type="checkbox" name="femaleonly"<?php if ($agecategory->femaleonly) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Lower <?php echo $agecategorytype->basisName(); ?>:</div>
						<div class="formfield"><input type="text" name="lowervalue" value="<?php echo $agecategory->lowervalue; ?>" size="10" onChange="refreshyears();" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Upper <?php echo $agecategorytype->basisName(); ?>:</div>
						<div class="formfield"><input type="text" name="uppervalue" value="<?php echo $agecategory->uppervalue; ?>" size="10" onChange="refreshyears();" /></div>
						<?php
						if ($agecategorytype->basis == 3) { ?>
							<div class="clear"></div><br />
							<div class="formcaption">Example Race Year:</div>
							<div class="formfield"><input type="text" name="raceyear" size="10" value="<?php echo date("Y"); ?>" onChange="refreshyears();" /><span id="examples" style="display: none;">&nbsp;From:&nbsp;<span id="fromyear"></span>&nbsp;To:&nbsp;<span id="toyear"></span></span></div>
						<?php
						} ?>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Confirm Update" />
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />&nbsp;&nbsp;
						<input type="hidden" name="id" value="<?php echo $agecategory->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>