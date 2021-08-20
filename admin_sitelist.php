<?php
/* ************************************************************************
' admin_sitelist.php -- Administration - List the sites for maintenance
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
require_once(LIB_PATH. 'site.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['site_searchtitle']);
	$_SESSION['site_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['site_searchtitle'] = $_POST['site_searchtitle'];
	$_SESSION['site_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['site_currentpage']) ? $_SESSION['site_currentpage'] : 1;
} else {
	$_SESSION['site_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

$searchtitle = "";
if (isset($_SESSION['site_searchtitle'])) {
	$searchtitle = $_SESSION['site_searchtitle'];
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

	<body onLoad="self.focus();document.form1.site_searchtitle.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					$itemcount = Site::countAll($searchtitle);
					$paging = new Paging($currentpage, 17, $itemcount); ?>

					<div class="floatleft">
						<h2>Site List</h2>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							Search:&nbsp;<input type="text" name="site_searchtitle" value="<?php echo $searchtitle; ?>" />
							<input type="submit" name="filter" value="Go" />
						</form>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						site list<br /><br /><br />
						<a href="admin_siteadd.php">add a site</a>
					</div>
					<div class="clear"></div><br />
					<table width="100%" cellpadding="2" cellspacing="0">
						<tr class="darkgreybg">
							<td width="300" align="left"><strong>Title</strong></td>
							<td width="200" align="center"><strong>Actions</strong></td>
						</tr>
						<?php
						if ($paging->pagecount() == 0) { ?>
							<tr class="<?php echo $paging->colourNewRow(); ?>">
								<td colspan="2" align="center"><br /><br /><strong>No sites found.</strong></td>
							</tr>
						<?php
						} else {
							$sites = Site::findAllByPage($paging, $searchtitle);
							foreach ($sites as $site) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td class="smalltext" align="left"><a href="admin_siteupdate.php?id=<?php echo $site->id; ?>"><?php echo $site->title; ?></a></td>
									<td class="smalltext" align="center"><a href="admin_sitepaymentlist.php?clearfilter=1&siteid=<?php echo $site->id; ?>">edit payment methods</a>&nbsp;&nbsp;<a href="admin_sitedelete.php?id=<?php echo $site->id; ?>">delete</a></td>
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