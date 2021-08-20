<?php
/* ************************************************************************
' admin_new1.php -- Administration - Add an entry #1
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
'		betap 2008-06-29 16:39:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once("includes/initialise.php");
require_once(LIB_PATH. "content.php");

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

// Clear 'last racenumber allocated'
$_SESSION['racenumber'] = '';

// if we're returning from admin_new1a with a selected form,
// don't bother checking for multiple forms again.
if (!empty($_REQUEST['formid'])) {
	$p_formid = $_REQUEST['formid'];
	$p_clubtypeid = $_REQUEST['clubtypeid'];
} else {

	$races = Race::countForms($currentSite->id);

	// if there are multiple forms, prompt for which one to use
	if (count($races) > 1) {
		redirect_to('admin_new1a.php?entrantid='. $_REQUEST['entrantid']. '&surname='. $_REQUEST['surname']);
	} else {
		$race = array_shift($races);
		$p_formid = $race->formid;
		$p_clubtypeid = $race->clubtypeid;
	}
}

// Get the first valid race for default selection
$races = Race::findAllForForm($currentSite->id, $p_formid, $cookieLanguageCode);
foreach($races as $race) {
	if (!$race->allocatenumbers) {
		$p_params['raceid'] = $race->id;
		break;
	}
	$noofentries = Entry::countAllPaid($race->id);
	$numberleft = $race->limit - $noofentries;
	if ($numberleft > 0) {
		$p_params['raceid'] = $race->id;
		break;
	}
}

$race = Race::findById($p_params['raceid']);
$p_raceid = $p_params['raceid'];
$p_entrantid = $_REQUEST['entrantid'];

if (empty($p_entrantid)) {
	$p_params['surname'] = $_REQUEST['surname'];
	$p_params['affil'] = false;
	$p_params['memberaffil'] = false;
} else {

	$entrant = Entrant::findByIdWithClub($p_entrantid);
	if ($entrant) {
		foreach (get_object_vars($entrant) as $dbName => $dbValue) {
			if ($dbName == 'id') {
				$dbName = 'entrantid';
			}
			if ($dbName == 'affil' || $dbName == 'memberaffil') {
				$dbValue = strtolower($dbValue);
			}
			$p_params[$dbName] = $dbValue;
		}
	}
}
$p_params['paid'] = true;
$p_params['racenumber'] = 0;
$p_params['total'] = 0;
$p_params['discount'] = 0;
$p_params['charval'] = 0;
$p_processasp = 'admin_new2.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<?php
		$dojoParse = "true";
		include(LIB_PATH. 'dojo.php'); ?>
		<script type="text/javascript" src="scripts/ajax.js"></script>
		<script type="text/javascript" src="scripts/entries.js"></script>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript">
			dojo.require("dojo.date.locale");
			dojo.require("dojo.currency");
			dojo.require("dojo.parser");
			dojo.require("dijit.form.CheckBox");
			dojo.require("dijit.form.TextBox");
			dojo.require("dijit.form.Textarea");
			dojo.require("dijit.form.CurrencyTextBox");
			dojo.require("dijit.form.DateTextBox");
			dojo.require("dijit.form.TimeTextBox");
			dojo.require("dijit.form.FilteringSelect");
			dojo.require("dijit.form.NumberTextBox");
			dojo.addOnLoad(function() {
				clearTotals();
				toggleRaceAjax(<?php echo $p_params['raceid']; ?>, false, 0, <?php echo $race->formid; ?>, '<?php echo $p_processasp; ?>');
				toggleAffil(false);
				togglePreAllocsNew();
				dijit.byId('firstname').focus();
			});
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
		<!--[if IE 6]>
		<style>
		#innerwrapper { zoom: 1; }
		</style>
		<![endif]-->
	</head>

	<body class="tundra">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div id="innerwrapper">
					<div class="windowcontentcolumn0">
						<h2>New Entry</h2>
						<?php
						include(LIB_PATH. 'inc_adminentryform.php'); ?>
					</div>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>