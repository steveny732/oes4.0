<?php
/* ************************************************************************
' admin_salesitemadd.php -- Add a sales item
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
'		betap 2008-06-29 16:33:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'salesitem.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (!empty($_POST['submit'])) {

	$salesitem = new SalesItem();
	$columns = DatabaseMetadata::findMetadata('salesitems');
	foreach ($columns as $column) {
		$dbName = $column->Field;
		if ($dbName != 'id') {
			$p_data = $_POST[$dbName];
			if ($column->Allow == 'tinyint(1)') {
				$p_data = $p_data ? 1 : 0;
			}
			$salesitem->$dbName = $p_data;
		}
	}
	$salesitem->save();

	redirect_to('admin_salesitemlist.php');
}

if (!empty($p_errorMessage)) {
	foreach($_POST as $key => $value) {
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
			dojo.require("dojo.currency");
			dojo.require("dojo.parser");
			dojo.require("dijit.form.CheckBox");
			dojo.require("dijit.form.TextBox");
			dojo.require("dijit.form.CurrencyTextBox");
			function checkme() {
				missinginfo = "";
				if (!dijit.byId('name').isValid()) {
					missinginfo += "\nYou must enter a sales item name";
				}
				if (!dijit.byId('price').isValid()) {
					missinginfo += "\nYou must enter a price";
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
						<h2>Add Sales Item</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_salesitemlist.php">sales item list</a>&nbsp;&gt;&nbsp;
						add sales item<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form method="post" name="form1" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Sales Item Name:</div>
						<div class="formfield"><input type="text" name="name" id="name" maxlength="50" style="width: 200px;" value="<?php if (isset($params["name"])) echo $params["name"]; ?>" dojoType="dijit.form.ValidationTextBox" required="true" invalidMessage="Name must be entered" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Price:</div>
						<div class="formfield"><input type="text" name="price" id="price" style="width: 100px;" dojoType="dijit.form.CurrencyTextBox" required="true" value="<?php if (isset($params["price"])) echo $params["price"]; ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Allow Quantity?:</div>
						<div class="formfield"><input type="checkbox" name="allowquantity" id="allowquantity" dojotype="dijit.form.CheckBox" <?php if (isset($params['allowquantity']) && $params['allowquantity']) { echo ' checked="checked"'; } ?>" /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Add Sales Item" />&nbsp;&nbsp;
						<a href="admin_salesitemlist.php"><input type="button" name="submit2" value="Cancel (back)" /></a>
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>