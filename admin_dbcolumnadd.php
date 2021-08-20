<?php
/* ************************************************************************
' admin_dbcolumnadd.php -- Administration - Add Database Column
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
require_once(LIB_PATH. 'dbcolumn.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (!empty($_POST['submit'])) {

	// Database column name must not already exist	
	$chkdbcolumn = DbColumn::findByName($_POST['name']);
	$p_errorMessage = "";
	if (DbColumn::findByName($_POST['name'])) {
		$p_errorMessage = "This database column already exists";
	} else {

		$dbcolumn = new DbColumn();
		$dbcolumn->dbgroupid = $_SESSION['dbgroupid'];
		$dbcolumn->name = $_POST['name'];
		$dbcolumn->description = $_POST['description'];
		$dbcolumn->type = $_POST['type'];
		$dbcolumn->length = $_POST['length'];
		$dbcolumn->dbupdated = 0;
		$dbcolumn->entryrel = $_POST['entryrel'] ? 1 : 0;
		$dbcolumn->entrantrel = $_POST['entrantrel'] ? 1 : 0;
		$dbcolumn->transferrel = $_POST['transferrel'] ? 1 : 0;
		$dbcolumn->transferpersist = $_POST['transferpersist'] ? 1 : 0;
		$dbcolumn->changepersist = $_POST['changepersist'] ? 1 : 0;
		$dbcolumn->save();

		redirect_to("admin_dbcolumnlist.php");
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
				if (document.form1.name.value == "") {
					missinginfo += "\nYou must select a database column name";
				}
				if (document.form1.description.value == "") {
					missinginfo += "\nYou must select the database column description";
				}
				if (document.form1.type.value == 202) {
					if (document.form1.length.value == "") {
						missinginfo += "\nYou must enter a text length";
					}
					if (!isNumeric(document.form1.length.value)) {
						missinginfo += "\nText length must be numeric";
					}
				}
				if (missinginfo != "") {
					alert(missinginfo);
					return false;
				} else { 
					return true;
				}
			}
			function toggleLength() {
				if (document.form1.type.value == 202) {
					document.getElementById('hidelength').style.display = '';
				} else {
					document.getElementById('hidelength').style.display = 'none';
				}
			}
			function toggleTransfer() {
				if (document.form1.transferrel.checked) {
					document.getElementById('hidepersists').style.display = 'none';
				} else {
					document.getElementById('hidepersists').style.display = '';
				}
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onload="self.focus();document.form1.name.focus();toggleLength();toggleTransfer();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Add Database Column</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_dbgrouplist.php">database group list</a>&nbsp;&gt;&nbsp;
						<a href="admin_dbcolumnlist.php">database column list</a>&nbsp;&gt;&nbsp;
						add database column<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form method="post" name="form1" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Name:</div>
						<div class="formfield"><input type="text" name="name" size="50" value="<?php echo $_REQUEST['name']; ?>" /></div>&nbsp;<?php echo '<span class="stylered">'. $p_errorMessage. '</span>'; ?>
						<div class="clear"></div>
						<div class="formcaption">Description:</div>
						<div class="formfield"><input type="text" name="description" value="<?php echo $_REQUEST['description']; ?>" style="width: 300px;" /></div>
						<div class="clear"></div>
						<div class="formcaption">Type:</div>
						<div class="formfield">
							<select name="type" onchange="toggleLength();">
								<?php
								foreach (DbColumn::$types as $key => $value) { ?>
									<option value="<?php echo $key; ?>"<?php if ($_REQUEST['type'] == $key) { echo ' selected="selected"'; } ?>><?php echo $value; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<div id="hidelength" style="display: none;">
							<div class="formcaption">Length:</div>
							<div class="formfield"><input type="text" name="length" id="length" size="5" value="<?php echo $_REQUEST['length']; ?>" /></div>
						</div>
						<div class="clear"></div><br />
						<div class="formcaption">Entry Relevant?:</div>
						<div class="formfield"><input type="checkbox" name="entryrel" id="entryrel"<?php if ($_REQUEST['entryrel']) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div>
						<div class="formcaption">Entrant Relevant?:</div>
						<div class="formfield"><input type="checkbox" name="entrantrel" id="entrantrel"<?php if ($_REQUEST['entrantrel']) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div>
						<div class="formcaption">Transfer Relevant?:</div>
						<div class="formfield"><input type="checkbox" name="transferrel" id="transferrel"<?php if ($_REQUEST['transferrel']) { echo ' checked="checked"'; } ?> onchange="toggleTransfer();" /></div>
						<div id="hidepersists" style="display: none;">
							<div class="clear"></div><br />
							<div class="formcaption">Transfer Persist?:</div>
							<div class="formfield"><input type="checkbox" name="transferpersist" id="transferpersist"<?php if ($_REQUEST['transferpersist']) { echo ' checked="checked"'; } ?> /></div>
							<div class="clear"></div>
							<div class="formcaption">Change Persist?:</div>
							<div class="formfield"><input type="checkbox" name="changepersist" id="changepersist"<?php if ($_REQUEST['changepersist']) { echo ' checked="checked"'; } ?> /></div>
						</div>
						<div class="clear"></div><br />
						<input type="submit" name="submit" value="Insert Record" />&nbsp;&nbsp;
						<a href="admin_dbcolumnlist.php"><input type="button" name="submit2" value="Cancel (back)" /></a>
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>