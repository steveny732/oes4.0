<?php
/* ************************************************************************
' admin_agecategorylist.php -- Administration - List Age Categories for Maintenance
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
require_once(LIB_PATH. 'agecategorytype.php');
require_once(LIB_PATH. 'agecategory.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['agecategory_searchcode']);
	$_SESSION['agecategory_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['agecategory_searchcode'] = $_POST['agecategory_searchcode'];
	$_SESSION['agecategory_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['agecategory_currentpage']) ? $_SESSION['agecategory_currentpage'] : 1;
} else {
	$_SESSION['agecategory_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

if (isset($_REQUEST['agecategorytypeid'])) {
	$_SESSION['agecategorytypeid'] = $_REQUEST['agecategorytypeid'];
}
$agecategorytypeid = $_SESSION['agecategorytypeid'];

$searchcode = "";
if (isset($_SESSION['agecategory_searchcode'])) {
	$searchcode = $_SESSION['agecategory_searchcode'];
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

	<body onLoad="self.focus();document.form1.agecategory_searchcode.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					$itemcount = AgeCategory::countAll($agecategorytypeid, $searchcode);
					$paging = new Paging($currentpage, 17, $itemcount);
					$agecategorytype = AgeCategoryType::findById($agecategorytypeid); ?>

					<div class="floatleft">
						<h2>Age Category List</h2>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							Search:&nbsp;<input type="text" name="agecategory_searchcode" value="<?php echo $searchcode; ?>">
							<input type="submit" name="filter" value="Go">
						</form>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_agecategorytypelist.php">age category type list</a>&nbsp;&gt;&nbsp;
						age category list<br /><br /><br />
						<a href="admin_agecategoryadd.php">add age category</a>&nbsp;
					</div>
					<div class="clear"></div><br />
					<table width="100%" cellpadding="2" cellspacing="0">
	
						<tr class="darkgreybg">
							<td width="100" align="left"><strong>Code</strong></td>
							<td width="100" align="center"><strong>Male Only</strong></td>
							<td width="100" align="center"><strong>Female Only</strong></td>
							<td width="100" align="center"><strong>Lower <?php echo $agecategorytype->basisName(); ?></strong></td>
							<td width="100" align="center"><strong>Upper <?php echo $agecategorytype->basisName(); ?></strong></td>
							<td width="180" align="center"><strong>Actions</strong></td>
						</tr>
						<?php
						if ($paging->pagecount() == 0) { ?>
							<tr class="<?php echo $paging->colourNewRow(); ?>">
								<td colspan="6" align="center"><br /><br /><strong>No age categories found.</strong></td>
							</tr>
						<?php
						} else {
							$agecategories = AgeCategory::findAllByPage($agecategorytypeid, $paging, $searchcode);
							foreach ($agecategories as $agecategory) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td class="smalltext" align="left">
										<a href="admin_agecategoryupdate.php?id=<?php echo $agecategory->id; ?>"><?php echo $agecategory->code; ?></a>
									</td>
									<td class="smalltext" align="center"><?php echo $agecategory->maleonly ? "Yes" : "No"; ?></td>
									<td class="smalltext" align="center"><?php echo $agecategory->femaleonly ? "Yes" : "No"; ?></td>
									<td class="smalltext" align="center"><?php echo $agecategory->lowervalue; ?></td>
									<td class="smalltext" align="center"><?php echo $agecategory->uppervalue; ?></td>
									<td class="smalltext" align="center">
										<a href="admin_agecategorydelete.php?id=<?php echo $agecategory->id; ?>">delete</a>
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