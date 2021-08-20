<?php
/* ************************************************************************
' admin_formfieldadd.php -- Administration - Add Form Field
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
'		betap 2008-06-29 16:39:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'formfield.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (!empty($_POST['submit'])) {

	$p_column = $_POST['column'];
	if ($_POST['type'] == 'section') {
		if ($p_column == '0') {
			$p_column = "";
		}
	}

	$minval = $_POST['minval'];
	$maxval = $_POST['maxval'];
	if (!empty($_POST['dbname'])) {
		$dbcolumn = DbColumn::findByName($_POST['dbname']);
		if ($dbcolumn->type == 3 || $dbcolumn->type == 6) {
			if (empty($_POST['minval'])) {
				$minval = "0";
			}
			if (empty($_POST['maxval'])) {
				$maxval = "0";
			}
		}
	}
	$formfield = new FormField();
	$formfield->formid = $_SESSION['formid'];
	$formfield->sequence = $_POST['sequence'];
	$formfield->column = $p_column;
	$formfield->type = $_POST['type'];
	$formfield->show = isset($_POST['show']) ? 1 : 0;
	$formfield->caption = $_POST['caption'];
	$formfield->memo = $_POST['type'] == 'checkbox' ? $_POST['plainmemo'] : $_POST['memo'];
	$formfield->control = $_POST['control'];
	$formfield->values = $_POST['values'];
	$formfield->dbname = $_POST['dbname'];
	$formfield->width = $_POST['width'];
	$formfield->mandatory = isset($_POST['mandatory']) ? 1 : 0;
	$formfield->format = $_POST['format'];
	$formfield->minval = $minval;
	$formfield->maxval = $maxval;
	$formfield->save();

	redirect_to("admin_formfieldlist.php");
}

$dbcolumns = DbColumn::findAll();
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
				if (document.form1.sequence.value == "") {
					missinginfo += "\nYou must enter a sequence";
				}
				if (!isNumeric(document.form1.sequence.value)) {
					missinginfo += "\nSequence must be numeric";
				}
				if (document.form1.type.value == "") {
					missinginfo += "\nYou must select a formfield type";
				}
				if (document.form1.minval.value != "" && (isNaN(document.form1.minval.value) || !isNumeric(document.form1.minval.value)) ||
					document.form1.minval.value != "" && (isNaN(document.form1.maxval.value) || !isNumeric(document.form1.maxval.value))) {
					missinginfo += "\nMinimum and maximum values are only allowed for numeric database columns so they must also be numeric";
				}
				if (document.form1.minval.value > document.form1.maxval.value) {
					missinginfo += "\nThe maximum value must be greater than the minimum value";
				}
				if (missinginfo != "") {
					alert(missinginfo);
					return false;
				} else { 
					return true;
				}
			}
			function toggleFields() {
				// Only show memo box for checkboxes, memos and textboxes
				if (document.form1.type.selectedIndex == 1 ||
					document.form1.type.selectedIndex == 4 ||
					document.form1.type.selectedIndex == 9) {
					// For checkboxes, the memo field is actually plain text (i.e. not html)
					if (document.form1.type.selectedIndex == 1) {
						document.getElementById('memodisplay').style.display = 'none';
						document.getElementById('memoplaindisplay').style.display = '';
					} else {
						document.getElementById('memodisplay').style.display = '';
						document.getElementById('memoplaindisplay').style.display = 'none';
					}
				} else {
					document.getElementById('memodisplay').style.display = 'none';
					document.getElementById('memoplaindisplay').style.display = 'none';
				}
				// Don't show column for section headings
				if (document.form1.type.selectedIndex == 7) {
					document.getElementById('columndisplay').style.display = 'none';
				} else {
					document.getElementById('columndisplay').style.display = '';
				}
				// Don't show caption for races
				if (document.form1.type.selectedIndex == 5) {
					document.getElementById('captiondisplay').style.display = 'none';
				} else {
					document.getElementById('captiondisplay').style.display = '';
				}
				// Don't show values for radio buttons
				if (document.form1.type.selectedIndex == 6) {
					document.getElementById('valuesdisplay').style.display = '';
				} else {
					document.getElementById('valuesdisplay').style.display = 'none';
				}
				// Only show dbname & mandatory for sum, checkboxes, date, datetime, radio and textboxes
				if (document.form1.type.selectedIndex == 0 ||
					document.form1.type.selectedIndex == 1 ||
					document.form1.type.selectedIndex == 2 ||
					document.form1.type.selectedIndex == 3 ||
					document.form1.type.selectedIndex == 6 ||
					document.form1.type.selectedIndex == 9) {
					document.getElementById('dbnamedisplay').style.display = '';
					document.getElementById('mandatorydisplay').style.display = '';
				} else {
					document.getElementById('dbnamedisplay').style.display = 'none';
					document.getElementById('mandatorydisplay').style.display = 'none';
				}
				// Only show width and format for textboxes
				if (document.form1.type.selectedIndex == 9) {
					document.getElementById('widthdisplay').style.display = '';
					document.getElementById('formatdisplay').style.display = '';
				} else {
					document.getElementById('widthdisplay').style.display = 'none';
					document.getElementById('formatdisplay').style.display = 'none';
				}
			}
		</script>
		<script type="text/javascript" src="scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript">
			tinyMCE.init({
				mode : "textareas",
				elements : "ajaxfilemanager",
				theme : "advanced",
				plugins : "advimage,advlink,media,contextmenu",
				file_browser_callback : "ajaxfilemanager",
				content_css : "css/styles.css",
				width : "700px",
				height : "400px",
				relative_urls : true
			});
			function ajaxfilemanager(field_name, url, type, win) {
				var ajaxfilemanagerurl = "../../../../jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php";
				switch (type) {
					case "image":
						break;
					case "media":
						break;
					case "flash": 
						break;
					case "file":
						break;
					default:
						return false;
				}
				tinyMCE.activeEditor.windowManager.open({
						url: "../../../../jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php",
						width: 782,
						height: 440,
						inline : "yes",
						close_previous : "no"
				},{
						window : win,
						input : field_name
				});
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onload="self.focus();document.form1.sequence.focus();toggleFields();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Add Form Field</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_formlist.php">form list</a>&nbsp;&gt;&nbsp;
						<a href="admin_formfieldlist.php">form field list</a>&nbsp;&gt;&nbsp;
						add form field<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form method="post" name="form1" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Sequence:</div>
						<div class="formfield"><input type="text" name="sequence" id="sequence" size="10" /></div>
						<div class="clear"></div>
						<div class="formcaption">Type:</div>
						<div class="formfield">
							<select name="type" id="type" onchange="toggleFields();">
								<?php
								foreach (FormField::$types as $key => $value) { ?>
									<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<div id="columndisplay">
							<div class="formcaption">Column:</div>
							<div class="formfield">
								<select name="column">
									<?php
									foreach (FormField::$columns as $key => $value) { ?>
										<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
									<?php
									} ?>
								</select>
							</div>
							<div class="clear"></div>
						</div>
						<div class="formcaption">Show on Form?:</div>
						<div class="formfield"><input type="checkbox" name="show" id="show" /></div>
						<div class="clear"></div>
						<div id="captiondisplay">
							<div class="formcaption">Caption:</div>
							<div class="formfield"><input type="text" name="caption" id="caption" maxlength="100" style="width: 200px;" /></div>
							<div class="clear"></div>
						</div>
						<div id="memodisplay" style="display: none;">
							<div class="formcaption">Memo:</div>
							<div class="clear"></div>
							<textarea name="memo" id="memo"></textarea>
							<div class="clear"></div>
						</div>
						<div id="memoplaindisplay" style="display: none;">
							<div class="formcaption">Memo:</div>
							<div class="formfield"><input type="text" name="plainmemo" id="plainmemo" style="width: 400px;" /></div>
							<div class="clear"></div>
						</div>
						<div id="controldisplay">
							<div class="formcaption">Control:</div>
							<div class="formfield">
								<select name="control">
									<?php
									foreach (FormField::$controls as $key => $value) { ?>
										<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
									<?php
									} ?>
								</select>
								<div class="clear"></div>
							</div>
						</div>
						<div id="valuesdisplay">
							<div class="formcaption">Values:</div>
							<div class="formfield"><input name="values" maxlength="400" style="width: 300px;" /></div>
							<div class="clear"></div>
						</div>
						<div id="dbnamedisplay">
							<div class="formcaption">Database Name:</div>
							<div class="formfield">
								<select name="dbname">
									<?php
									foreach (FormField::$specialdbcolumns as $key => $value) { ?>
										<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
									<?php
									}
									foreach ($dbcolumns as $dbcolumn) {
										echo '<option value="'. $dbcolumn->name. '">'. $dbcolumn->name. '</option>';
									} ?>
								</select>
							</div>
							<div class="clear"></div>
						</div>
						<div id="mandatorydisplay">
							<div class="formcaption">Mandatory?:</div>
							<div class="formfield"><input type="checkbox" name="mandatory" /></div>
							<div class="clear"></div>
						</div>
						<div id="widthdisplay">
							<div class="formcaption">Width (pixels):</div>
							<div class="formfield"><input name="width" maxlength="5" style="width: 30px;" /></div>
							<div class="clear"></div>
						</div>
						<div id="formatdisplay">
							<div class="formcaption" id="format">Formatting:</div>
							<div class="formfield">
								<select name="format">
									<?php
									foreach (FormField::$formats as $key => $value) { ?>
										<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
									<?php
									} ?>
								</select>
							</div>
							<div class="clear"></div>
							<div class="formcaption" id="format">Minimum Value:</div>
							<div class="formfield">
								<input name="minval" maxlength="10" style="width: 30px;" />
							</div>
							<div class="clear"></div>
							<div class="formcaption" id="format">Maximum Value:</div>
							<div class="formfield">
								<input name="maxval" maxlength="10" style="width: 30px;" />
							</div>
						</div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Insert Record" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>