<?php
/* ************************************************************************
' admin_sitepaymentadd.php -- Administration - Add a Site Payment Type
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
require_once(LIB_PATH. 'sitepayment.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$p_errorMessage = "";
if (!empty($_POST['submit'])) {
	
	// Database payment type must not already exist for site
	$sitepaymentchk = SitePayment::findByType($_SESSION['siteid'], $_POST['type']);

	if (!empty($sitepaymentchk)) {
		$p_errorMessage = "This payment type already exists for the site";
	} else {

		$sitepayment = new SitePayment();
		$sitepayment->siteid = $_SESSION['siteid'];
		$sitepayment->name = $_POST['name'];
		$sitepayment->type = $_POST['type'];
		$sitepayment->enabled = isset($_POST['enabled']) ? 1 : 0;
		$sitepayment->merchantid = $_POST['merchantid'];
		$sitepayment->save();
		
		redirect_to("admin_sitepaymentlist.php");
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
					missinginfo += "\nYou must select a name";
				}
				if (document.form1.type.value == "0") {
					missinginfo += "\nYou must select the payment type";
				}
				if (document.form1.merchantid.value == "") {
					missinginfo += "\nYou must enter the merchant id/user name";
				}
				if (document.form1.merchantid.value.toUpperCase() == "*NONE") {
					document.form1.merchantid.value = document.form1.merchantid.value.toUpperCase();
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

	<body onLoad="self.focus();document.form1.name.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Add Site Payment Type</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_sitelist.php">site list</a>&nbsp;&gt;&nbsp;
						<a href="admin_sitepaymentlist.php">site payment type list</a>&nbsp;&gt;&nbsp;
						add site payment type<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form method="post" name="form1" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Name:</div>
						<div class="formfield"><input type="text" name="name" maxlength="50" value="<?php if (isset($_REQUEST["name"])) echo $_REQUEST["name"]; ?>" style="width: 100px;" /></div>
						<div class="clear"></div>
						<div class="formcaption">Type:</div>
						<div class="formfield">
							<select name="type">
								<option value="0"<?php if (!isset($_REQUEST["type"]) || $_REQUEST["type"] == "" || $_REQUEST["type"] == "0") { echo ' selected="selected"'; } ?>>Select One</option>
								<option value="1"<?php if (isset($_REQUEST["type"]) && $_REQUEST["type"] == 1) { echo ' selected="selected"'; } ?>>Nochex</option>
								<option value="2"<?php if (isset($_REQUEST["type"]) && $_REQUEST["type"] == 2) { echo ' selected="selected"'; } ?>>Paypal</option>
								<option value="3"<?php if (isset($_REQUEST["type"]) && $_REQUEST["type"] == 3) { echo ' selected="selected"'; } ?>>Virtual TPV</option>
							</select>
						</div>&nbsp;<?php echo '<span class="stylered">'. $p_errorMessage. '</span>'; ?>
						<div class="clear"></div>
						<div class="formcaption">Merchant ID:</div>
						<div class="formfield"><input type="text" name="merchantid" id="merchantid" maxlength="100" value="<?php if (isset($_REQUEST["merchantid"])) echo $_REQUEST["merchantid"]; ?>" style="width: 300px;" /></div>
						<div class="clear"></div>
						<div class="formcaption">Enabled?:</div>
						<div class="formfield"><input type="checkbox" name="enabled" id="enabled"<?php if (isset($_REQUEST["enabled"]) && $_REQUEST["enabled"]) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div><br />
						<input type="submit" name="submit" value="Add Site Payment Type" />&nbsp;&nbsp;
						<a href="admin_sitepaymentlist.php"><input type="button" name="submit2" value="Cancel (back)" /></a>
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>