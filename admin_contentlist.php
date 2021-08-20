<?php
/* ************************************************************************
' admin_contentlist.php -- Administration - List Content for Maintenance
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
require_once(LIB_PATH. 'content.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['content_searchtext']);
	$_SESSION['content_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['content_searchtext'] = $_POST['content_searchtext'];
	$_SESSION['content_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['content_currentpage']) ? $_SESSION['content_currentpage'] : 1;
} else {
	$_SESSION['content_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

if (isset($_REQUEST['pageid'])) {
	$_SESSION['pageid'] = $_REQUEST['pageid'];
}
$pageid = $_SESSION['pageid'];

$searchtext = "";
if (isset($_SESSION['content_searchtext'])) {
	$searchtext = $_SESSION['content_searchtext'];
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

	<body onload="self.focus();document.form1.content_searchtext.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					$itemcount = Content::countAll($pageid, $searchtext);
					$paging = new Paging($currentpage, 17, $itemcount); ?>
					<div class="floatleft">
						<h2>Content List</h2>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							Search:&nbsp;<input type="text" name="content_searchtext" value="<?php echo $searchtext; ?>" />
							<input type="submit" name="filter" value="Go" />
						</form>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_pagelist.php">page list</a>&nbsp;&gt;&nbsp;
						content list<br /><br /><br />
						<a href="admin_contentadd.php">add content</a>&nbsp;
					</div>
					<div class="clear"></div><br />
					<table width="100%" cellpadding="2" cellspacing="0">

						<tr class="darkgreybg">
							<td width="50" align="left"><strong>Seq.</strong></td>
							<td width="50"><strong>Type</strong></td>
							<td width="400"><strong>Content</strong></td>
							<td width="100" align="center"><strong>Actions</strong></td>
						</tr>
						<?php
						if ($paging->pagecount() == 0) { ?>
							<tr class="<?php echo $paging->colourNewRow(); ?>">
								<td colspan="4" align="center"><br /><br /><strong>No page content found.</strong></td>
							</tr>
						<?php
						} else {
							$contents = Content::findAllByPage($pageid, $paging, $searchtext);
							foreach ($contents as $content) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td class="smalltext" align="left" valign="top">
										<a href="admin_contentupdate.php?id=<?php echo $content->id; ?>"><?php echo $content->sequence; ?></a>
									</td>
									<td class="smalltext" valign="top"><?php echo $content->type; ?></td>
									<td class="smalltext"><?php echo $content->text; ?></td>
									<td class="smalltext" align="center" valign="top">
										<?php
										if (Language::countAll() > 1) { ?>
											<a href="admin_contenttrans.php?id=<?php echo $content->id; ?>">translate</a>&nbsp;
										<?php
										} ?>
										<a href="admin_contentdelete.php?id=<?php echo $content->id; ?>">delete</a>
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