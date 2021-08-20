<?php
/* ************************************************************************
' admin_levellist.php -- List the security levels for maintenance
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
require_once(LIB_PATH. 'paging.php');
require_once(LIB_PATH. 'level.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['level_searchname']);
	$_SESSION['level_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['level_searchname'] = $_POST['level_searchname'];
	$_SESSION['level_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['level_currentpage']) ? $_SESSION['level_currentpage'] : 1;
} else {
	$_SESSION['level_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

$searchname = "";
if (isset($_SESSION['level_searchname'])) {
	$searchname = $_SESSION['level_searchname'];
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

	<body onLoad="self.focus();document.form1.level_searchname.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					$itemcount = Level::countAll($searchname);
					$paging = new Paging($currentpage, 17, $itemcount); ?>

					<div class="floatleft">
						<h2>Security Level List</h2>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							Search:&nbsp;<input type="text" name="level_searchname" value="<?php echo $searchname; ?>" />
							<input type="submit" name="filter" value="Go" />
						</form>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						security level list<br /><br /><br />
						<a href="admin_leveladd.php">add a level</a>
					</div>
					<div class="clear"></div><br />
					<table width="100%" align="center" cellpadding="2" cellspacing="0">
						<tr class="darkgreybg">
							<td width="200" align="left"><strong>Name</strong></td>
							<td width="20"><strong>Level</strong></td>
							<td width="50"><strong>Action</strong></td>
						</tr>
						<?php
						if ($paging->pagecount() == 0) { ?>
							<tr class="<?php echo $paging->colourNewRow(); ?>">
								<td colspan="3" align="center"><br /><br /><strong>No security levels found.</strong></td>
							</tr>
						<?php
						} else {
							$levels = Level::findAllByPage($paging, $searchname);
							foreach ($levels as $level) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td class="smalltext" align="left"><a href="admin_levelupdate.php?id=<?php echo $level->id; ?>"><?php echo $level->name; ?></a></td>
									<td class="smalltext"><?php echo $level->number; ?></td>
									<td class="smalltext"><a href="admin_leveldelete.php?id=<?php echo $level->id; ?>">delete</a></td>
								</tr>
							<?php
							}
						} ?>
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