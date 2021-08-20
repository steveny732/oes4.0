<?php
/* ************************************************************************
' admin_newstrans.php -- Administration - Translate a News Item
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
require_once('includes/initialise.php');
require_once(LIB_PATH. 'newsitem.php');
require_once(LIB_PATH. 'newslang.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (isset($_REQUEST['form2']) && $_REQUEST['form2'] == "form2") {
	$_SESSION['languagecode'] = $_REQUEST['languagecode'];
}

$newslang = NewsLang::findById($_REQUEST['id'], $_SESSION['languagecode']);

if (!empty($_POST['submit'])) {

	if (!$newslang) {
		$newslang = new NewsLang();
	}
	$newslang->newsid = $_POST['id'];
	$newslang->languagecode = $_SESSION['languagecode'];
	$newslang->summary = $_POST['summary'];
	$newslang->text = $_POST['text'];
	$newslang->headline = $_POST['headline'];
	$newslang->save();

	redirect_to("admin_newslist.php");
}

$languages = Language::findAll();
$newsitem = NewsItem::findById($_REQUEST['id']);

$p_summary = $newsitem->summary;
$p_text = $newsitem->text;
$p_headline = $newsitem->headline;

if (!empty($newslang)) {
	$p_summary = $newslang->summary;
	$p_text = $newslang->text;
	$p_headline = $newslang->headline;
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript" src="scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
		<?php
		include(LIB_PATH. 'dojo.php'); ?>
		<script type="text/javascript">
			dojo.require("dojo.parser");
			dojo.require("dijit.form.TextBox");
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

	<body class="tundra" onload="self.focus();document.form1.name.focus()">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Translate News Item</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_newslist.php">news list</a>&nbsp;&gt;&nbsp;
						translate news item<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form2" id="langform2" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Language Name:</div>
						<div class="formfield">
							<select name="languagecode" id="languagecode" onchange="document.form2.submit();">
								<?php
								foreach ($languages as $language) {
									if ($language->code != $currentSite->languagecode) {
										echo '<option value="'. $language->code. '"';
										if (empty($_SESSION['languagecode'])) {
											$_SESSION['languagecode'] = $language->code;
										}
										if ($_SESSION['languagecode'] == $language->code) {
											echo ' selected="selected"';
										}
										echo '>'. $language->name. '</option>';
									}
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<input type="hidden" name="form2" value="form2" />
						<input type="hidden" name="id" value="<?php echo $newsitem->id; ?>" />
					</form>
					<form name="form1" method="post" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">News Headline:</div>
						<div class="formfield"><input type="text" name="headline" maxlength="50" value="<?php echo $p_headline; ?>" dojoType="dijit.form.TextBox" /></div>
						<div class="clear"></div>
						<div class="formcaption">Summary:</div>
						<div class="formfield"><textarea name="summary" cols="50" rows="5" id="summary"><?php echo $p_summary; ?></textarea></div>
						<div class="clear"></div><br />
						<div class="formcaption">News Text:</div>
						<div class="formfield"><textarea name="text" cols="50" rows="10" id="text"><?php echo $p_text; ?></textarea></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Add/Update" />
						<a href="admin_newslist.php"><input type="button" name="Submit2" value="Cancel (back)" /></a>
						<input type="hidden" name="id" value="<?php echo $newsitem->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>