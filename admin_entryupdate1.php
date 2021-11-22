<?php
/* ************************************************************************
' admin_entryupdate1.php -- Administration - Update an entry #1
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
require_once('includes/initialise.php');
require_once(LIB_PATH. 'entry.php');
require_once(LIB_PATH. 'agecategorytype.php');
require_once(LIB_PATH. 'agecategory.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

// Clear 'last racenumber allocated'
$_SESSION['racenumber'] = '';

$entry = Entry::findById($_REQUEST['id']);

if (!empty($entry)) {

$p_param = array();
$extraJS = '';


	// Load up dictionary
	foreach (get_object_vars($entry) as $dbName => $dbValue) {
		if ($dbName == 'affil' || $dbName == 'memberaffil') {
			$dbValue = strtolower($dbValue);
		} elseif ($dbName == 'discount') {
			if (empty($dbValue)) {
				$dbValue = 0;
			}
			$p_discount = $dbValue;
			$p_applyDiscount = 'false';
			if ($p_discount != 0) {
				$p_applyDiscount = 'true';
			}
		} elseif ($dbName == 'raceid') {
			$race = Race::findByIdWithFormData($dbValue);
			$agecategorytype = AgeCategoryType::findById($race->agecategorytypeid);
			$p_raceid = $race->id;
			$p_formid = $race->formid;
			$p_clubtypeid = $race->clubtypeid;
		} elseif ($dbName == 'agecategorycode') {
			if ($dbValue != AgeCategory::calcAgeCategory($race->date, $agecategorytype->basis, $agecategorytype->id, $entry->dob, $entry->gender)) {
				$p_params['overridecategory'] = $dbValue;
			}
		} elseif ($dbName == 'waitdate' && !empty($dbValue)) {
			$p_params['waittime'] = 'T'. substr($dbValue, 11, 2). ':'. substr($dbValue, 14, 2). ':'. substr($dbValue, 17, 2);
			$dbValue = substr($dbValue, 0, 10);
		} elseif ($dbName == 'salesitems' && !empty($dbValue)) {
			// Load up selected sales items
			$salesitems = preg_split(',', $dbValue);
			foreach ($salesitems as $salesitem) {
				$sifields = preg_split(';', $salesitem);
				$p_params['si_'. $p_raceid. '_'. $sifields[0]] = true;
				$extraJS .= 'toggleSalesItem('. $p_raceid. ', '. $sifields[0]. ', '. $sifields[3]. ', true);';
				if ($sifields[3]) {
					$p_params['siqty_'. $p_raceid. '_'. $sifields[0]] = $sifields[2];
					$extraJS .= 'toggleSalesItemQty('. $p_raceid. ',  '. $sifields[0]. ');';
				} else {
					$p_params['siqty_'. $p_raceid. '_'. $sifields[0]] = 0;
				}
			}
		}
		$p_params[$dbName] = $dbValue;
	}
	$p_processasp = "admin_entryupdate2.php";
} ?>
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
				toggleRaceAjax(<?php echo $p_params['raceid']; ?>, <?php echo $p_applyDiscount; ?>, <?php echo $p_discount; ?>, <?php echo $p_formid; ?>, '<?php echo $p_processasp; ?>');
				toggleAffil(false);
				togglePreAllocsNew();
				<?php if (!empty($p_params['couponcode'])) { ?>
					globCouponOK=checkCoupon();
				<?php } else { ?>
					globCouponOK=true;
				<?php }
				echo $extraJS; ?>
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
						<div class="floatleft">
							<h2>Update Entry</h2>
						</div>
						<div class="floatright" style="text-align: right;">
							<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
							<a href="admin_entrylist.php">entry list</a>&nbsp;&gt;&nbsp;
							update entry<br /><br /><br />
						</div>
						<div class="clear"></div><br />
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