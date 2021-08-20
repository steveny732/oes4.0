<?php
/* ************************************************************************
' admin_entrychangelist.php -- Administration - List Changes Made to an Entry
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
require_once(LIB_PATH. 'change.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['change_searchsource']);
	$_SESSION['change_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['change_searchsource'] = $_POST['change_searchsource'];
	$_SESSION['change_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['change_currentpage']) ? $_SESSION['change_currentpage'] : 1;
} else {
	$_SESSION['change_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

if (isset($_REQUEST['entryid'])) {
	$_SESSION['entryid'] = $_REQUEST['entryid'];
}
$entryid = $_SESSION['entryid'];

$searchsource = "";
if (isset($_SESSION['change_searchsource'])) {
	$searchsource = $_SESSION['change_searchsource'];
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

	<body onLoad="self.focus();document.form1.change_searchsource.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					$itemcount = Change::countAll($entryid, $searchsource);
					$paging = new Paging($currentpage, 17, $itemcount); ?>

					<div class="floatleft">
						<h2>Entry Changes List</h2>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							Search:&nbsp;<input type="text" name="change_searchsource" value="<?php echo $searchsource; ?>">
							<input type="submit" name="filter" value="Go">
						</form>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_entrylist.php">entry list</a>&nbsp;&gt;&nbsp;
						entry list<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<table width="100%" cellpadding="2" cellspacing="0">
	
						<tr class="darkgreybg">
							<td width="100" align="left"><strong>Time</strong></td>
							<td width="100" align="center"><strong>Source</strong></td>
							<td width="100" align="left"><strong>User</strong></td>
							<td width="100" align="left"><strong>Column</strong></td>
							<td width="100" align="left"><strong>Old Value</strong></td>
							<td width="100" align="left"><strong>New Value</strong></td>
						</tr>
						<?php
						if ($paging->pagecount() == 0) { ?>
							<tr class="<?php echo $paging->colourNewRow(); ?>">
								<td colspan="6" align="center"><br /><br /><strong>No changes found.</strong></td>
							</tr>
						<?php
						} else {
							$changes = Change::findAllByPage($entryid, $paging, $searchsource);
							foreach ($changes as $change) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td class="smalltext" align="left"><?php echo $change->formatdate; ?></td>
									<td class="smalltext" align="center"><?php echo $change->source; ?></td>
									<td class="smalltext" align="left"><?php echo $change->username; ?></td>
									<td class="smalltext" align="left"><?php echo $change->column; ?></td>
									<td class="smalltext" align="left"><?php echo $change->oldvalue; ?></td>
									<td class="smalltext" align="left"><?php echo $change->newvalue; ?></td>
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