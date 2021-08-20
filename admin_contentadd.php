<?php
/* ************************************************************************
' admin_contentadd.php -- Administration - Add Page Content
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
require_once(LIB_PATH. 'content.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (!empty($_POST['submit'])) {

	if ($_POST['type'] == 'plaintext') {
		$p_text = $_POST['plaintext'];
	} else {
		$p_text = $_POST['formattedtext'];
	}

	$content = new Content();
	$content->pageid = $_SESSION['pageid'];
	$content->sequence = $_POST['sequence'];
	$content->type = $_POST['type'];
	$content->text = $p_text;
	$content->save();

	redirect_to("admin_contentlist.php");
}
$formattedtext = '';
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
				if (document.form1.type.value == "") {
					missinginfo += "\nYou must select a content type";
				}
				if (document.form1.sequence.value == "") {
					missinginfo += "\nYou must enter a sequence";
				}
				if (!isNumeric(document.form1.sequence.value)) {
					missinginfo += "\nSequence must be numeric";
				}
				if (missinginfo != "") {
					alert(missinginfo);
					return false;
				} else { 
					return true;
				}
			}

			function toggleText() {
				if (document.form1.type.selectedIndex == 0) {
					document.getElementById('textformatted').style.display = 'block';
					document.getElementById('textplain').style.display = 'none';
				} else {
					document.getElementById('textformatted').style.display = 'none';
					document.getElementById('textplain').style.display = 'block';
				}
			}
		</script>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript" src="scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript">
			tinyMCE.init({
				mode : "textareas",
				elements : "ajaxfilemanager",
				theme : "advanced",
				plugins : "advimage,advlink,media,contextmenu",
				file_browser_callback : "ajaxfilemanager",
				content_css : "css/styles.css",
				width : "700px",
				height : "400px",
				relative_urls : true
			});
			function ajaxfilemanager(field_name, url, type, win) {
				var ajaxfilemanagerurl = "../../../../jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php";
				switch (type) {
					case "image":
						break;
					case "media":
						break;
					case "flash": 
						break;
					case "file":
						break;
					default:
						return false;
				}
				tinyMCE.activeEditor.windowManager.open({
						url: "../../../../jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php",
						width: 782,
						height: 440,
						inline : "yes",
						close_previous : "no"
				},{
						window : win,
						input : field_name
				});
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onload="self.focus();document.form1.sequence.focus();toggleText();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Add Content</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_pagelist.php">page list</a>&nbsp;&gt;&nbsp;
						<a href="admin_contentlist.php">content list</a>&nbsp;&gt;&nbsp;
						add content<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form method="post" name="form1" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Sequence:</div>
						<div class="formfield"><input type="text" name="sequence" size="10" /></div>
						<div class="clear"></div>
						<div class="formcaption">Type:</div>
						<div class="formfield">
							<select name="type" onchange="toggleText();">
								<option value="text">Formatted Text</option>
								<option value="plaintext">Plain Text</option>
								<option value="raceentry">Race Entry</option>
								<option value="news">News</option>
								<option value="contact">Contact</option>
								<option value="contactemail">Contact Email</option>
							</select>
						</div>
						<div class="clear"></div><br />
						<div id="textformatted" style="display: none;">
							<textarea name="formattedtext" cols="70" rows="20" id="formattedtext"><?php echo $formattedtext; ?></textarea>
						</div>
						<div id="textplain" style="display: none;">
							<div class="formcaption">Content Text:</div>
							<div class="formfield"><input name="plaintext" size="70" /></div><br /><br />
						</div>
						<div class="clear"></div><br />
						<input type="submit" name="submit" value="Insert Record" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>