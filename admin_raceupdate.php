<?php
/* ************************************************************************
' admin_raceupdate.php -- Update a Race
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
require_once(LIB_PATH. 'race.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$race = Race::findById($_REQUEST['id']);

if (!empty($_POST['submit'])) {

	$p_date = str_replace("/", "-", $_POST['date']);
	$p_entriesopen = str_replace("/", "-", $_POST['entriesopen']);
	$p_entriesclose = str_replace("/", "-", $_POST['entriesclose']);
	$p_emailbcc = $_POST['emailbcc'];
	if (empty($p_emailbcc)) {
		$p_emailbcc = '*NONE';
	}
	$p_emailfrom = $_POST['emailfrom'];
	if (empty($p_emailfrom)) {
		$p_emailfrom = '*NONE';
	}
	$p_startnumber = $_POST['startnumber'];
	if (empty($p_startnumber)) {
		$p_startnumber = "1";
	}

	$columns = DatabaseMetadata::findMetadata('races');
	foreach ($columns as $column) {
		$dbName = $column->Field;
		if ($dbName != 'id') {
			if ($dbName == 'date') {
				$p_data = $p_date;
			} elseif ($dbName == 'emailbcc') {
				$p_data = $p_emailbcc;
			} elseif ($dbName == 'emailfrom') {
				$p_data = $p_emailfrom;
			} elseif ($dbName == 'entriesopen') {
				$p_data = $p_entriesopen;
			} elseif ($dbName == 'entriesclose') {
				$p_data = $p_entriesclose;
			} elseif ($dbName == 'startnumber') {
				$p_data = $p_startnumber;
			} else {
				$p_data = isset($_POST[$dbName]) ? $_POST[$dbName] : '';
			}
			if ($column->Type == 'tinyint(1)') {
				$p_data = $p_data ? 1 : 0;
			}
			$race->$dbName = $p_data;
		}
	}
	$race->save();

	if ($_POST['generate'] == '1' || $_POST['generate'] == 'on') {
		redirect_to('admin_genracenos.php?start='. $p_startnumber. '&limit='. $_POST['limit']. '&raceid='. $_POST['id']);
	}
	redirect_to("admin_racelist.php");
}

require_once(LIB_PATH. 'series.php');
require_once(LIB_PATH. 'agecategorytype.php');
require_once(LIB_PATH. 'form.php');
require_once(LIB_PATH. 'site.php');

$series = Series::findAll();
$agecategorytypes = AgeCategoryType::findAll();
$forms = Form::findAll();
$pages = Page::findAll();
$sites = Site::findAll();

$params = array();
foreach (get_object_vars($race) as $key => $value) {
	$params[$key] = $value;
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<?php
		include(LIB_PATH. 'dojo.php'); ?>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript">
			dojo.require("dojo.date.locale");
			dojo.require("dojo.currency");
			dojo.require("dojo.parser");
			dojo.require("dijit.form.CheckBox");
			dojo.require("dijit.form.TextBox");
			dojo.require("dijit.form.Textarea");
			dojo.require("dijit.form.NumberTextBox");
			dojo.require("dijit.form.CurrencyTextBox");
			dojo.require("dijit.form.DateTextBox");
			dojo.require("dijit.form.FilteringSelect");
	
			function checkme() {
				missinginfo = "";
				if (isNaN(dijit.byId('sequence').value) || !isNumeric(dijit.byId('sequence').value)) {
					missinginfo += "\nThe display sequence must entered and must be numeric";
				}
				if (document.form1.name.value == "") {
					missinginfo += "\nYou must enter a race name";
				}
				if (isNaN(dijit.byId('distance').value) || !isNumeric(dijit.byId('distance').value)) {
					missinginfo += "\nThe race distance must be entered and must be numeric";
				}
				if (!dijit.byId('date').isValid()) {
					missinginfo += "\nYou must enter a valid race date";
				}
				if (!dijit.byId('entriesopen').isValid()) {
					missinginfo += "\nYou must enter a valid date that online entries open or leave it blank";
				}
				if (!dijit.byId('entriesclose').isValid()) {
					missinginfo += "\nYou must enter a valid date that online entries close";
				}
				if (document.form1.transfersallowed.checked && (isNaN(dijit.byId('transferclosedays').value) || !isNumeric(dijit.byId('transferclosedays').value))) {
					missinginfo += "\nThe transfer close days must be entered and must be numeric";
				}
				if (isNaN(dijit.byId('minimumage').value) || !isNumeric(dijit.byId('minimumage').value)) {
					missinginfo += "\nThe minimum age must be entered and must be numeric";
				}
				if (document.form1.allocatenumbers.checked && (isNaN(dijit.byId('limit').value) || !isNumeric(dijit.byId('limit').value))) {
					missinginfo += "\nThe entry limit must be entered and must be numeric";
				}
				//if (!document.form1.teamsenabled.checked && !document.form1.clubsenabled.checked ||
				//	document.form1.teamsenabled.checked && document.form1.clubsenabled.checked) {
				//	missinginfo += "\nOne of either Teams or Clubs must be enabled but not both";
				//}
				if (document.form1.entryformurl.value == "") {
					missinginfo += "\nYou must enter an entry form URL (or *NONE)";
				} else if (document.form1.entryformurl.value.toUpperCase() == "*NONE") {
					document.form1.entryformurl.value = document.form1.entryformurl.value.toUpperCase();
				}
				if (document.form1.emailbcc.value == "") {
					missinginfo += "\nYou must enter a BCC email address (or *NONE)";
				} else {
					if (document.form1.emailbcc.value.toUpperCase() == "*NONE") {
						document.form1.emailbcc.value = document.form1.emailbcc.value.toUpperCase();
					} else {
						document.form1.emailbcc.value = document.form1.emailbcc.value.toLowerCase();
					}
				}
				if (document.form1.emailfrom.value == "") {
					missinginfo += "\nYou must enter a from email address (or *NONE)";
				} else {
					if (document.form1.emailfrom.value.toUpperCase() == "*NONE") {
						document.form1.emailfrom.value = document.form1.emailfrom.value.toUpperCase();
					} else {
						document.form1.emailfrom.value = document.form1.emailfrom.value.toLowerCase();
					}
				}
				if (document.form1.cumulsplits.checked && document.form1.emailfrom.value.chiptimes.value == 0) {
					missinginfo += "\nAt least one phase time must be enabled for official time is total of phases";
				}
				if (missinginfo != "") {
					alert(missinginfo);
					return false;
				} else { 
					return true;
				}
			}
			dojo.addOnLoad(function() {
				dijit.byId('sequence').focus();
				toggleTransfers();
				toggleWaitingList();
				toggleNumbers();
				toggleTransport();
				toggleTimes(<?php echo $params['chiptimes']; ?>);
				dojo.connect(dijit.byId("chiptimes"), 'onKeyUp', function(e) { setTimeout(function(){ toggleTimes(e.target.value); }, 0); });
			});
			function toggleTransfers() {
				if (document.form1.transfersallowed.checked) {
					document.getElementById("hidetransferoptions").style.display = '';
				} else {
					document.getElementById("hidetransferoptions").style.display = 'none';
				}
			}
			function toggleWaitingList() {
				if (document.form1.waitinglistenabled.checked) {
					document.getElementById("hidewaitinglistoptions").style.display = '';
				} else {
					document.getElementById("hidewaitinglistoptions").style.display = 'none';
				}
			}
			function toggleTransport() {
				if (document.form1.transportenabled.checked) {
					document.getElementById("hidetransportoptions").style.display = '';
				} else {
					document.getElementById("hidetransportoptions").style.display = 'none';
				}
			}
			function toggleNumbers() {
				//if (document.form1.allocatenumbers.checked) {
					document.getElementById("hidenumberoptions").style.display = '';
				//} else {
				//	document.getElementById("hidenumberoptions").style.display = 'none';
				//}
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

	<body class="tundra">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Update Race</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_racelist.php">race list</a>&nbsp;&gt;&nbsp;
						update race<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form1" method="post" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Display Sequence:</div>
						<div class="formfield"><input type="text" name="sequence" id="sequence" style="width: 50px;" dojoType="dijit.form.NumberTextBox" constraints="{min:1,max:9999,places:0}" promptMessage= "Enter a value between 1 and 9999" value="<?php echo $params['sequence']; ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Site:</div>
						<div class="formfield">
							<select name="siteid" id="siteid" dojoType="dijit.form.FilteringSelect" style="width: 300px;">
								<?php
								foreach ($sites as $site) { ?>
									<option value="<?php echo $site->id; ?>"<?php if ($params['siteid'] == $site->id) { echo ' selected="selected"'; } ?>><?php echo $site->title; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<div class="formcaption">Race Series:</div>
						<div class="formfield">
							<select name="seriesid" id="seriesid" dojoType="dijit.form.FilteringSelect">
								<option value="0"<?php if (empty($params['seriesid'])) { echo ' selected="selected"'; } ?>>None</option>
								<?php
								foreach ($series as $serie) { ?>
									<option value="<?php echo $serie->id; ?>"<?php if ($params['seriesid'] == $serie->id) { echo ' selected="selected"'; } ?>><?php echo $serie->name; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<div class="formcaption">Race Entry Form:</div>
						<div class="formfield">
							<select name="formid" id="formid" dojoType="dijit.form.FilteringSelect">
								<?php
								foreach ($forms as $form) { ?>
									<option value="<?php echo $form->id; ?>"<?php if ($params['formid'] == $form->id) { echo ' selected="selected"'; } ?>><?php echo $form->name; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<div class="formcaption">Race T&C Page:</div>
						<div class="formfield">
							<select name="tcpageid" id="tcpageid" dojoType="dijit.form.FilteringSelect">
								<option value="0"<?php if ($params['tcpageid'] == 0) { echo ' selected="selected"'; } ?>>Default</option>
								<?php
								foreach ($pages as $page) { ?>
									<option value="<?php echo $page->id; ?>"<?php if ($params['tcpageid'] == $page->id) { echo ' selected="selected"'; } ?>><?php echo $page->name; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<div class="formcaption">Age Category Type:</div>
						<div class="formfield">
							<select name="agecategorytypeid" id="agecategorytypeid" dojoType="dijit.form.FilteringSelect">
								<?php
								foreach ($agecategorytypes as $agecategorytype) { ?>
									<option value="<?php echo $agecategorytype->id; ?>"<?php if ($params['agecategorytypeid'] == $agecategorytype->id) { echo ' selected="selected"'; } ?>><?php echo $agecategorytype->name; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<div class="formcaption">Race Name:</div>
						<div class="formfield"><input type="text" name="name" id="name" style="width: 200px;" dojoType="dijit.form.ValidationTextBox" propercase="true" required="true" invalidMessage="Race name must be entered" value="<?php echo $params['name']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Race Distance (in km):</div>
						<div class="formfield"><input type="text" name="distance" id="distance" style="width: 100px;" dojoType="dijit.form.NumberTextBox" constraints="{min:1,max:500}" promptMessage= "Enter a value between 1.00 and 500.00" value="<?php echo $params['distance']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Race Date:</div>
						<div class="formfield"><input type="text" name="date" id="date" style="width: 100px;" dojoType="dijit.form.DateTextBox" required="true" value="<?php echo $params['date']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Online Entries Open:</div>
						<div class="formfield"><input type="text" name="entriesopen" id="entriesopen" style="width: 100px;" dojoType="dijit.form.DateTextBox" value="<?php echo $params['entriesopen']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Online Entries Close:</div>
						<div class="formfield"><input type="text" name="entriesclose" id="entriesclose" style="width: 100px;" dojoType="dijit.form.DateTextBox" required="true" value="<?php echo $params['entriesclose']; ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Transfers Allowed?:</div>
						<div class="formfield"><input type="checkbox" name="transfersallowed" id="transfersallowed" dojotype="dijit.form.CheckBox"<?php if ($params['transfersallowed']) { echo ' checked="checked"'; } ?> onclick="toggleTransfers();" /></div>
						<div id="hidetransferoptions" style="display: none;">
							<div class="clear"></div>
							<div class="formcaption">Transfer Close Days:</div>
							<div class="formfield"><input type="text" name="transferclosedays" id="transferclosedays" style="width: 50px;" dojoType="dijit.form.NumberTextBox" constraints="{min:0,max:60,places:0}" promptMessage= "Enter a value between 0 and 60" value="<?php echo $params['transferclosedays']; ?>" /></div>
						</div>
						<div class="clear"></div><br />
						<div class="formcaption">Minimum Age:</div>
						<div class="formfield"><input type="text" name="minimumage" id="minimumage" style="width: 50px;" dojoType="dijit.form.NumberTextBox" constraints="{min:1,max:40,places:0}" promptMessage= "Enter a value between 1 and 40" value="<?php echo $params['minimumage']; ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Waiting List Enabled?:</div>
						<div class="formfield"><input type="checkbox" name="waitinglistenabled" id="waitinglistenabled" dojotype="dijit.form.CheckBox"<?php if ($params['waitinglistenabled']) { echo ' checked="checked"'; } ?> onclick="toggleWaitingList();" /></div>
						<div id="hidewaitinglistoptions" style="display: none;">
							<div class="clear"></div>
							<div class="formcaption">Wait List Additions Open?:</div>
							<div class="formfield"><input type="checkbox" name="waitinglistadditionsopen" id="waitinglistadditionsopen" dojotype="dijit.form.CheckBox"<?php if ($params['waitinglistadditionsopen']) { echo ' checked="checked"'; } ?> /></div>
							<div class="clear"></div>
							<div class="formcaption">Wait List Offers Open?:</div>
							<div class="formfield"><input type="checkbox" name="waitinglistoffersopen" id="waitinglistoffersopen" dojotype="dijit.form.CheckBox"<?php if ($params['waitinglistoffersopen']) { echo ' checked="checked"'; } ?> /></div>
						</div>
						<div class="clear"></div><br />
						<div class="formcaption">Show Entry List?:</div>
						<div class="formfield"><input type="checkbox" name="showentrylist" id="showentrylist" dojotype="dijit.form.CheckBox"<?php if ($params['showentrylist']) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div>
						<div class="formcaption">T-Shirts Enabled?:</div>
						<div class="formfield"><input type="checkbox" name="tshirtsenabled" id="tshirtsenabled" dojotype="dijit.form.CheckBox"<?php if ($params['tshirtsenabled']) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div>
						<div class="formcaption">Teams Enabled?:</div>
						<div class="formfield"><input type="checkbox" name="teamsenabled" id="teamsenabled" dojotype="dijit.form.CheckBox"<?php if ($params['teamsenabled']) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div>
						<div class="formcaption">Clubs Enabled?:</div>
						<div class="formfield"><input type="checkbox" name="clubsenabled" id="clubsenabled" dojotype="dijit.form.CheckBox"<?php if ($params['clubsenabled']) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div>
						<div class="formcaption">Feedback Open?:</div>
						<div class="formfield"><input type="checkbox" name="feedbackopen" id="feedbackopen" dojotype="dijit.form.CheckBox"<?php if ($params['feedbackopen']) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div>
						<div class="formcaption">Allocate Numbers?:</div>
						<div class="formfield"><input type="checkbox" name="allocatenumbers" id="allocatenumbers" dojotype="dijit.form.CheckBox"<?php if ($params['allocatenumbers']) { echo ' checked="checked"'; } ?> onclick="toggleNumbers();" /></div>
						<div id="hidenumberoptions" style="display: none;">
							<div class="clear"></div>
							<div class="formcaption">Start Number:</div>
							<div class="formfield"><input type="text" name="startnumber" id="startnumber" style="width: 50px;" dojoType="dijit.form.NumberTextBox" constraints="{min:1,max:5000,places:0}" promptMessage= "Enter a value between 1 and 5000" value="<?php echo $params['startnumber']; ?>" /></div>
							<div class="clear"></div>
							<div class="formcaption">Entry Limit:</div>
							<div class="formfield"><input type="text" name="limit" id="limit" style="width: 50px;" dojoType="dijit.form.NumberTextBox" constraints="{min:1,max:5000,places:0}" promptMessage= "Enter a value between 1 and 5000" value="<?php echo $params['limit']; ?>" />&nbsp;Generate Numbers?&nbsp;<input type="checkbox" name="generate" dojotype="dijit.form.CheckBox" /></div>
						</div>
						<div class="clear"></div><br />
						<div class="formcaption">Transport Enabled:</div>
						<div class="formfield"><input type="checkbox" name="transportenabled" id="transportenabled" dojotype="dijit.form.CheckBox"<?php if ($params['transportenabled']) { echo ' checked="checked"'; } ?> onclick="toggleTransport();" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Option 1 Enabled:</div>
						<div class="formfield"><input type="checkbox" name="option1enabled" id="option1enabled" dojotype="dijit.form.CheckBox"<?php if ($params['option1enabled']) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div>
						<div class="formcaption">Option 2 Enabled:</div>
						<div class="formfield"><input type="checkbox" name="option2enabled" id="option2enabled" dojotype="dijit.form.CheckBox"<?php if ($params['option2enabled']) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Test Mode?:</div>
						<div class="formfield"><input type="checkbox" name="testmode" id="testmode" dojotype="dijit.form.CheckBox"<?php if ($params['testmode']) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Chip Rental Fee:</div>
						<div class="formfield"><input type="text" name="chiprentalfee" id="chiprentalfee" style="width: 100px;" dojoType="dijit.form.CurrencyTextBox" value="<?php echo $params['chiprentalfee']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Own Chip Allowed?:</div>
						<div class="formfield"><input type="checkbox" name="ownchipallowed" id="ownchipallowed" dojotype="dijit.form.CheckBox"<?php if ($params['ownchipallowed']) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Base Entry Fee:</div>
						<div class="formfield"><input type="text" name="fee" id="fee" style="width: 100px;" dojoType="dijit.form.CurrencyTextBox" value="<?php echo $params['fee']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Affiliation Fee:</div>
						<div class="formfield"><input type="text" name="affilfee" id="affilfee" style="width: 100px;" dojoType="dijit.form.CurrencyTextBox" value="<?php echo $params['affilfee']; ?>" /></div>
						<div class="clear"></div>
						<div id="hidetransportoptions" style="display: none;">
							<div class="formcaption">Transport Fee:</div>
							<div class="formfield"><input type="text" name="transportfee" id="transportfee" style="width: 100px;" dojoType="dijit.form.CurrencyTextBox" value="<?php echo $params['transportfee']; ?>" /></div>
							<div class="clear"></div>
						</div>
						<div class="formcaption">Online Fee:</div>
						<div class="formfield"><input type="text" name="onlinefee" id="onlinefee" style="width: 100px;" dojoType="dijit.form.CurrencyTextBox" value="<?php echo $params['onlinefee']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Charity Fee:</div>
						<div class="formfield"><input type="text" name="charityfee" id="charityfee" style="width: 100px;" dojoType="dijit.form.CurrencyTextBox" value="<?php echo $params['charityfee']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Charity Name:</div>
						<div class="formfield"><input type="text" name="charityname" id="charityname" style="width: 200px;" dojoType="dijit.form.TextBox" value="<?php echo $params['charityname']; ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Entry Form URL:</div>
						<div class="formfield"><input type="text" name="entryformurl" id="entryformurl" style="width: 200px;" dojoType="dijit.form.TextBox" value="<?php echo $params['entryformurl']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Cheque Payee:</div>
						<div class="formfield"><input type="text" name="payee" id="payee" style="width: 200px;" dojoType="dijit.form.TextBox" value="<?php echo $params['payee']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Postal Address:</div>
						<div class="formfield"><textarea name="postaladdress" id="postaladdress" style="width: 300px;" cols="50" rows="5" dojoType="dijit.form.Textarea"><?php echo $params['postaladdress']; ?></textarea></div>
						<div class="clear"></div>
						<div class="formcaption">Email BCC:</div>
						<div class="formfield"><input type="text" name="emailbcc" id="emailbcc" style="width: 200px;" dojoType="dijit.form.TextBox" lowercase="true" value="<?php echo $params['emailbcc']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Email From:</div>
						<div class="formfield"><input type="text" name="emailfrom" id="emailfrom" style="width: 200px;" dojoType="dijit.form.TextBox" lowercase="true" value="<?php echo $params['emailfrom']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Handling Fee Text:</div>
						<div class="formfield"><textarea name="handlingfeetext" id="handlingfeetext" style="width: 300px;" cols="50" rows="2" dojoType="dijit.form.Textarea"><?php echo $params['handlingfeetext']; ?></textarea></div>
						<div class="clear"></div>
						<div class="formcaption">Official Time is Total of Phase Times?:</div>
						<div class="formfield"><input type="checkbox" name="cumulsplits" id="cumulsplits" dojotype="dijit.form.CheckBox"<?php if ($params['cumulsplits']) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div>
						<div class="formcaption">Text for Official Time:</div>
						<div class="formfield"><input type="text" name="timetext" id="timetext" style="width: 200px;" dojoType="dijit.form.TextBox" value="<?php echo $params['timetext']; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">No.of Phase Times:</div>
						<div class="formfield"><input type="text" name="chiptimes" id="chiptimes" style="width: 50px;" cols="50" rows="2" dojoType="dijit.form.NumberTextBox" constraints="{min:0,max:6,places:0}" promptMessage= "Enter a value between 0 and 6" value="<?php echo $params['chiptimes']; ?>" /></div>
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
						<input type="submit" name="submit" value="Confirm Update" />
						<a href="admin_racelist.php"><input type="button" name="submit2" value="Cancel (back)" /></a>
						<input type="hidden" name="id" value="<?php echo $params['id']; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>