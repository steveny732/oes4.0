<?php
/* ************************************************************************
' admin_seriesadd.php -- Administration - Add a Race Series
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
require_once(LIB_PATH. 'series.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (!empty($_POST['submit'])) {

	$serie = new Series();
	$serie->name = $_POST['name'];
	$serie->discnoraces = $_POST['discnoraces'];
	$serie->seriesracenumbers = $_POST['seriesracenumbers'] ? '1' : '0';
	$serie->discount = $_POST['discount'];
	$serie->save();

	redirect_to("admin_serieslist.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript">
			function checkme() {
				missinginfo = "";
				if (document.form1.name.value == "") {
					missinginfo += "\nYou must enter a series name";
				}
				if (!isNumeric(document.form1.discnoraces.value)) {
					missinginfo += "\nThe number of races for discount must be numeric";
				}
				if (!isNumeric(document.form1.discount.value)) {
					missinginfo += "\nThe discount must be numeric";
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
						<h2>Add Series</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_serieslist.php">series list</a>&nbsp;&gt;&nbsp;
						add series<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form method="post" name="form1" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Series Name:</div>
						<div class="formfield"><input type="text" name="name" id="name" maxlength="50" style="width: 200px;" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">No. of Races for Discount:</div>
						<div class="formfield"><input type="text" name="discnoraces" id="discnoraces" maxlength="10" style="width: 20px;" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Discount:</div>
						<div class="formfield"><input type="text" name="discount" id="discount" maxlength="10" style="width: 50px;" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Series-Wide Race Numbers?:</div>
						<div class="formfield"><input type="checkbox" name="seriesracenumbers" id="seriesracenumbers" /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Add Series" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>