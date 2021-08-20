<?php
/* ************************************************************************
' admin_couponupdate.php -- Administration - Update a coupon
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
require_once(LIB_PATH. 'coupon.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$coupon = Coupon::findById($_REQUEST['id']);

if (!empty($_POST['submit'])) {

	// Check race/code combination doesn't already exist
	$p_errorMessage = '';
	if (Coupon::checkForDuplicateCouponCode($coupon->id, $_SESSION['raceid'], $_POST['code'])) {
		$p_errorMessage = 'The coupon code already exists';
	} elseif ($_POST['startdate'] > $_POST['enddate']) {
		$p_errorMessage = 'The end date cannot be before the start end';
	}
	if (empty($p_errorMessage)) {
		$p_startdate = str_replace("/", "-", $_POST['startdate']);
		$p_enddate = str_replace("/", "-", $_POST['enddate']);

		$columns = DatabaseMetadata::findMetadata('coupons');
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
				$coupon->$dbName = $p_data;
			}
		}
		$coupon->save();

		redirect_to("admin_couponlist.php");
	}
	$params = array();
	foreach($_POST as $key => $value) {
		$params[$key] = $value;
	}
} else {
	$params = array();
	foreach (get_object_vars($coupon) as $key => $value) {
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
				if (!dijit.byId('code').isValid()) {
					missinginfo += "\nYou must enter a coupon code";
				}
				if (!dijit.byId('name').isValid()) {
					missinginfo += "\nYou must enter a coupon name";
				}
				if (!dijit.byId('startdate').isValid()) {
					missinginfo += "\nYou must enter a start date";
				}
				if (!dijit.byId('enddate').isValid()) {
					missinginfo += "\nYou must enter an end date";
				}
				if (!dijit.byId('discount').isValid()) {
					missinginfo += "\nYou must enter a discount";
				}
				if (missinginfo != "") {
					alert(missinginfo);
					return false;
				} else { 
					return true;
				}
			}
			dojo.addOnLoad(function() {
				dijit.byId('code').focus();
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
						<h2>Update Coupon</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_racelist.php">race list</a>&nbsp;&gt;&nbsp;
						<a href="admin_couponlist.php">coupon list</a>&nbsp;&gt;&nbsp;
						update coupon<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form1" method="post" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Code:</div>
						<div class="formfield"><input type="text" name="code" id="code" style="width: 200px;" dojoType="dijit.form.ValidationTextBox" uppercase="true" required="true" invalidMessage="Code must be entered" value="<?php echo $params['code']; ?>" /></div>&nbsp;<?php echo '<span class="stylered">'. $p_errorMessage. '</span>'; ?>
						<div class="clear"></div>
						<div class="formcaption">Name:</div>
						<div class="formfield"><input type="text" name="name" id="name" style="width: 200px;" dojoType="dijit.form.ValidationTextBox" propercase="true" required="true" invalidMessage="Name must be entered" value="<?php echo $params['name']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">From Date:</div>
						<div class="formfield"><input type="text" name="startdate" id="startdate" style="width: 100px;" dojoType="dijit.form.DateTextBox" required="true" value="<?php echo $params['startdate']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">To Date:</div>
						<div class="formfield"><input type="text" name="enddate" id="enddate" style="width: 100px;" dojoType="dijit.form.DateTextBox" required="true" value="<?php echo $params['enddate']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Discount:</div>
						<div class="formfield"><input type="text" name="discount" id="discount" style="width: 100px;" dojoType="dijit.form.CurrencyTextBox" required="true" value="<?php echo $params['discount']; ?>" /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Confirm Update" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
						<input type="hidden" name="id" value="<?php echo $coupon->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>