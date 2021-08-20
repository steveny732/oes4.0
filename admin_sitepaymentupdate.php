<?php
/* ************************************************************************
' admin_sitepaymentupdate.php -- Administration - Update a Site Payment Type
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
require_once(LIB_PATH. 'sitepayment.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$sitepayment = SitePayment::findById($_REQUEST['id']);

if (!empty($_POST['submit'])) {
	
	$sitepayment->siteid = $_SESSION['siteid'];
	$sitepayment->name = $_POST['name'];
	$sitepayment->enabled = isset($_POST['enabled']) ? 1 : 0;
	$sitepayment->merchantid = $_POST['merchantid'];
	$sitepayment->save();

	redirect_to("admin_sitepaymentlist.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script src="scripts/AC_RunActiveContent.js" type="text/javascript"></script>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript">
			function checkme() {
				missinginfo = "";
				if (document.form1.name.value == "") {
					missinginfo += "\nYou must select a name";
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
						<h2>Update Site Payment Type</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_sitelist.php">site list</a>&nbsp;&gt;&nbsp;
						<a href="admin_sitepaymentlist.php">site payment type list</a>&nbsp;&gt;&nbsp;
						update site payment type<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form1" method="post" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Name:</div>
						<div class="formfield"><input type="text" name="name" maxlength="50" value="<?php echo $sitepayment->name; ?>" style="width: 100px;" /></div>
						<div class="clear"></div>
						<div class="formcaption">Type:</div>
						<div class="formfield">
							<strong>
								<?php
								if ($sitepayment->type == 1) {
									echo "Nochex";
								} elseif ($sitepayment->type == 2) {
									echo "Paypal";
								} elseif ($sitepayment->type == 3) {
									echo "Virtual TPV";
								} ?>
							</strong>
						</div>
						<div class="clear"></div>
						<div class="formcaption">Merchant ID:</div>
						<div class="formfield"><input type="text" name="merchantid" id="merchantid" maxlength="100" value="<?php echo $sitepayment->merchantid; ?>" style="width: 300px;" /></div>
						<div class="clear"></div>
						<div class="formcaption">Enabled?:</div>
						<div class="formfield"><input type="checkbox" name="enabled" id="enabled"<?php if ($sitepayment->enabled) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div><br />
						<input type="submit" name="submit" value="Confirm Update" />
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />&nbsp;&nbsp;
						<input type="hidden" name="id" value="<?php echo $sitepayment->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>