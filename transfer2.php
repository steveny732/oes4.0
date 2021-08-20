<?php
/* ************************************************************************
' transfer2.php -- Transfer/Change a race entry #2 - T&Cs and confirm original entrant details
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

$entry = Entry::findByDetails($_REQUEST['raceid'], $_REQUEST['surname'], $_REQUEST['firstname'], $_REQUEST['postcode'], $_REQUEST['dob']);

if (isset($entry)) {
	$race = Race::findById($entry->raceid);
	$transfer = Transfer::findByEntry($entry->raceid, $entry->id);
}
?>
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
			function formatDate() {
				var pyear = document.form1.dob.value.substring(0, 4);
				var pmonth = parseInt(document.form1.dob.value.substring(5, 7)) - 1;
				var pdate = document.form1.dob.value.substring(8, 10);
				var d = new Date(pyear, pmonth, pdate);
				MM_setTextOfLayer('displaydob', '', dojo.date.locale.format(d, {selector: 'date', formatLength: 'long', locale: '<?php echo $cookieLanguageCode; ?>'}));
			}
			<?php
			if ($entry) {
				if (!$transfer) {
					echo 'dojo.addOnLoad(formatDate);';
				}
			} ?>
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
					$p_transfer = $content->text;
					$content = array_shift($contents);
					$p_offers = $content->text;
					$content = array_shift($contents);
					$p_transferracepackssent = $content->text;
					$content = array_shift($contents);
					$p_transfernoemail = $content->text;
					$content = array_shift($contents);
					$p_transfergotemail = $content->text;
					$content = array_shift($contents);
					$p_transferauthtext = $content->text;
					$content = array_shift($contents);
					$p_change = $content->text;
					$content = array_shift($contents);
					$p_changeracepackssent = $content->text;
					$content = array_shift($contents);
					$p_changeauthtext = $content->text;
					$content = array_shift($contents);
					$p_offertext = $content->text;
					$content = array_shift($contents);
					$p_confirm = $content->text;
					$content = array_shift($contents);
					$p_racenumber = $content->text;
					$content = array_shift($contents);
					$p_name = $content->text;
					$content = array_shift($contents);
					$p_address = $content->text;
					$content = array_shift($contents);
					$p_postcode = $content->text;
					$content = array_shift($contents);
					$p_dob = $content->text;
					$content = array_shift($contents);
					$p_category = $content->text;
					$content = array_shift($contents);
					$p_club = $content->text;
					$content = array_shift($contents);
					$p_cancelbutton = $content->text;
					$content = array_shift($contents);
					$p_confirmbutton = $content->text;
					$content = array_shift($contents);
					$p_continueto3button = $content->text;
					$content = array_shift($contents);
					$p_alreadyoffered = $content->text;
					$content = array_shift($contents);
					$p_nomatch = $content->text;
					if ($entry) {
						if (!$transfer) {
							$daysToGo = round(($race->datenumber() - time()) / 86400) + 1;
							if ($_REQUEST['change'] == 0) {
								echo $p_transfer;
								if ($race->waitinglistenabled) {
									echo $p_offers;
								}
								if ($daysToGo <= 14) {
									echo $p_transferracepackssent;
								}
								if ($entry->email == '*NONE') {
									echo $p_transfernoemail;
								} else {
									echo $p_transfergotemail;
								}
								echo $p_transferauthtext;
				
							} elseif ($_REQUEST['change'] == 1) {
								echo $p_change;
								
								if ($daysToGo <= 14) {
									echo $p_changeracepackssent;
								}
								echo $p_changeauthtext;
								
							} elseif ($_REQUEST['change'] == 2) {
								echo $p_offertext;
							}
							echo $p_confirm; ?><br /><br />
							<div class="formcaption"><?php echo $p_racenumber; ?></div>
							<div class="formfield"><strong><?php echo $entry->racenumber; ?></strong></div>
							<div class="clear"></div>
							<div class="formcaption"><?php echo $p_name; ?></div>
							<div class="formfield"><strong><?php echo $entry->firstname; ?>&nbsp;<?php echo $entry->surname; ?></strong></div>
							<div class="clear"></div>
							<div class="formcaption"><?php echo $p_address; ?></div>
							<div class="formfield"><strong>
								<?php
								echo $entry->address1. '<br />';
								echo $entry->address2. '<br />';
								echo $entry->town. '<br />';
								echo $entry->county; ?></strong>
							</div>
							<div class="clear"></div>
							<div class="formcaption"><?php echo $p_postcode; ?></div>
							<div class="formfield"><strong><?php echo $entry->postcode; ?></strong></div>
							<div class="clear"></div>
							<div class="formcaption"><?php echo $p_dob; ?></div>
							<div class="formfield"><strong><div id="displaydob"></div></strong></div>
							<div class="clear"></div>
							<div class="formcaption"><?php echo $p_category; ?></div>
							<div class="formfield"><strong><?php echo $entry->agecategorycode; ?></strong></div>
							<div class="clear"></div>
							<div class="formcaption">
								<?php echo $p_club; ?>
							</div>
							<div class="formfield"><strong><?php echo $entry->clubname; ?></strong></div>
							<div class="clear"></div><br /><br />
							<form name="form1" method="post" action="transfer<?php if ($_REQUEST['change'] == '2') { echo '4'; } else { echo '3'; } ?>.php">
								<input type="hidden" name="change" value="<?php echo htmlspecialchars($_REQUEST['change'], ENT_QUOTES); ?>" />
								<input type="hidden" name="id" id="id" value="<?php echo $entry->id; ?>">
								<input type="hidden" name="dob" id="dob" value="<?php echo $entry->dob; ?>">
								<input type="hidden" name="formid" id="formid" value="<?php echo $race->formid; ?>">
								<input type="hidden" name="transferno" id="transferno" value="<?php echo date('YmdHis'). createRandomCode(6); ?>" />
								<div class="buttonblock">
									<div class="button">
										<a href="javascript:window.close();"><?php echo $p_cancelbutton; ?></a>
									</div>
									<?php
									if ($_REQUEST['change'] == "2") { ?>
										<div class="button">
											<a href="javascript:document.form1.submit();"><?php echo $p_confirmbutton; ?></a>
										</div>
									<?php
									} else { ?>
										<div class="button">
											<a href="javascript:document.form1.submit();"><?php echo $p_continueto3button; ?></a>
										</div>
									<?php
									} ?>
								</div>
							</form><br />
						<?php
						} else {
							echo $p_alreadyoffered;
						}
					} else {
						$p_urlparams = '';
						if ($_REQUEST['raceid'] != '') {
							$p_urlparams = '?raceid='. htmlspecialchars($_REQUEST['raceid'], ENT_QUOTES);
						}
						if ($_REQUEST['change'] == '2') {
							$p_nomatch = str_replace('*URL', 'offer1.php'. $p_urlparams, $p_nomatch);
						} else {
							$p_nomatch = str_replace('*URL', 'transfer1.php'. $p_urlparams, $p_nomatch);
						}
						echo $p_nomatch; ?><br /><br /><br /><br />
					<?php
					} ?>
				</div>
				<?php
				include(LIB_PATH. "inc_windowfooter.php"); ?>
			</div>
		</div>
	</body>
</html>