<?php
/* ************************************************************************
' admin_clubadd.php -- Administration - Add a club
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
require_once(LIB_PATH. 'club.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (!empty($_POST['submit'])) {

	$club = new Club();
	$club->clubtypeid = $_SESSION['clubtypeid'];
	$club->name = $_POST['name'];
	$club->frostbite = isset($_POST['frostbite']) ? 1 : 0;
	$club->affiliated = isset($_POST['affiliated']) ? 1 : 0;
	$club->save();

	redirect_to("admin_clublist.php");
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
			if (document.form1.club_name.value == "") {
				missinginfo += "\nYou must enter a club name";
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

	<body onLoad="self.focus();document.form1.name.focus()">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Add Club</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_clubtypelist.php">club type list</a>&nbsp;&gt;&nbsp;
						<a href="admin_clublist.php">club list</a>&nbsp;&gt;&nbsp;
						add club<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form method="post" name="form1" onSubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Club Name:</div>
						<div class="formfield"><input type="text" name="name" style="width: 200px;" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">League Club?:</div>
						<div class="formfield"><input type="checkbox" name="frostbite" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Affiliated?:</div>
						<div class="formfield"><input type="checkbox" name="affiliated" /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Insert Record" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onClick="javascript:history.go(-1);" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>