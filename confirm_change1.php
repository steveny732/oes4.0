<?php
/* ************************************************************************
' confirm_change1.php -- Confirm change #1 - Entry point
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

$url = $_SERVER['SCRIPT_NAME'];
$url = substr($url, strrpos($url, "/") + 1, strlen($url) - strrpos($url, "/"));

$contents = Content::findByURL($url, $cookieLanguageCode);
$content = array_shift($contents);

if (!empty($_REQUEST['change_code'])) {
	$transfer = Transfer::findByCode($_REQUEST['change_code']);
	
	if ($transfer) {
		$p_raceid = $transfer->raceid;
		$race = Race::findById($p_raceid);
	}
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
				var year = document.form2.dob_year.value;
				var month = document.form2.dob_month.value - 1;
				var day = document.form2.dob_day.value;
				var d = new Date(year, month, day, 0, 0);
				document.form2.displaydob.value = dojo.date.locale.format(d, {selector: 'date', formatLength: 'long', locale: '<?php echo $cookieLanguageCode; ?>'});
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<?php
	$onloadjs = '';
	if (empty($_REQUEST['change_code'])) {
		$onloadjs = 'self.focus();document.form1.change_code.focus();';
	} else {
		if (!empty($_REQUEST['change_code']) &&
			$transfer && !$race->transfersclosed()) {
			if (!$transfer->complete &&
				!$transfer->superceded) {
				$onloadjs .= 'formatDate();';
			}
		}
	} ?>

	<body onload="<?php echo $onloadjs; ?>">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					echo $content->text;
					$content = array_shift($contents);
					$p_transfersclosed = $content->text;
					$content = array_shift($contents);
					$p_codenotvalid = $content->text;
					$content = array_shift($contents);
					$p_changecomplete = $content->text;
					$content = array_shift($contents);
					$p_changesuperceded = $content->text;
					$content = array_shift($contents);
					$p_confirmchange = $content->text;
					$content = array_shift($contents);
					$p_confirmbutton = $content->text;
					$content = array_shift($contents);
					$p_entercode = $content->text;
					$content = array_shift($contents);
					$p_submitbutton = $content->text;
					if ($transfer && $race->transfersclosed()) {
						echo $p_transfersclosed;
					} else {
						if (!empty($_REQUEST['change_code'])) {
							if (!$transfer) {
								echo $p_codenotvalid;
							} else {
								if ($transfer->complete) {
									echo $p_changecomplete;
								} elseif ($transfer->superceded) {
									echo $p_changesuperceded;
								} else {
									echo $p_confirmchange; ?>
									<form name="form2" method="post" action="confirm_change2.php">
										<input type="hidden" name="changeid" id="changeid" value="<?php echo $transfer->id; ?>" />
										<input type="hidden" name="dob_year" id="dob_year" value="<?php echo $transfer->newdobyear(); ?>" />
										<input type="hidden" name="dob_month" id="dob_month" value="<?php echo $transfer->newdobmonth(); ?>" />
										<input type="hidden" name="dob_day" id="dob_day" value="<?php echo $transfer->newdobday(); ?>" />
										<input type="hidden" name="displaydob" id="displaydob" value="" />
										<div class="buttonblock">
											<div class="button"><a href="javascript:document.form2.submit();"><?php echo $p_confirmbutton; ?></a></div>
										</div>
									</form><br />
								<?php
								}
							}
						} else {?>
							<div class="clear"></div><br /><br />
							<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
								<div class="formcaption"><?php echo $p_entercode; ?></div>
								<div class="formfield"><input name="change_code" type="text" id="change_code" /></div>
								<div class="clear"></div><br /><br />
								<div class="buttonblock">
									<div class="button"><a href="javascript:document.form1.submit();"><?php echo $p_submitbutton; ?></a></div>
								</div>
								<input type="hidden" name="raceid" value="<?php echo htmlspecialchars($_REQUEST['raceid'], ENT_QUOTES); ?>" />
							</form>
						<?php
						}
					} ?>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>