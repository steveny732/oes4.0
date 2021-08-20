<?php
/* ************************************************************************
' offer1.php -- Offer entry to waiting list #1 - Validate the user
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
'		betap 2008-07-01 09:00:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once("includes/initialise.php");
require_once(LIB_PATH. "content.php");

if (!defined('DB_SERVER') || $currentSite->downformaint && !$session->isLoggedIn()) {
	redirect_to("downformaint.php");
}

$url = $_SERVER['SCRIPT_NAME'];
$url = substr($url, strrpos($url, "/") + 1, strlen($url) - strrpos($url, "/"));

$contents = Content::findByURL($url, $cookieLanguageCode);
$content = array_shift($contents);

$races = Race::findAllWithWaitingList($currentSite->id, $cookieLanguageCode);

$transfersClosed = '';
$JS = '<script type="text/javascript">function loadRaces(){var d=new Date();var strText;var i=0;';
$p_raceid = htmlspecialchars($_REQUEST['raceid'], ENT_QUOTES);
foreach ($races as $race) {
	$noofentries = Entry::countAllPaid($race->id);
	$noofpreallocations = Preallocation::countAllUnused($race->id);
	$numberleft = $race->limit - $noofentries - $noofpreallocations;
	if ($numberleft <= 0) {
		if (empty($p_raceid)) {
			$p_raceid = $race->id;
		}
		$JS .= 'd.setFullYear('. $race->year(). ','. ($race->month() - 1). ','. $race->day(). ');strText=dojo.date.locale.format(d,{selector:\'date\',formatLength:\'medium\',locale:\''. $cookieLanguageCode. '\'}) + \' '. $race->name. '\';document.form1.raceid.options[i++]=new Option(strText,'. $race->id. ');';
		$transfersClosed .= '<input type="hidden" name="transfersclosed_'. $race->id. '" id="transfersclosed_'. $race->id. '" value="'. $race->transfersclosed(). '" />';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<?php $dojoParse = 'false';
		include(LIB_PATH. 'dojo.php'); ?>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript">
			dojo.require("dojo.parser");
			dojo.require("dojo.date.locale");
			dojo.require("dijit.form.CheckBox");
			dojo.require("dijit.form.TextBox");
			dojo.require("dijit.form.ValidationTextBox");
			dojo.require("dijit.form.Select");
			dojo.require("dijit.form.FilteringSelect");
			dojo.require("dijit.form.DateTextBox");
	<?php
	$p_surnameerror = $content->text;
	$content = array_shift($contents);
	$p_postcodeerror = $content->text;
	$content = array_shift($contents);
	$p_doberror = $content->text;
	$content = array_shift($contents);
	$p_offersclosederror = $content->text;
	$content = array_shift($contents); ?>
			function checkform() {
				missinginfo = "";
				var transfersclosed = document.getElementById('transfersclosed_' + dijit.byId('raceid').value).value;
				if (transfersclosed == 'True') {
					missinginfo += "\n<?php echo $p_offersclosederror; ?>";
				}
				if (document.form1.surname.value == '') {
					missinginfo += "\n<?php echo $p_surnameerror; ?>";
				}
				if (document.form1.postcode.value == '') {
					missinginfo += "\n<?php echo $p_postcodeerror; ?>";
				}
				if (!dijit.byId('dob').isValid()) {
					missinginfo += "\n<?php echo $p_doberror; ?>";
				}
				if (missinginfo != "") {
					alert(missinginfo);
				}	else { 
					document.form1.submit();
				}
			}
			
			dojo.addOnLoad(function() {
				loadRaces();
				dojo.parser.parse();
				dijit.byId('surname').focus();
			});
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
					echo $content->text;
					$content = array_shift($contents); ?>
					<form name="form1" method="post" action="transfer2.php">
						<div class="formcaption"><?php echo $content->text; ?></div>
						<div class="formfield">
							<select name="raceid" id="raceid" tabindex="1" style="width: 300px;" dojoType="dijit.form.Select">
							</select>
						</div>
						<?php
						$JS .= '}</script>';
						echo $JS;
						echo $transfersClosed;
						$content = array_shift($contents); ?>
	
						<div class="clear"></div>
						<div class="formcaption"><?php echo $content->text; ?></div>
						<div class="formfield"><input name="surname" type="text" id="surname" tabindex="2" style="width: 200px;" dojoType="dijit.form.ValidationTextBox" propercase="true" required="true" invalidMessage="<?php echo $p_surnameerror; ?>" /></div>
						<div class="clear"></div>
						<?php
						$content = array_shift($contents); ?>
						<div class="formcaption"><?php echo $content->text; ?></div>
						<div class="formfield"><input name="postcode" type="text" id="postcode" tabindex="3" style="width: 80px;" dojoType="dijit.form.ValidationTextBox" uppercase="true" required="true" invalidMessage="<?php echo $p_postcodeerror; ?>" /></div>
						<div class="clear"></div>
						<?php
						$content = array_shift($contents); ?>
						<div class="formcaption"><?php echo $content->text; ?></div>
						<div class="formfield">
							<?php
							$content = array_shift($contents); ?>
							<input type="text" name="dob" id="dob" tabindex="4" dojoType="dijit.form.DateTextBox" required="true" promptMessage="<?php echo $content->text;?>" />
						</div>
						<div class="clear"></div><br />
						<?php
						$content = array_shift($contents); ?>
						<div class="formcaption"><?php echo $content->text; ?></div>
						<div class="formfield"><input name="firstname" type="text" id="firstname" tabindex="5" style="width: 200px;" dojoType="dijit.form.TextBox" propercase="true" /></div>
						<div class="clear"></div>
						<input type="hidden" value="2" name="change" /><br /><br />
						<div class="buttonblock">
							<?php
							$content = array_shift($contents); ?>
							<div class="button"><a href="javascript:window.close();"><?php echo $content->text; ?></a></div>
							<?php
							$content = array_shift($contents); ?>
							<div class="button"><a href="javascript:checkform();"><?php echo $content->text; ?></a></div>
						</div>
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_windowfooter.php"); ?>
			</div>
		</div>
	</body>
</html>