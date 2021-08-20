<?php
/* ************************************************************************
' admin_dbgroupdelete.php -- Confirm and delete a Database Group
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
'		betap 2008-06-29 16:54:37
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'dbgroup.php');
require_once(LIB_PATH. 'dbcolumn.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$dbgroup = DbGroup::findById($_REQUEST['id']);

if (!empty($_POST['submit'])) {

	DbColumn::deleteAll($dbgroup->id);
	$dbgroup->delete();
	
	redirect_to("admin_dbgrouplist.php");
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
						<h2>Delete Database Group</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_dbgrouplist.php">database group list</a>&nbsp;&gt;&nbsp;
						delete database group<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					Are you absolutely sure you want to delete the following database group?<br /><br />
					<div class="formcaption">Database Group Name:</div>
					<div class="formfield"><strong><?php echo $dbgroup->name; ?></strong></div>
					<div class="clear"></div><br /><br /><br />
					<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<input type="submit" name="submit" value="Confirm Delete" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
						<input type="hidden" name="id" value="<?php echo $dbgroup->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>