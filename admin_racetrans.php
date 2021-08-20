<?php
/* ************************************************************************
' admin_racetrans.php -- Administration - Translate a Page
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
' Version: 1.0
' Last Modified By:
'  betap 2008-06-27 20:39:23
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'race.php');
require_once(LIB_PATH. 'racelang.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (isset($_REQUEST['form2']) && $_REQUEST['form2'] == "form2") {
	$_SESSION['languagecode'] = $_REQUEST['languagecode'];
}

$racelang = RaceLang::findById($_REQUEST['id'], $_SESSION['languagecode']);

if (!empty($_POST['submit'])) {
	if (!$racelang) {
		$racelang = new RaceLang();
	}
	$columns = DatabaseMetadata::findMetadata('racelangs');
	foreach ($columns as $column) {
		$dbName = $column->Field;
		if ($dbName != 'id') {
			if ($dbName == 'raceid') {
				$p_data = $_POST['id'];
			} elseif ($dbName == 'languagecode') {
				$p_data = $_SESSION[$dbName];
			} else {
				$p_data = $_POST[$dbName];
			}
			if ($column->Type == 'tinyint(1)') {
				$p_data = $p_data ? 1 : 0;
			}
			$racelang->$dbName = $p_data;
		}
	}
	$racelang->save();

	redirect_to("admin_racelist.php");
}

$languages = Language::findAll();
$race = Race::findById($_REQUEST['id']);

if ($racelang) {
	foreach (get_object_vars($racelang) as $key => $value) {
		$params[$key] = $value;
	}
} else {
	foreach (get_object_vars($race) as $key => $value) {
		$params[$key] = $value;
	}
}
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
					missinginfo += "\nYou must enter a race name";
				}
				if (missinginfo != "") {
					alert(missinginfo);
					return false;
				} else { 
					return true;
				}
			}
			function toggleTimes(chiptimes) {
				if (chiptimes > 0) {
					document.getElementById("hidechiptime").style.display = '';
				} else {
					document.getElementById("hidechiptime").style.display = 'none';
				}
				if (chiptimes > 1) {
					document.getElementById("hidechip2time").style.display = '';
				} else {
					document.getElementById("hidechip2time").style.display = 'none';
				}
				if (chiptimes > 2) {
					document.getElementById("hidechip3time").style.display = '';
				} else {
					document.getElementById("hidechip3time").style.display = 'none';
				}
				if (chiptimes > 3) {
					document.getElementById("hidechip4time").style.display = '';
				} else {
					document.getElementById("hidechip4time").style.display = 'none';
				}
				if (chiptimes > 4) {
					document.getElementById("hidechip5time").style.display = '';
				} else {
					document.getElementById("hidechip5time").style.display = 'none';
				}
				if (chiptimes > 5) {
					document.getElementById("hidechip6time").style.display = '';
				} else {
					document.getElementById("hidechip6time").style.display = 'none';
				}
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onload="self.focus();document.form1.name.focus();toggleTimes(<?php echo $race->chiptimes; ?>);">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Translate Page</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_racelist.php">race list</a>&nbsp;&gt;&nbsp;
						translate race<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form2" id="langform2" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Language Name:</div>
						<div class="formfield">
							<select name="languagecode" id="languagecode" onchange="document.form2.submit();">
								<?php
								foreach ($languages as $language) {
									if ($language->code != $currentSite->languagecode) {
										echo '<option value="'. $language->code. '"';
										if (empty($_SESSION['languagecode'])) {
											$_SESSION['languagecode'] = $language->code;
										}
										if ($_SESSION['languagecode'] == $language->code) {
											echo ' selected="selected"';
										}
										echo '>'. $language->name. '</option>';
									}
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<input type="hidden" name="form2" value="form2" />
						<input type="hidden" name="id" value="<?php echo $race->id; ?>" />
					</form>
					<form name="form1" method="post" onsubmit="return checkme();" action="admin_racetrans.php">
						<div class="formcaption">Race Name:</div>
						<div class="formfield"><input type="text" name="name" maxlength="50" style="width: 200px;" value="<?php echo $params['name']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Charity Name:</div>
						<div class="formfield"><input type="text" name="charityname" id="charityname" maxlength="50" value="<?php echo $params['charityname']; ?>" style="width: 200px;" /></div>
						<div class="clear"></div>
						<div class="formcaption">Entry Form URL:</div>
						<div class="formfield"><input type="text" name="entryformurl" id="entryformurl" maxlength="100" value="<?php echo $params['entryformurl']; ?>" style="width: 200px;" /></div>
						<div class="clear"></div>
						<div class="formcaption">Postal Address:</div>
						<div class="formfield"><textarea name="postaladdress" id="postaladdress" cols="50" rows="5"><?php echo $params['postaladdress']; ?></textarea></div>
						<div class="clear"></div>
						<div class="formcaption">Handling Fee Text:</div>
						<div class="formfield"><textarea name="handlingfeetext" id="handlingfeetext" cols="50" rows="2"><?php echo $params['handlingfeetext']; ?></textarea></div>
						<div class="clear"></div>
						<div class="formcaption">Text for Official Time:</div>
						<div class="formfield"><input type="text" name="timetext" id="timetext" style="width: 200px;" dojoType="dijit.form.TextBox" value="<?php echo $params['timetext']; ?>" /></div>
						<div id="hidechiptime" style="display: none;">
							<div class="clear"></div>
							<div class="formcaption">Text for Phase 1:</div>
							<div class="formfield"><input type="text" name="chiptimetext" id="chiptimetext" style="width: 200px;" dojoType="dijit.form.TextBox" value="<?php echo $params['chiptimetext']; ?>" /></div>
						</div>
						<div id="hidechip2time" style="display: none;">
							<div class="clear"></div>
							<div class="formcaption">Text for Phase 2:</div>
							<div class="formfield"><input type="text" name="chiptime2text" id="chiptime2text" style="width: 200px;" dojoType="dijit.form.TextBox" value="<?php echo $params['chiptime2text']; ?>" /></div>
						</div>
						<div id="hidechip3time" style="display: none;">
							<div class="clear"></div>
							<div class="formcaption">Text for Phase 3:</div>
							<div class="formfield"><input type="text" name="chiptime3text" id="chiptime3text" style="width: 200px;" dojoType="dijit.form.TextBox" value="<?php echo $params['chiptime3text']; ?>" /></div>
						</div>
						<div id="hidechip4time" style="display: none;">
							<div class="clear"></div>
							<div class="formcaption">Text for Phase 4:</div>
							<div class="formfield"><input type="text" name="chiptime4text" id="chiptime4text" style="width: 200px;" dojoType="dijit.form.TextBox" value="<?php echo $params['chiptime4text']; ?>" /></div>
						</div>
						<div id="hidechip5time" style="display: none;">
							<div class="clear"></div>
							<div class="formcaption">Text for Phase 5:</div>
							<div class="formfield"><input type="text" name="chiptime5text" id="chiptime5text" style="width: 200px;" dojoType="dijit.form.TextBox" value="<?php echo $params['chiptime5text']; ?>" /></div>
						</div>
						<div id="hidechip6time" style="display: none;">
							<div class="clear"></div>
							<div class="formcaption">Text for Phase 6:</div>
							<div class="formfield"><input type="text" name="chiptime6text" id="chiptime6text" style="width: 200px;" dojoType="dijit.form.TextBox" value="<?php echo $params['chiptime6text']; ?>" /></div>
						</div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Add/Update" />
						<a href="admin_racelist.php"><input type="button" name="submit2" value="Cancel (back)" /></a>
						<input type="hidden" name="id" value="<?php echo $race->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>