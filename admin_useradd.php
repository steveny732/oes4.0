<?php
/* ************************************************************************
' admin_useradd.php -- Administration - Add a user
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
'		betap 2008-07-10 20:27:51
'		Correct field name user_level to user_levelid
'		betap 2008-06-29 16:02:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'user.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (!empty($_POST['submit'])) {

	$user = new User();
	$user->name = $_POST['name'];
	$user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$user->levelid = $_POST['levelid'];
	$user->save();

	redirect_to('admin_userlist.php');
}

$levels = Level::findAll();
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
					missinginfo += "\nYou must enter a user name";
				}
				if (document.form1.password.value == "") {
					missinginfo += "\nYou must enter a password";
				}
				if (document.form1.password.value != document.form1.password1.value) {
					missinginfo += "\nThe passwords must agree";
				}
				if (document.form1.levelid.value == "") {
					missinginfo += "\nYou must select a user level";
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
						<h2>Add User</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_userlist.php">user list</a>&nbsp;&gt;&nbsp;
						add user<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form method="post" name="form1" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">User Name:</div>
						<div class="formfield"><input type="text" name="name" maxlength="50" style="width: 200px;" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Password:</div>
						<div class="formfield"><input type="password" name="password" maxlength="50" style="width: 200px;" /></div>
						<div class="clear"></div>
						<div class="formcaption">Re-enter Password:</div>
						<div class="formfield"><input type="password" name="password1" maxlength="50" style="width: 200px;" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">User Level:</div>
						<div class="formfield">
							<select name="levelid">
								<option value="" selected="selected">Select a level</option>
								<?php
								foreach ($levels as $level) { ?>
									<option value="<?php echo $level->id; ?>"><?php echo $level->name; ?></option>
									<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Add User" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>