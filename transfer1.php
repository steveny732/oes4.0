<?php
/* ************************************************************************
' transfer1.php -- Transfer/Change a race entry #1 - validate original entrant details
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
'		betap 2008-07-01 09:16:00
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

$races = Race::findAllAllowingChanges($currentSite->id, $cookieLanguageCode);

$transfersclosed = '';
$JS = '<script type="text/javascript">function loadRaces(){var d=new Date();var strText;var i=0;';
$p_raceid = "";
if (isset($_REQUEST['raceid'])) {
	$p_raceid = htmlspecialchars($_REQUEST['raceid'], ENT_QUOTES);
}
foreach ($races as $race) {
	if (empty($p_raceid)) {
		$p_raceid = $race->id;
	}
	$JS .= 'd.setFullYear('. $race->year(). ','. ($race->month() - 1). ','. $race->day(). ');strText=dojo.date.locale.format(d,{selector:\'date\',formatLength:\'medium\',locale:\''. $cookieLanguageCode. '\'}) + \' '. $race->name. '\';document.form1.raceid.options[i++]=new Option(strText,'. $race->id. ');';
	$transfersclosed .= '<input type="hidden" name="transfersclosed_'. (isset($race->id) ? $race->id : ''). '" id="transfersclosed_'. (isset($race->id) ? $race->id : ''). '" value="'. (isset($race->transfersclosed) ? $race->transfersclosed : ''). '" />';
}

$race = Race::findById($p_raceid);
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
			dojo.require("dojo.date.locale");
			dojo.require("dijit.form.CheckBox");
			dojo.require("dijit.form.TextBox");
			dojo.require("dijit.form.ValidationTextBox");
			dojo.require("dijit.form.FilteringSelect");
			dojo.require("dijit.form.Select");
			dojo.require("dijit.form.DateTextBox");
			<?php
			$p_surnameerror = $content->text;
			$content = array_shift($contents);
			$p_postcodeerror = $content->text;
			$content = array_shift($contents);
			$p_doberror = $content->text;
			$content = array_shift($contents);
			$p_transfersclosederror = $content->text;
			$content = array_shift($contents);
			$p_changeerror = $content->text;
			$content = array_shift($contents); ?>

			function checkform() {
				missinginfo = "";
				var transfersclosed = document.getElementById('transfersclosed_' + dijit.byId('raceid').value).value;
				if (transfersclosed == 'True') {
					missinginfo += "\n<?php echo $p_transfersclosederror; ?>";
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
				if (!dijit.byId('transferid').checked && !dijit.byId('changeid').checked) {
					missinginfo += "\n<?php echo $p_changeerror; ?>";
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
				dijit.byId('raceid').setValue(<?php echo $p_raceid; ?>);
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
						echo $transfersclosed;
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
							<input type="text" name="dob" id="dob" tabindex="4" dojoType="dijit.form.DateTextBox" required="true" promptMessage="<?php echo $content->text; ?>" />
						</div>
						<div class="clear"></div><br />
						<?php
						$content = array_shift($contents); ?>
						<div class="formcaption"><?php echo $content->text; ?></div>
						<div class="formfield"><input name="firstname" type="text" id="firstname" tabindex="5" style="width: 200px;" dojoType="dijit.form.TextBox" propercase="true" /></div>
						<div class="clear"></div><br />
						<input type="radio" value="0" name="change" id="transferid" tabindex="6" dojotype="dijit.form.RadioButton" />
							<?php
							$content = array_shift($contents);
							echo $content->text; ?>
						<input type="radio" value="1" name="change" id="changeid" tabindex="7" dojotype="dijit.form.RadioButton" />
							<?php
							$content = array_shift($contents);
							echo $content->text; ?>
						<div class="clear"></div><br />
						<div class="buttonblock">
							<?php
							$content = array_shift($contents); ?>
							<div class="button"><a href="javascript:window.close();"><?php echo $content->text; ?></a></div>
							<?php
							$content = array_shift($contents); ?>
							<div class="button"><a href="javascript:checkform();"><?php echo $content->text; ?></a></div>
						</div>
						<div class="clear"></div>
						<?php
						$content = array_shift($contents);
						echo $content->text; ?>
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_windowfooter.php"); ?>
			</div>
		</div>
	</body>
</html>