<?php
/* ************************************************************************
' admin_racepriceupdate.php -- Administration - Update a race price
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
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'raceprice.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$raceprice = RacePrice::findById($_REQUEST['id']);

if (!empty($_POST['submit'])) {

	$p_errorMessage = '';
	$p_startdate = str_replace("/", "-", $_POST['startdate']);
	$p_enddate = str_replace("/", "-", $_POST['enddate']);
	// Check there are no price overlaps
	if (RacePrice::checkForOverlappingPrices($raceprice->id, $_SESSION['raceid'], $p_startdate, $p_enddate)) {
		$p_errorMessage = 'A price already exists for the dates specified';
	} elseif ($_POST['startdate'] > $_POST['enddate']) {
		$p_errorMessage = 'The end date cannot be before the start end';
	}
	if (empty($p_errorMessage)) {

	$columns = DatabaseMetadata::findMetadata('raceprices');
	foreach ($columns as $column) {
		$dbName = $column->Field;
		if ($dbName != 'id') {
			if ($dbName == 'raceid') {
				$p_data = $_SESSION['raceid'];
			} elseif ($dbName == 'startdate') {
				$p_data = $p_startdate;
			} elseif ($dbName == 'enddate') {
				$p_data = $p_enddate;
			} else {
				$p_data = $_POST[$dbName];
			}
			if ($column->Type == 'tinyint(1)') {
				$p_data = $p_data ? 1 : 0;
			}
			$raceprice->$dbName = $p_data;
		}
	}
	$raceprice->save();

	redirect_to("admin_racepricelist.php");
	}
	$params = array();
	foreach($_POST as $key => $value) {
		$params[$key] = $value;
	}
} else {
	$params = array();
	foreach (get_object_vars($raceprice) as $key => $value) {
		$params[$key] = $value;
	}
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<?php
		include(LIB_PATH. 'dojo.php'); ?>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript">
			dojo.require("dojo.date.locale");
			dojo.require("dojo.currency");
			dojo.require("dojo.parser");
			dojo.require("dijit.form.TextBox");
			dojo.require("dijit.form.CurrencyTextBox");
			dojo.require("dijit.form.DateTextBox");

			function checkme() {
				missinginfo = "";
				if (!dijit.byId('name').isValid()) {
					missinginfo += "\nYou must enter a race price name";
				}
				if (!dijit.byId('startdate').isValid()) {
					missinginfo += "\nYou must enter a start date";
				}
				if (!dijit.byId('enddate').isValid()) {
					missinginfo += "\nYou must enter an end date";
				}
				if (!dijit.byId('discount').isValid()) {
					missinginfo += "\nYou must enter a race fee";
				}
				if (missinginfo != "") {
					alert(missinginfo);
					return false;
				} else { 
					return true;
				}
			}
			dojo.addOnLoad(function() {
				dijit.byId('name').focus();
			});
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body class="tundra">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Update Race Price</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_racelist.php">race list</a>&nbsp;&gt;&nbsp;
						<a href="admin_racepricelist.php">race price list</a>&nbsp;&gt;&nbsp;
						update race price<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form1" method="post" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Name:</div>
						<div class="formfield"><input type="text" name="name" id="name" style="width: 200px;" dojoType="dijit.form.ValidationTextBox" propercase="true" required="true" invalidMessage="Name must be entered" value="<?php echo $params['name']; ?>" /></div>&nbsp;<?php echo '<span class="stylered">'. $p_errorMessage. '</span>'; ?>
						<div class="clear"></div>
						<div class="formcaption">From Date:</div>
						<div class="formfield"><input type="text" name="startdate" id="startdate" style="width: 100px;" dojoType="dijit.form.DateTextBox" required="true" value="<?php echo $params['startdate']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">To Date:</div>
						<div class="formfield"><input type="text" name="enddate" id="enddate" style="width: 100px;" dojoType="dijit.form.DateTextBox" required="true" value="<?php echo $params['enddate']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Race Fee:</div>
						<div class="formfield"><input type="text" name="fee" id="fee" style="width: 100px;" dojoType="dijit.form.CurrencyTextBox" required="true" value="<?php echo $params['fee']; ?>" /></div>
            <div class="clear"></div>
						<div class="formcaption">Online Fee:</div>
						<div class="formfield"><input type="text" name="onlinefee" id="onlinefee" style="width: 100px;" dojoType="dijit.form.CurrencyTextBox" required="true" value="<?php echo $params['onlinefee']; ?>" /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Confirm Update" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
						<input type="hidden" name="id" value="<?php echo $raceprice->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>