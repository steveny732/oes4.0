<?php
/* ************************************************************************
' admin_clubtypelist.php -- List Club Types for Maintenance
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
require_once(LIB_PATH. 'clubtype.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['clubtype_searchname']);
	$_SESSION['clubtype_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['clubtype_searchname'] = $_POST['clubtype_searchname'];
	$_SESSION['clubtype_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['clubtype_currentpage']) ? $_SESSION['clubtype_currentpage'] : 1;
} else {
	$_SESSION['clubtype_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

$searchname = "";
if (isset($_SESSION['clubtype_searchname'])) {
	$searchname = $_SESSION['clubtype_searchname'];
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

	<body onLoad="self.focus();document.form1.clubtype_searchname.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					$itemcount = ClubType::countAll($searchname);
					$paging = new Paging($currentpage, 17, $itemcount); ?>
						
					<div class="floatleft">
						<h2>Club Type List</h2>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							Search:&nbsp;<input type="text" name="clubtype_searchname" value="<?php echo $searchname; ?>" />
							<input type="submit" name="filter" value="Go" />
						</form>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						club type list<br /><br /><br />
						<a href="admin_clubtypeadd.php">add a club type</a>
					</div>
					<div class="clear"></div><br />
					<table width="100%" cellpadding="2" cellspacing="0">
						
						<tr class="darkgreybg">
							<td width="400" align="left"><strong>Name</strong></td>
							<td width="280" align="center"><strong>Actions</strong></td>
						</tr>
						<?php
						if ($paging->pagecount() == 0) { ?>
							<tr class="<?php echo $paging->colourNewRow(); ?>">
								<td colspan="2" align="center"><br /><br /><strong>No club types found.</strong></td>
							</tr>
						<?php
						} else {
							$clubtypes = ClubType::findAllByPage($paging, $searchname);
							foreach ($clubtypes as $clubtype) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td class="smalltext" align="left"><a href="admin_clubtypeupdate.php?id=<?php echo $clubtype->id; ?>"><?php echo $clubtype->name; ?></a></td>
									<td class="smalltext" align="center">
										<a href="admin_clublist.php?clearfilter=1&clubtypeid=<?php echo $clubtype->id; ?>">edit clubs</a>&nbsp;
										<a href="admin_clubtypedelete.php?id=<?php echo $clubtype->id; ?>">delete</a>
									</td>
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