<?php
/* ************************************************************************
' admin_pageadd.php -- Administration - Add a page
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
'		betap 2008-07-11 16:53:36
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

if (!empty($_POST['submit'])) {

	$page = new Page();
	$page->name = $_POST['name'];
	$page->sequence = $_POST['sequence'];
	$page->url = $_POST['url'];
	$page->levelid = $_POST['levelid'];
	$page->levelonly = $_POST['levelonly'];
	$page->alttext = $_POST['alttext'];
	$page->showonmenu = $_POST['showonmenu'] ? '1' : '0';
	$page->showonfooter = $_POST['showonfooter'] ? '1' : '0';
	$page->subpageof = $_POST['subpageof'];
	$page->siteid = $_POST['siteid'];
	$page->save();

	if ($_POST['url'] == "default.php" || $_POST['url'] == "index.php") {
	
		// Retrieve page id just allocated and update the url accordingly
		$page->url .= "?id=". $page->id;
		$page->save();
	}

	redirect_to("admin_pagelist.php");
}

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
				if (document.form1.levelid.value == "") {
					missinginfo += "\nYou must select a security level";
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
						<h2>Add Page</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_pagelist.php">page list</a>&nbsp;&gt;&nbsp;
						add page<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form method="post" name="form1" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Page Name:</div>
						<div class="formfield"><input type="text" name="name" maxlength="50" style="width: 200px;" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Page Sequence:</div>
						<div class="formfield"><input type="text" name="sequence" maxlength="10" style="width: 50px;" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Show for Site:</div>
						<div class="formfield">
							<select name="siteid" id="siteid">
								<option value="0">All Sites</option>
								<?php
								foreach ($sites as $site) { ?>
									<option value="<?php echo $site->id; ?>"><?php echo $site->title; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div><br />
						<div class="formcaption">Page URL:</div>
						<div class="formfield"><input type="text" name="url" maxlength="200" style="width: 200px;" /></div>
						<div class="clear"></div>
						<div class="formcaption">Alt Text:</div>
						<div class="formfield"><input type="text" name="alttext" maxlength="100" style="width: 200px;" /></div>
						<div class="clear"></div>
						<div class="formcaption">Show on Menu?:</div>
						<div class="formfield"><input type="checkbox" name="showonmenu" /></div>
						<div class="clear"></div>
						<div class="formcaption">Show on Footer?:</div>
						<div class="formfield"><input type="checkbox" name="showonfooter" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Sub Page Of:</div>
						<div class="formfield">
							<select name="subpageof">
								<option value="0" selected="selected">None</option>
								<?php
								foreach ($parentpages as $parentpage) { ?>
									<option value="<?php echo $parentpage->id; ?>"><?php echo $parentpage->name; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div><br />
						<div class="formcaption">Security Level:</div>
						<div class="formfield">
							<select name="levelid">
								<option value="" selected="selected">Select a level</option>
								<?php
								foreach ($levels as $level) { ?>
									<option value="<?php echo $level->id; ?>"><?php echo $level->name; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<div class="formcaption">This Level Only?:</div>
						<div class="formfield"><input type="checkbox" name="levelonly" /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Add Page" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>