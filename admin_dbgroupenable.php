<?php
/* ************************************************************************
' admin_dbgroupenable.php -- Administration - Enable a Database Group
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
require_once(LIB_PATH. 'dbgroup.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (!empty($_POST['submit'])) {

	// Process all columns in the chosen group which haven't yet been processed
	$dbcolumns = DbColumn::findAllByGroup($_REQUEST['id']);

	foreach ($dbcolumns as $dbcolumn) {
		if ($dbcolumn->type == 3) {
			$p_type = " INTEGER(11)";
		} elseif ($dbcolumn->type == 6) {
			$p_type = " DECIMAL(11,2)";
		} elseif ($dbcolumn->type == 7) {
			$p_type = " DATE";
		} elseif ($dbcolumn->type == 11) {
			$p_type = " TINYINT(1)";
		} elseif ($dbcolumn->type == 202) {
			$p_type = " VARCHAR(". $dbcolumn->length. ")";
		} elseif ($dbcolumn->type == 300) {
			$p_type = " TEXT";
		}
		if ($dbcolumn->entryrel) {
			$resultSet = $db->query("ALTER TABLE `". DB_PREFIX. "entries` ADD COLUMN `". $dbcolumn->name. "`". $p_type);
		}
		if ($dbcolumn->entrantrel) {
			$resultSet = $db->query("ALTER TABLE `". DB_PREFIX. "entrants` ADD COLUMN `". $dbcolumn->name. "`". $p_type);
		}
		if ($dbcolumn->transferrel) {
			$resultSet = $db->query("ALTER TABLE `". DB_PREFIX. "transfers` ADD COLUMN `old". $dbcolumn->name. "`". $p_type);
			$resultSet = $db->query("ALTER TABLE `". DB_PREFIX. "transfers` ADD COLUMN `new". $dbcolumn->name. "`". $p_type);
		}
	}

	DbColumn::setGroupColumnsUpdated($_REQUEST['id']);

	$dbgroup = DbGroup::findById($_REQUEST['id']);
	$dbgroup->enabled = 1;
	$dbgroup->save();

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
						<h2>Enable Database Group</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_dbgrouplist.php">database group list</a>&nbsp;&gt;&nbsp;
						enable database group<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					WARNING: This will update your database and is irreversable.<br /><br />
					Make sure the server is not too busy and that the site is set to 'down for maintenance' before continuing.
					<form method="post" name="form1" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Enable Group" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
						<input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>