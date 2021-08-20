<?php
/* ************************************************************************
' admin_contactadd.php -- Administration - Add a Contact
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
'		betap 2008-07-11 13:07:35
'		New script.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'contact.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (!empty($_POST['submit'])) {

	$contact = new Contact();
	$contact->raceid = $_POST['raceid'];
	$contact->role = $_POST['role'];
	$contact->email = $_POST['email'];
	$contact->save();

	redirect_to("admin_contactlist.php");
}

$races = Race::findAllFuture($currentSite->id, $cookieLanguageCode);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript">
			function checkme() {
				missinginfo = "";
				<?php
				if ($currentSite->emailfrom == '*NONE') { ?>
					if (document.form1.raceid.value == "") {
						missinginfo += "\nYou must select a race for which this contact is responsible";
					}
				<?php
				} ?>
				if (document.form1.role.value == "") {
					missinginfo += "\nYou must enter a contact role";
				}
				if (document.form1.email.value == "") {
					missinginfo += "\nYou must enter the email address of this contact";
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

	<body onload="self.focus();document.form1.role.focus()">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Add Contact</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_contactlist.php">contact list</a>&nbsp;&gt;&nbsp;
						add contact<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form method="post" name="form1" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Race:</div>
						<div class="formfield">
							<select name="raceid">
								<option value="">All Races</option>
								<?php
								foreach ($races as $race) { ?>
									<option value="<?php echo $race->id; ?>"><?php echo $race->date. " ". $race->name; ?></option>
								<?php
								}
								?>
							</select>
						</div>
						<div class="clear"></div><br />
						<div class="formcaption">Role:</div>
						<div class="formfield"><input type="text" name="role" maxlength="50" style="width: 100px;" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Email Address:</div>
						<div class="formfield"><input type="text" name="email" maxlength="100" style="width: 200px;" /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Add Contact" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>