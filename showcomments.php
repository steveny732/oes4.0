<?php
/* ************************************************************************
' showcomments.php -- Show race feedback
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
'		betap 2008-09-05 13:50:35
'		New script.
'
' ************************************************************************ */
require_once("includes/initialise.php");
require_once(LIB_PATH. "comment.php");

if (!defined('DB_SERVER') || $currentSite->downformaint && !$session->isLoggedIn()) {
	redirect_to("downformaint.php");
}

$url = $_SERVER['SCRIPT_NAME'];
$url = substr($url, strrpos($url, "/") + 1, strlen($url) - strrpos($url, "/"));

$contents = Content::findByURL($url, $cookieLanguageCode);
$content = array_shift($contents);

if (isset($_REQUEST['clearfilter'])) {
	$_SESSION['comments_currentpage'] = 1;
}

if (isset($_REQUEST['raceid'])) {
	$_SESSION['comments_raceid'] = htmlspecialchars($_REQUEST['raceid'], ENT_QUOTES);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['comments_currentpage']) ? $_SESSION['comments_currentpage'] : 1;
} else {
	$_SESSION['comments_currentpage'] = htmlspecialchars($_REQUEST['page'], ENT_QUOTES);
	redirect_to($_SERVER['PHP_SELF']);
}

$raceid = $_SESSION['comments_raceid'];
$searchtext = '';
$itemcount = Comment::countAllModerated($raceid, $searchtext);
$paging = new Paging($currentpage, 5, $itemcount);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body>
		<div id="pagecontainer">
			<div id="windowcontainer">
				<div class="windowcontentcolumn0">
					<div class="floatright">
						<a href="#" onclick="window.close();" style="color:#000000; text-decoration:none"><?php echo $content->text; ?>&nbsp;<img src="images/close_icon.gif" width="8" height="8" alt="<?php echo $content->text; ?>" /></a>
					</div>
					<div class="clear"></div>
					<?php
					$content = array_shift($contents);
					echo $content->text;
					$content = array_shift($contents);
					
					if ($paging->pagecount() == 0) { ?>
						<div class="<?php echo $paging->colourNewRow(); ?>">
							<div class="buttonblock">
								<br /><br /><strong><?php echo $content->text; // No comments found ?></strong>
							</div>
						</div>
					<?php
					} else {
						$comments = Comment::findAllByPageModerated($raceid, $paging, $searchtext);
						foreach ($comments as $comment) {
							$rowcolour = $paging->colourNewRow(); ?>
							<div class="<?php echo $rowcolour; ?>">
								<div class="floatleft"><strong><?php echo $comment->name; ?></strong></div>
								<div class="floatright"><?php echo $comment->formatdate; ?></div>
							</div>
							<div class="clear"></div>
							<div class="<?php echo $rowcolour; ?>">
								<?php echo $comment->text; ?>
							</div>
							<div class="<?php echo $rowcolour; ?>">
								<img src="images/black.gif" width="100%" height="1" alt="" />
							</div>
						<?php
						}
					} ?><br /><br />
					<?php
					echo $paging->pagingNav($_SERVER['PHP_SELF']); ?>
				</div>
				<?php
				include(LIB_PATH. "inc_windowfooter.php"); ?>
			</div>
		</div>
	</body>
</html>