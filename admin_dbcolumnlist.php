<?php
/* ************************************************************************
' admin_dbcolumnlist.php -- Administration - List Database Columns for Maintenance
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
'		betap 2008-06-29 16:02:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once("includes/initialise.php");
require_once(LIB_PATH. 'paging.php');
require_once(LIB_PATH. 'dbcolumn.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['dbcolumn_searchdescription']);
	$_SESSION['dbcolumn_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['dbcolumn_searchdescription'] = $_POST['dbcolumn_searchdescription'];
	$_SESSION['dbcolumn_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['dbcolumn_currentpage']) ? $_SESSION['dbcolumn_currentpage'] : 1;
} else {
	$_SESSION['dbcolumn_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

if (isset($_REQUEST['dbgroupid'])) {
	$_SESSION['dbgroupid'] = $_REQUEST['dbgroupid'];
}
$dbgroupid = $_SESSION['dbgroupid'];
$dbgroup = DbGroup::findById($dbgroupid);

$searchdescription = "";
if (isset($_SESSION['dbcolumn_searchdescription'])) {
	$searchdescription = $_SESSION['dbcolumn_searchdescription'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script src="scripts/AC_RunActiveContent.js" type="text/javascript"></script>
		<script type="text/javascript" src="scripts/general.js"></script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onLoad="self.focus();document.form1.dbcolumn_searchdescription.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					$itemcount = DbColumn::countAll($dbgroupid, $searchdescription);
					$paging = new Paging($currentpage, 17, $itemcount); ?>
					<div class="floatleft">
						<h2>Database Column List</h2>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							Search:&nbsp;<input type="text" name="dbcolumn_searchdescription" value="<?php echo $searchdescription; ?>" />
							<input type="submit" name="filter" value="Go" />
						</form>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_dbgrouplist.php">database group list</a>&nbsp;&gt;&nbsp;
						database column list<br /><br /><br />
						<?php
						if (!$dbgroup->enabled && !$dbgroup->system) { ?>
							<a href="admin_dbcolumnadd.php">add database column</a>&nbsp;
						<?php
						} ?>
					</div>
					<div class="clear"></div><br />
					<table width="100%" cellpadding="2" cellspacing="0">
						
						<tr class="darkgreybg">
							<td width="50"><strong>Name</strong></td>
							<td width="200"><strong>Description</strong></td>
							<td width="50" align="center"><strong>Type</strong></td>
							<td width="50" align="center"><strong>Length</strong></td>
							<td width="100" align="center"><strong>Actions</strong></td>
						</tr>
						<?php
						if ($paging->pagecount() == 0) { ?>
							<tr class="<?php echo $paging->colourNewRow(); ?>">
								<td colspan="5" align="center"><br /><br /><strong>No database columns found.</strong></td>
							</tr>
						<?php
						} else {
							$dbcolumns = DbColumn::findAllByPage($dbgroupid, $paging, $searchdescription);
							foreach ($dbcolumns as $dbcolumn) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td class="smalltext" align="left" valign="top">
										<a href="admin_dbcolumnupdate.php?id=<?php echo $dbcolumn->id; ?>"><?php echo $dbcolumn->name; ?></a>
									</td>
									<td class="smalltext"><?php echo $dbcolumn->description; ?></td>
									<td class="smalltext" align="center"><?php echo $dbcolumn->columnType(); ?></td>
									<td class="smalltext" align="center"><?php if (!empty($dbcolumn->length)) { echo $dbcolumn->length; } ?></td>
									<td class="smalltext" align="center" valign="top">
										<?php
										if (!$dbgroup->enabled && !$dbgroup->system) { ?>
											<a href="admin_dbcolumndelete.php?id=<?php echo $dbcolumn->id; ?>">delete</a>
										<?php
										} ?>
									</td>
								</tr>
							<?php
							}
						}
						?>
					</table>
					<?php
					echo $paging->pagingNav($_SERVER['PHP_SELF']); ?>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>