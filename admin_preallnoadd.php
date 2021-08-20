<?php
/* ************************************************************************
' admin_preallnoadd.php -- Administration - Add a pre-allocation
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
'		betap 2008-06-29 20:27:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'preallocation.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (!empty($_POST['form'])) {

	// if (only from race number entered, just create one preallocation
	$toracenumber = $_POST['toracenumber'];
	if (empty($toracenumber)) {
		$toracenumber = $_POST['fromracenumber'];
	}

	for ($x = $_POST['fromracenumber']; $x <= $toracenumber; $x++) {
		// Only add if a preallocation doesn't already exist
		$chkpreallno = RaceNo::findWithoutPreall($_SESSION['raceid'], $x);
		if ($chkpreallno) {
			$preallno = new Preallocation();
			$preallno->raceid = $_SESSION['raceid'];
			$preallno->clubid = $_POST['clubid'];
			$preallno->racenumber = $x;
			$preallno->save();
		}
	}

	redirect_to("admin_preallnolist.php");
}

$clubs = Club::findAllForRace($_SESSION['raceid']);
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
				if (document.form1.fromracenumber.value == "") {
					missinginfo += "\nYou must enter a 'from' race number";
				} else {
					if (isNaN(document.form1.fromracenumber.value) || !isNumeric(document.form1.fromracenumber.value)) {
						missinginfo += "\nThe 'from' race number must be numeric";
					}
				}
				if (document.form1.toracenumber.value != "") {
					if (isNaN(document.form1.toracenumber.value) || !isNumeric(document.form1.toracenumber.value)) {
						missinginfo += "\nThe 'to' race number must be numeric";
					}
					if (missinginfo == "") {
						if (parseInt(document.form1.fromracenumber.value) > parseInt(document.form1.toracenumber.value)) {
							missinginfo += "\nThe 'from' race number must be lower or equal to the 'to' race number.";
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
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onload="self.focus();document.form1.clubid.focus()">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Add Preallocation</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_racelist.php">race list</a>&nbsp;&gt;&nbsp;
						<a href="admin_preallnolist.php">preallocation list</a>&nbsp;&gt;&nbsp;
						add preallocation<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form method="post" name="form1" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Club:</div>
						<div class="formfield">
							<select name="clubid">
								<option value="0"<?php if (!isset($_POST['clubid']) || empty($_POST['clubid'])) { echo ' selected="selected"'; } ?>>Administration</option>
								<?php
								foreach ($clubs as $club) { ?>
									<option value="<?php echo $club->id; ?>"<?php if (isset($_POST['clubid']) && $_POST['clubid'] == $club->id) { echo ' selected="selected"'; } ?>><?php echo $club->name ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<div class="formcaption">From Race Number:</div>
						<div class="formfield"><input type="text" name="fromracenumber" value="<?php if (isset($_POST['fromracenumber'])) echo $_POST['fromracenumber']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">To Race Number:</div>
						<div class="formfield"><input type="text" name="toracenumber" value="<?php if (isset($_POST['toracenumber'])) echo $_REQUEST['toracenumber']; ?>" /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Insert Record(s)" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
						<input type="hidden" name="form" value="form1" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>