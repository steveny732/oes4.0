<?php
/* ************************************************************************
' confirm_waitlistentry1.php -- Confirm waiting list acceptance #1 - Entry point
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

if (!empty($_REQUEST['transferid']) && !empty($_REQUEST['waitlist_code'])) {
	$transfer = Transfer::findForWaitingList($_REQUEST['transferid'], $_REQUEST['waitlist_code']);
	
	if ($transfer) {
		$p_raceid = $transfer->raceid;
		$race = Race::findById($p_raceid);
	}
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript">
			function checkform() {
				missinginfo = "";
				if (!isNumeric(document.form1.waitlist_code.value)) {
					missinginfo += "\n<?php echo $content->text; ?>";
					<?php
					$content = array_shift($contents); ?>
				} 
				if (!isNumeric(document.form1.transferid.value)) {
					missinginfo += "\n<?php echo $content->text; ?>";
					<?php
					$content = array_shift($contents); ?>
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

	<?php
	$onloadjs = '';
	if (empty($_REQUEST['waitlist_code'])) {
		$onloadjs = 'self.focus();document.form1.waitlist_code.focus();';
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
					$p_alreadyconfirmed = $content->text;
					$content = array_shift($contents);
					$p_confirmacceptance = $content->text;
					$content = array_shift($contents);
					$p_confirmbutton = $content->text;
					$content = array_shift($contents);
					$p_entercode = $content->text;
					$content = array_shift($contents);
					$p_enterid = $content->text;
					$content = array_shift($contents);
					$p_submitbutton = $content->text;
					if ($transfer && $race->transfersclosed()) {
						echo $p_transfersclosed;
					} else {
						if (!empty($_REQUEST['waitlist_code'])) {
							if (!$transfer) {
								echo $p_codenotvalid;
							} else {
								if ($transfer->paid) {
									echo $p_alreadyconfirmed;
								} else {
									echo$p_confirmacceptance; ?>
									<form name="form2" method="post" action="confirm_waitlistentry2.php">
										<input name="waitlistid" type="hidden" id="waitlistid" value="<?php echo $transfer->offertoentryid; ?>" />
										<div class="buttonblock">
											<div class="button"><a href="javascript:document.form2.submit();"><?php echo $p_confirmbutton; ?></a></div>
										</div>
									</form><br />
								<?php
								}
							}
						} else { ?>
							<div class="clear"></div><br /><br />
							<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
								<div class="formcaption"><?php echo $p_entercode; ?></div>
								<div class="formfield"><input name="waitlist_code" type="text" id="waitlist_code" /></div>
								<div class="clear"></div>
								<div class="formcaption"><?php echo $p_enterid; ?></div>
								<div class="formfield"><input name="transferid" type="text" id="transferid" /></div>
								<div class="clear"></div><br /><br />
								<div class="buttonblock">
									<div class="button"><a href="javascript:checkform();"><?php echo $p_submitbutton; ?></a></div>
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