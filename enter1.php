<?php
/* ************************************************************************
' enter1.php -- Public Entry Process #1 - Terms and Conditions
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
require_once(LIB_PATH. "content.php");

if (!defined('DB_SERVER') || $currentSite->downformaint && !$session->isLoggedIn()) {
	redirect_to("downformaint.php");
}

// Count how many forms are associated with active races with available places
// (if a race id was passed, we'll only be reading one race anyway)
$p_raceid = '';
$p_formid = '';
$actionURL = 'enter2.php';

if (empty($_REQUEST['raceid'])) {
	$races = Race::countForms($currentSite->id);
	if (count($races) > 1) {
		$actionURL = 'enter1a.php';
	} else {
		$race = array_shift($races);
		$p_formid = $race->formid;
		$p_clubtypeid = $race->clubtypeid;
	}
} else {
	$p_raceid = htmlspecialchars($_REQUEST['raceid'], ENT_QUOTES);
	$race = Race::findByIdWithFormData($p_raceid);
	$p_formid = $race->formid;
	$p_clubtypeid = $race->clubtypeid;
}

// If specific T & C page content details given, use those. Otherwise use the defaults.
if ($race->tcpageid != 0) {
	$contents = Content::findByPageId($race->tcpageid, $cookieLanguageCode);
} else {
	$url = $_SERVER['SCRIPT_NAME'];
	$url = substr($url, strrpos($url, "/") + 1, strlen($url) - strrpos($url, "/"));
	$contents = Content::findByURL($url, $cookieLanguageCode);
}
$content = array_shift($contents);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<?php
		$dojoParse = "false";
		include(LIB_PATH. 'dojo.php'); ?>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript">
			dojo.require("dojo.parser");
			dojo.require("dijit.form.CheckBox");
	
			function checkform() {
				missinginfo = "";
				if (!document.form1.agree.checked) {
					missinginfo += "\n<?php echo $content->text; ?>";
				} 
				if (missinginfo != "") {
					alert(missinginfo);
				}	else { 
					document.form1.submit();
				}
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body class="tundra">
		<div id="pagecontainer">
			<div id="windowcontainer">
				<div class="windowcontentcolumn0">
					<?php
					$content = array_shift($contents);
					echo $content->text;
					$content = array_shift($contents); ?>
	
					<form action="<?php echo $actionURL; ?>" method="post" name="form1">
						<input type="checkbox" name="agree" id="agree" value="1" dojotype="dijit.form.CheckBox" />&nbsp;<label for="agree"><?php echo $content->text; ?></label><br /><br /><br />
						<input type="hidden" name="formid" value="<?php echo $p_formid; ?>" />
            <input type="hidden" name="clubtypeid" value="<?php echo $p_clubtypeid; ?>" />
						<input type="hidden" name="raceid" value="<?php echo $p_raceid; ?>" />
						<input type="hidden" name="entrantid" value="<?php if (!empty($_SESSION['entrantid']) && !empty($_REQUEST['entrantid'])) { echo htmlspecialchars($_REQUEST['entrantid'], ENT_QUOTES); } ?>" />
						<div class="buttonblock">
							<?php
							$content = array_shift($contents); ?>
							<div class="button"><a href="javascript:window.close();"><?php echo $content->text; ?></a></div>
							<?php
							$content = array_shift($contents); ?>
							<div class="button"><a href="javascript:checkform();"><?php echo $content->text; ?></a></div>
						</div><br /><br />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_windowfooter.php"); ?>
			</div>
		</div>
	</body>
</html>