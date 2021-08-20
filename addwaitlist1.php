<?php
/* ************************************************************************
' admin_addwaitlist1.php -- Add a waiting list entry #1
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
'		betap 2008-06-29 15:40:00
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
			dojo.require("dijit.form.FilteringSelect");

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
			
			dojo.addOnLoad(function() {
				loadRaces();
				dojo.parser.parse();
				dijit.byId('agree').focus();
				toggleRace();
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
					$content = array_shift($contents);
					echo $content->text;
					$content = array_shift($contents);
					?>
					<form method="post" name="form1" action="addwaitlist2.php">
						<div class="formcaption">
							<?php
							echo $content->text;
							$content = array_shift($contents); ?>
						</div>
						<div class="formfield">
							<select name="raceid" id="raceid" onchange="toggleRace();" style="width: 300px;" dojotype="dijit.form.FilteringSelect">
								<?php
								$savedMinimumAges = '';
								$savedRaceDates = '';
								$savedForms = '';
								$JS = '<script type="text/javascript">function loadRaces(){var d=new Date();var strText;var i=0;';
								$p_raceid = htmlspecialchars($_REQUEST['raceid'], ENT_QUOTES);
								foreach ($races as $race) {
									$noofentries = Entry::countAllPaid($p_raceid);
									$noofpreallocations = Preallocation::countAllUnused($p_raceid);
									$numberleft = $race->limit - $noofentries - $noofpreallocations;
									if ($numberleft <= 0) {
										if (empty($p_raceid)) {
											$p_raceid = $race->id;
										}
										$JS .= 'd.setFullYear('. $race->year(). ','. ($race->month() - 1). ','. $race->day(). ');strText=dojo.date.locale.format(d,{selector:\'date\',formatLength:\'medium\',locale:\''. $cookieLanguageCode. '\'}) + \' '. $race->name. '\';document.form1.raceid.options[i++]=new Option(strText,'. $race->id. ');';
										$savedMinimumAges .= '<input type="hidden" name="age_'. $race->id. '" id="age_'. $race->id. '" value="'. $race->minimumage. '" />';
										$savedRaceDates .= '<input type="hidden" name="year_'. $race->id. '" id="year_'. $race->id. '" value="'. $race->year(). '" />';
										$savedRaceDates .= '<input type="hidden" name="month_'. $race->id. '" id="month_'. $race->id. '" value="'. $race->month(). '" />';
										$savedRaceDates .= '<input type="hidden" name="day_'. $race->id. '" id="day_'. $race->id. '" value="'. $race->day(). '" />';
										$savedForms .= '<input type="hidden" name="form_'. $race->id. '" id="form_'. $race->id. '" value="'. $race->formid. '" />';
										$savedForms .= '<input type="hidden" name="clubtype_'. $race->id. '" id="clubtype_'. $race->id. '" value="'. $race->clubtypeid. '" />';
									}
								}
								$JS .= '}</script>'; ?>
							</select>
							<?php
							echo $JS;
							echo $savedMinimumAges;
							echo $savedRaceDates;
							echo $savedForms; ?>
						</div><br /><br /><br />
						<div class="clear"></div>
						<?php
						echo $content->text;
						$content = array_shift($contents); ?>
						<div id="agemessage"></div><br />
						<input type="checkbox" name="agree" id="agree" value="1" dojotype="dijit.form.CheckBox">&nbsp;<label for="agree"><?php echo $content->text; ?></label><br /><br />
						<input type="hidden" name="formid" value="">
						<input type="hidden" name="clubtypeid" value="">
						<div class="buttonblock">
							<?php
							$content = array_shift($contents); ?>
							<div class="button"><a href="javascript:window.close();"><?php echo $content->text; ?></a></div>
							<?php
							$content = array_shift($contents); ?>
							<div class="button"><a href="javascript:checkform();"><?php echo $content->text; ?></a></div>
						</div>
						<div class="clear"></div>
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_windowfooter.php"); ?>
			</div>
		</div>
	</body>
</html>
<?php
$content = array_shift($contents); ?>
<script type="text/javascript">
	function toggleRace() {
		dijit.byId('raceid').setValue(<?php echo $p_raceid; ?>);
		var minimumage = document.getElementById("age_" + dijit.byId('raceid').value).value;
		var raceyear = document.getElementById("year_" + dijit.byId('raceid').value).value;
		var racemonth = document.getElementById("month_" + dijit.byId('raceid').value).value - 1;
		var raceday = document.getElementById("day_" + dijit.byId('raceid').value).value;
		var d = new Date(raceyear, racemonth, raceday);
		var strText = dojo.date.locale.format(d, {selector:'date',formatLength:'long',locale:'<?php echo $cookieLanguageCode; ?>'});
		if (minimumage < 16) {
			MM_setTextOfLayer('agemessage', '', '<?php echo $content->text; ?>');
		} else {
<?php
$content = array_shift($contents);
$p_text = '<br />'. $content->text;
$p_text = str_replace('*MINIMUMAGE', "' + minimumage + '", $p_text);
$p_text = str_replace('*RACEDATE', "' + strText + '", $p_text);
?>
			MM_setTextOfLayer('agemessage', '', '<?php echo $p_text; ?>');
		}
		document.form1.formid.value = document.getElementById("form_" + dijit.byId('raceid').value).value
		document.form1.clubtypeid.value = document.getElementById("clubtype_" + dijit.byId('raceid').value).value
	}
</script>