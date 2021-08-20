<?php
/* ************************************************************************
' news.php -- Race news page
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
'		betap 2008-06-29 21:22:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once("includes/initialise.php");
require_once(LIB_PATH. "paging.php");
require_once(LIB_PATH. "newsitem.php");

if (!defined('DB_SERVER') || $currentSite->downformaint && !$session->isLoggedIn()) {
	redirect_to("downformaint.php");
}

$url = $_SERVER['SCRIPT_NAME'];
$url = substr($url, strrpos($url, "/") + 1, strlen($url) - strrpos($url, "/"));

$contents = Content::findByURL($url, $cookieLanguageCode);
$content = array_shift($contents);

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['news_searchtext']);
	$_SESSION['news_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['news_searchtext'] = htmlspecialchars($_POST['searchtext'], ENT_QUOTES);
	$_SESSION['news_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['news_currentpage']) ? $_SESSION['news_currentpage'] : 1;
} else {
	$_SESSION['news_currentpage'] = htmlspecialchars($_REQUEST['page'], ENT_QUOTES);
	redirect_to($_SERVER['PHP_SELF']);
}

$searchtext = "";
if (isset($_SESSION['news_searchtext'])) {
	$searchtext = $_SESSION['news_searchtext'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript">
			function initMenu() {
				var myMenuBar = new Spry.Widget.MenuBar('myMenuBar', {imgDown:'SpryAssets/SpryMenuBarDownHover.gif', imgRight:'SpryAssets/SpryMenuBarRightHover.gif'});
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<link href="SpryAssets/SpryMenuBarVertical.css" rel="stylesheet" type="text/css" />
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
		<!--[if IE 6]>
		<style>
		#innerwrapper { zoom: 1; }
		</style>
		<![endif]-->
	</head>

	<body onload="initMenu();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div id="innerwrapper">
					<div id="banner">
						<img src="<?php echo $currentSite->bannerimgurl; ?>" />
					</div>
					<?php
					include(LIB_PATH. "inc_language.php"); ?>
					<div id="menucolumn">
						<img src="<?php echo $currentSite->clubimgurl; ?>" /><br />
						<?php
						include(LIB_PATH. "inc_menu.php"); ?>
					</div>
					<div id="contentcolumn">
						<?php
						echo $content->text;
						$content = array_shift($contents); ?>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<?php
							echo $content->text; ?>&nbsp;
							<input type="text" name="searchtext" value="<?php echo $searchtext; ?>" />&nbsp;
							<?php
							$content = array_shift($contents); ?>
							<input type="submit" name="filter" value="<?php echo $content->text; ?>" />
						</form>
						<div class="clear"></div><br />
						<?php
						$itemcount = NewsItem::countAll($searchtext);
						$paging = new Paging($currentpage, 10, $itemcount);
						$content = array_shift($contents);

						if ($paging->pagecount() == 0) { ?>
							<div class="<?php echo $paging->colourNewRow(); ?>">
								<div class="buttonblock">
									<br /><br /><strong><?php if (empty($content->text)) { echo $content->summary; } else { echo $content->text; } ?></strong>
								</div>
							</div>
						<?php
						} else {
							$newsitems = NewsItem::findAllByPage($paging, $searchtext);
							foreach ($newsitems as $newsitem) { ?>
								<div class="<?php echo $paging->colourNewRow(); ?>">
									<div class="floatleft"><h3><?php echo $newsitem->headline; ?></h3></div>
									<div class="floatright"><?php echo $newsitem->formatdate; ?></div>
									<div class="clear"></div><br />
									<?php
									echo $newsitem->text; ?><br />
									<img src="images/black.gif" width="100%" height="1" alt="" /><br />
								</div>
							<?php
							}
						} ?><br /><br />
						<?php
						echo $paging->pagingNav($_SERVER['PHP_SELF']); ?>
					</div>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>