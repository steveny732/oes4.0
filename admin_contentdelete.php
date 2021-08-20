<?php
/* ************************************************************************
' admin_contentdelete.php -- Administration - Delete Page Content
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

$content = Content::findById($_REQUEST['id']);

if (!empty($_POST['submit'])) {

	$content->delete();
	
	redirect_to("admin_contentlist.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body>
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Delete Content</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_pagelist.php">page list</a>&nbsp;&gt;&nbsp;
						<a href="admin_contentlist.php">content list</a>&nbsp;&gt;&nbsp;
						delete content<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					Are you absolutely sure you want to delete the following content item?<br /><br />
					<div class="formcaption">Page:</div>
					<div class="formfield"><strong><?php echo $content->name; ?></strong></div>
					<div class="clear"></div><br />
					<div class="formcaption">Sequence:</div>
					<div class="formfield"><strong><?php echo $content->sequence; ?></strong></div>
					<div class="clear"></div><br />
					<div class="formcaption">Type:</div>
					<div class="formfield"><strong><?php echo $content->type; ?></strong></div>
					<div class="clear"></div><br />
					<div class="formcaption">Text:</div>
					<div class="formfield"><?php echo $content->text; ?></div>
					<div class="clear"></div><br /><br /><br />
					<form method="post" name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<input type="submit" name="submit" value="Confirm Delete" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
						<input type="hidden" name="id" value="<?php echo $content->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>