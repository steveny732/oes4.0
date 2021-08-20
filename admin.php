<?php
/* ************************************************************************
' admin.php -- Administration - Administration entry point
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
require_once('includes/dbconfig.php');
if (!defined('DB_SERVER')) {
	header('Location: install.php');
	exit;
}
require_once('includes/initialise.php');

if (isset($_REQUEST['logout']) && $_REQUEST['logout'] == 1) {
	$session->logout();
}
if ($session->isLoggedIn()) {
	redirect_to('admin_loggedin.php');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Administration Login</title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. 'style_overrides.php'); ?>
	</head>

	<body onload="self.focus();document.form1.username.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<h2>Admin Login</h2>
					<form name="form1" method="post" action="admin_login.php">
						<div class="formcaption">User Name:&nbsp;</div>
						<div class="formfield"><input name="username" type="text" id="username" value="<?php if(isset($_REQUEST['username'])) { echo htmlentities($_REQUEST['username'], ENT_QUOTES); } ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Password:&nbsp;</div>
						<div class="formfield"><input name="password" type="password" id="password" /></div>
						<div class="clear"></div><br />
						<?php if (isset($_REQUEST['error']) && $_REQUEST['error'] == '1') { ?>
							<strong><div class="stylered">Invalid user name or password</div></strong><br /><br />
						<?php } else if (isset($_REQUEST['error']) && $_REQUEST['error'] == '2') { ?>
							<strong><div class="stylered">You've been logged out due to inactivity</div></strong><br /><br />
						<?php } ?>
						<div class="formcaption">&nbsp;</div>
						<div class="formfield"><input type="submit" name="Submit" value="Submit"></div>
						<div class="clear"></div><br /><br />
					</form>
				</div>
				<?php
				include LIB_PATH. 'inc_footer.php'; ?>
			</div>
		</div>
	</body>
</html>