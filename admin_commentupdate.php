<?php
/* ************************************************************************
' admin_commentupdate.php -- Administration - Update a comments item
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
'		betap 2008-09-05 15:18:03
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'comment.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$comment = Comment::findById($_REQUEST['id']);

if (!empty($_POST['submit'])) {

	$p_date = str_replace("/", "-", $_POST['date']). $_POST['time'];

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
				if (document.form1.date.value == "") {
					missinginfo += "\nYou must enter the comment date";
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
						<h2>Update Feedback</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_commentlist.php">feedback list</a>&nbsp;&gt;&nbsp;
						update feedback<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form1" method="post" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Date &amp; Time:</div>
						<div class="formfield"><input type="text" name="date" id="date" dojoType="dijit.form.DateTextBox" required="true" value="<?php echo substr($comment->date, 0, 10); ?>" /><input type="text" name="time" id="time" dojoType="dijit.form.TimeTextBox" required="true" value="<?php echo 'T'. substr($comment->date, 11, 8); ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Name:</div>
						<div class="formfield"><input type="text" name="name" size="50" dojoType="dijit.form.TextBox" value="<?php echo $comment->name; ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Email:</div>
						<div class="formfield"><input type="text" name="email" value="<?php echo $comment->email; ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Comments:</div>
						<div class="formfield"><textarea name="text" cols="50" rows="10" id="text"><?php echo $comment->text; ?></textarea></div>
						<div class="clear"></div><br />
						<div class="formcaption">Moderated?:</div>
						<div class="formfield"><input type="checkbox" name="moderated"<?php if ($comment->moderated) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Confirm Update" />
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />&nbsp;&nbsp;
						<input type="hidden" name="id" value="<?php echo $comment->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>