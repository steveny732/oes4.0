<?php
/* ************************************************************************
' admin_commentlist.php -- Administration - List comments for maintenance
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
'		betap 2008-09-05 15:03:31
'		New script.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'paging.php');
require_once(LIB_PATH. 'race.php');
require_once(LIB_PATH. 'comment.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['comment_searchtext']);
	$_SESSION['comment_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['comment_searchtext'] = $_POST['comment_searchtext'];
	$_SESSION['comment_raceid'] = $_POST['raceid'];
	$_SESSION['comment_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['comment_currentpage']) ? $_SESSION['comment_currentpage'] : 1;
} else {
	$_SESSION['comment_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

if (isset($_REQUEST['cmd']) && $_REQUEST['cmd'] == 'mod') {
	$comment = Comment::findById($_REQUEST['id']);
	$comment->moderated = 1;
	$comment->save();

	redirect_to($_SERVER['PHP_SELF']);
}

$races = Race::findAllWithComments();
$searchtext = "";
if (isset($_SESSION['comment_searchtext'])) {
	$searchtext = $_SESSION['comment_searchtext'];
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

	<body onload="self.focus();document.form1.comment_searchtext.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					if (empty($races)) {
						$defaultraceid = 1;
					} else {
						$race = $races[0];
						$defaultraceid = $race->id;
					}
					if (empty($_SESSION['comment_raceid'])) {
						$_SESSION['comment_raceid'] = $defaultraceid;
					} else {
						$defaultraceid = $_SESSION['comment_raceid'];
					}
					$itemcount = Comment::countAll($defaultraceid, $searchtext);
					$paging = new Paging($currentpage, 17, $itemcount); ?>
					<div class="floatleft">
						<h2>Feedback List</h2>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<div class="formcaption">Race:</div>
							<div class="formfield">
								<select name="raceid">
									<?php
									if (empty($races)) { ?>
										<option value="">There are no races available with comments</option>
									<?php
									} else {
										foreach ($races as $race) { ?>
											<option value="<?php echo $race->id; ?>"<?php if ($race->id == $_SESSION['comment_raceid']) { echo ' selected="selected"'; } ?>><?php echo $race->formatdate. ' '. $race->name; ?></option>
										<?php
										}
									}?>
								</select>
							</div>
							<div class="clear"></div><br />
							<div class="formcaption">Search:</div>
							<div class="formfield"><input type="text" name="comment_searchtext" value="<?php echo $searchtext; ?>" />
							<input type="submit" name="filter" value="Go" /></div>
						</form>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						comment list<br /><br /><br />
						<a href="admin_commentadd.php">add feedback</a>
					</div>
					<div class="clear"></div><br />
					<table width="100%" cellpadding="2" cellspacing="0">
						<tr class="darkgreybg">
							<td width="150"><strong>Date</strong></td>
							<td width="100"><strong>Name</strong></td>
							<td width="100"><strong>Email</strong></td>
							<td width="200"><strong>Text</strong></td>
							<td width="150" align="center"><strong>Actions</strong></td>
						</tr>
						<?php
						if ($paging->pagecount() == 0) { ?>
							<tr class="<?php echo $paging->colourNewRow(); ?>">
								<td colspan="5" align="center"><br /><br /><strong>No comments found.</strong></td>
							</tr>
						<?php
						} else {
							$comments = Comment::findAllByPage($_SESSION['comment_raceid'], $paging, $searchtext);
							foreach ($comments as $comment) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td class="smalltext" align="left"><a href="admin_commentupdate.php?id=<?php echo $comment->id; ?>"><?php echo $comment->formatdate; ?></a></td>
									<td class="smalltext"><?php echo $comment->name; ?></td>
									<td class="smalltext"><?php echo $comment->email; ?></td>
									<td class="smalltext"><?php echo $comment->text; ?></td>
									<td class="smalltext" align="center">
										<?php
										if (!$comment->moderated) { ?>
											<a href="admin_commentlist.php?id=<?php echo $comment->id. '&cmd=mod'; ?>">set to moderated</a>&nbsp;
										<?php
										} ?>
										<a href="admin_commentdelete.php?id=<?php echo $comment->id; ?>">delete</a></td>
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