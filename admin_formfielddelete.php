<?php
/* ************************************************************************
' admin_formfielddelete.php -- Administration - Delete Form Field
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

$formfield = FormField::findById($_REQUEST['id']);

if (!empty($_POST['submit'])) {

	$formfield->delete();
	
	redirect_to("admin_formfieldlist.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript">
			function toggleFields() {
				// Only show memo box for checkboxes, memos and textboxes
				if ('<?php echo $formfield->type; ?>' == 'checkbox' ||
					'<?php echo $formfield->type; ?>' == 'memo' ||
					'<?php echo $formfield->type; ?>' == 'text') {
					document.getElementById('memodisplay').style.display = '';
				} else {
					document.getElementById('memodisplay').style.display = 'none';
				}
				// Don't show column for section headings
				if ('<?php echo $formfield->type; ?>' == 'section') {
					document.getElementById('columndisplay').style.display = 'none';
				} else {
					document.getElementById('columndisplay').style.display = '';
				}
				// Don't show caption for races
				if ('<?php echo $formfield->type; ?>' == 'races') {
					document.getElementById('captiondisplay').style.display = 'none';
				} else {
					document.getElementById('captiondisplay').style.display = '';
				}
				// Don't show values for radio buttons
				if ('<?php echo $formfield->type; ?>' == 'radio') {
					document.getElementById('valuesdisplay').style.display = '';
				} else {
					document.getElementById('valuesdisplay').style.display = 'none';
				}
				// Only show dbname & mandatory for checkboxes, date, datetime, radio, sum and textboxes
				if ('<?php echo $formfield->type; ?>' == 'checkbox' ||
					'<?php echo $formfield->type; ?>' == 'date' ||
					'<?php echo $formfield->type; ?>' == 'datetime' ||
					'<?php echo $formfield->type; ?>' == 'radio' ||
					'<?php echo $formfield->type; ?>' == 'sum' ||
					'<?php echo $formfield->type; ?>' == 'text') {
					document.getElementById('dbnamedisplay').style.display = '';
					document.getElementById('mandatorydisplay').style.display = '';
				} else {
					document.getElementById('dbnamedisplay').style.display = 'none';
					document.getElementById('mandatorydisplay').style.display = 'none';
				}
				// Only show width and format for textboxes
				if ('<?php echo $formfield->type; ?>' == 'text') {
					document.getElementById('widthdisplay').style.display = '';
					document.getElementById('formatdisplay').style.display = '';
				} else {
					document.getElementById('widthdisplay').style.display = 'none';
					document.getElementById('formatdisplay').style.display = 'none';
				}
			}
		</script>
		<script type="text/javascript" src="scripts/general.js"></script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onload="toggleFields();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Delete Form Field</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_formlist.php">page list</a>&nbsp;&gt;&nbsp;
						<a href="admin_formfieldlist.php">form field list</a>&nbsp;&gt;&nbsp;
						delete form field<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					Are you absolutely sure you want to delete the following form field?<br /><br />
						<div class="formcaption">Sequence:</div>
						<div class="formfield"><strong><?php echo $formfield->sequence; ?></strong></div>
						<div class="clear"></div>
						<div class="formcaption">Type:</div>
						<div class="formfield"><strong><?php echo FormField::$types[$formfield->type]; ?></strong></div>
						<div class="clear"></div>
						<div id="columndisplay">
							<div class="formcaption">Column:</div>
							<div class="formfield"><strong><?php echo FormField::$columns[$formfield->column]; ?></strong></div>
							<div class="clear"></div>
						</div>
						<div class="formcaption">Show on Form?:</div>
						<div class="formfield"></strong><?php echo $formfield->show ? "Yes" : "No"; ?></strong></div>
						<div class="clear"></div>
						<div id="captiondisplay">
							<div class="formcaption">Caption:</div>
							<div class="formfield"><strong><?php echo $formfield->caption; ?></strong></div>
							<div class="clear"></div>
						</div>
						<div id="memodisplay">
							<div class="formcaption">Memo:</div>
							<div class="clear"></div>
							<strong><?php echo $formfield->memo; ?></strong>
							<div class="clear"></div>
						</div>
						<div id="controldisplay">
							<div class="formcaption">Control:</div>
							<div class="formfield"><strong><?php echo FormField::$controls[$formfield->control]; ?></strong></div>
							<div class="clear"></div>
						</div>
						<div id="valuesdisplay">
							<div class="formcaption">Values:</div>
							<div class="formfield"><strong><?php echo $formfield->values; ?></strong></div>
							<div class="clear"></div>
						</div>
						<div id="dbnamedisplay">
							<div class="formcaption">Database Name:</div>
							<div class="formfield"><strong><?php echo $formfield->dbname; ?></strong></div>
							<div class="clear"></div>
						</div>
						<div id="mandatorydisplay">
							<div class="formcaption">Mandatory?:</div>
							<div class="formfield"><strong><?php echo $formfield->mandatory ? "Yes" : "No"; ?></strong></div>
							<div class="clear"></div>
						</div>
						<div id="widthdisplay">
							<div class="formcaption">Width (pixels):</div>
							<div class="formfield"><strong><?php echo $formfield->width; ?></strong></div>
							<div class="clear"></div>
						</div>
						<div id="formatdisplay">
							<div class="formcaption" id="format">Formatting:</div>
							<div class="formfield"><strong><?php echo FormField::$formats[$formfield->format]; ?></strong></div>
						</div>
					<div class="clear"></div><br /><br /><br />
					<form method="post" name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<input type="submit" name="submit" value="Confirm Delete" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
						<input type="hidden" name="id" value="<?php echo $formfield->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>