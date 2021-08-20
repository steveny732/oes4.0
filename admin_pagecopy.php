<?php
/* ************************************************************************
' admin_pagecopy.php -- Administration - Copy a page
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
'		betap 2008-07-11 17:06:12
'		Append pageid to 'index.php'.
'		betap 2008-06-27 20:39:23
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'page.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$oldpageid = $_REQUEST['id'];

if (!empty($_POST['submit'])) {

	$newpage = new Page();

	$newpage->name = $_POST['name'];
	$newpage->sequence = $_POST['sequence'];
	$newpage->url = $_POST['url'];
	$newpage->levelid = $_POST['levelid'];
	$newpage->levelonly = $_POST['levelonly'];
	$newpage->alttext = $_POST['alttext'];
	$newpage->showonmenu = $_POST['showonmenu'] ? '1' : '0';
	$newpage->showonfooter = $_POST['showonfooter'] ? '1' : '0';
	$newpage->subpageof = $_POST['subpageof'];
	$newpage->siteid = $_POST['siteid'];
	$newpage->save();

	if ($_POST['url'] == "default.php" || $_POST['url'] == "index.php") {

		// Retrieve page id just allocated and update the url accordingly
		$newpage->url .= "?id=". $newpage->id;
		$newpage->save();
	}

	$pagelangs = PageLang::findAllForPage($oldpageid);
	foreach ($pagelangs as $pagelang) {
		$newpagelang = clone $pagelang;
		unset($newpagelang->id);
		$newpagelang->pageid = $newpage->id;
		$newpagelang->save();
	}

	$contents = Content::findAllForPage($oldpageid);
	foreach ($contents as $content) {
		$newcontent = clone $content;
		unset($newcontent->id);
		$newcontent->pageid = $newpage->id;
		$newcontent->save();
		$contentlangs = ContentLang::findAllForContent($content->id);
		foreach ($contentlangs as $contentlang) {
			$newcontentlang = clone $contentlang;
			unset($newcontentlang->id);
			$newcontentlang->contentid = $newcontent->id;
			$newcontentlang->save();
		}
	}

	redirect_to("admin_pagelist.php");
}

$page = Page::findById($oldpageid);
$page->url = "index.php";

$parentpages = Page::findAll();
$levels = Level::findAll();
$sites = Site::findAll();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript">
			function checkme() {
				missinginfo = "";
				if (document.form1.name.value == "") {
					missinginfo += "\nYou must enter a page name";
				}
				if (document.form1.sequence.value == "") {
					missinginfo += "\nSequence must be entered";
				}
				if (!isNumeric(document.form1.sequence.value)) {
					missinginfo += "\nSequence must be numeric";
				}
				if (missinginfo != "") {
					alert(missinginfo);
					return false;
				} else { 
					return true;
				}
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onload="self.focus();document.form1.name.focus()">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Update Page</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_pagelist.php">page list</a>&nbsp;&gt;&nbsp;
						copy page<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form1" method="post" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Page Name:</div>
						<div class="formfield"><input type="text" name="name" maxlength="50" style="width: 200px;" value="<?php echo $page->name; ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Page Sequence:</div>
						<div class="formfield"><input type="text" name="sequence" maxlength="10" style="width: 50px;" value="<?php echo $page->sequence; ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Site:</div>
						<div class="formfield">
							<select name="siteid" id="siteid">
								<option value="0"<?php if ($page->siteid == "0") { echo ' selected="selected"'; } ?>>All Sites</option>
								<?php
								foreach ($sites as $site) { ?>
									<option value="<?php echo $site->id; ?>"<?php if ($page->siteid == $site->id) { echo ' selected="selected"'; } ?>><?php echo $site->title; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div><br />
						<div class="formcaption">Page URL:</div>
						<div class="formfield"><input type="text" name="url" maxlength="50" style="width: 200px;" value="<?php echo $page->url; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Alt Text:</div>
						<div class="formfield"><input type="text" name="alttext" maxlength="100" style="width: 200px;" value="<?php echo $page->alttext; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Show on Menu?:</div>
						<div class="formfield"><input type="checkbox" name="showonmenu"<?php if ($page->showonmenu) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div>
						<div class="formcaption">Show on Footer?:</div>
						<div class="formfield"><input type="checkbox" name="showonfooter"<?php if ($page->showonfooter) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Sub Page Of:</div>
						<div class="formfield">
							<select name="subpageof">
								<option value="0"<?php if ($page->subpageof == "0") { echo ' selected="selected"'; } ?>>None</option>
								<?php
								foreach ($parentpages as $parentpage) { ?>
									<option value="<?php echo $parentpage->id; ?>"<?php if ($page->subpageof == $parentpage->id) { echo ' selected="selected"'; } ?>><?php echo $parentpage->name; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div><br />
						<div class="formcaption">Security Level:</div>
						<div class="formfield">
							<select name="levelid">
								<?php
								foreach ($levels as $level) { ?>
									<option value="<?php echo $level->id; ?>"<?php if ($page->levelid == $level->id) { echo ' selected="selected"'; } ?>><?php echo $level->name; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<div class="formcaption">This Level Only?:</div>
						<div class="formfield"><input type="checkbox" name="levelonly"<?php if ($page->levelonly) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Confirm Copy" />
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />&nbsp;&nbsp;
						<input type="hidden" name="id" value="<?php echo $page->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>