<?php
/* ************************************************************************
' admin_commentadd.php -- Administration - Add a comments item
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
'		betap 2008-06-05 15:06:11
'		New script.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'comment.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (!empty($_POST['submit'])) {

	$p_date = str_replace("/", "-", $_POST['date']). $_POST['time'];

	$comment = new Comment();
	$comment->raceid = $_SESSION['raceid'];
	$comment->date = $p_date;
	$comment->name = $_POST['name'];
	$comment->email = $_POST['email'];
	$comment->text = $_POST['text'];
	$comment->moderated = isset($_POST['moderated']) ? 1 : 0;
	$comment->save();

	redirect_to("admin_commentlist.php");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<?php
		include(LIB_PATH. 'dojo.php'); ?>
		<script type="text/javascript">
			dojo.require("dojo.date.locale");
			dojo.require("dojo.parser");
			dojo.require("dijit.form.TextBox");
			dojo.require("dijit.form.DateTextBox");
			dojo.require("dijit.form.TimeTextBox");

			function checkme() {
				missinginfo = "";
				if (!dijit.byId('date').isValid() || !dijit.byId('time').isValid()) {
					missinginfo += "\nYou must enter a valid comment date/time";
				}
				if (missinginfo != "") {
					alert(missinginfo);
					return false;
				} else { 
					return true;
				}
			}

			dojo.addOnLoad(function() {
				dijit.byId('date').focus();
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
						<h2>Add Feedback</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_commentlist.php">feedback list</a>&nbsp;&gt;&nbsp;
						add feedback<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form method="post" name="form1" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Date &amp; Time:</div>
						<div class="formfield"><input type="text" name="date" id="date" dojoType="dijit.form.DateTextBox" required="true" /><input type="text" name="time" id="time" dojoType="dijit.form.TimeTextBox" required="true" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Name:</div>
						<div class="formfield"><input type="text" name="name" size="50" dojoType="dijit.form.TextBox" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Email:</div>
						<div class="formfield"><input type="text" name="email" size="50" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Comments:</div>
						<div class="formfield"><textarea name="text" cols="50" rows="10" id="text"></textarea></div>
						<div class="clear"></div><br />
						<div class="formcaption">Moderated?:</div>
						<div class="formfield"><input type="checkbox" name="moderated" /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Add Comment" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>