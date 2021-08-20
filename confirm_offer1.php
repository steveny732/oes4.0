<?php
/* ************************************************************************
' confirm_offer1.php -- Confirm offer #1 - Entry point
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

if (!empty($_REQUEST['offer_code'])) {
	$transfer = Transfer::findByCode($_REQUEST['offer_code']);
	$p_invalidCode = false;
	$p_transfersAreRevoked = false;
	$p_transferIsReady = false;

	if (isset($transfer)) {
		if ($transfer->offerrevoked ||
			$transfer->superceded) {
			$p_transfersAreRevoked = true;
		}
		if ($transfer->offerready) {
			$p_transferIsReady = true;
		}
		$p_transfercode = $transfer->code;
		$p_raceid = $transfer->raceid;
		$race = Race::findById($p_raceid);
	} else {
		$p_invalidCode = true;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
		<!--[if IE 6]>
		<style>
		#innerwrapper { zoom: 1; }
		</style>
		<![endif]-->
	</head>

	<?php
	$onloadjs = '';
	if (empty($_REQUEST['offer_code'])) {
		$onloadjs = 'self.focus();document.form1.offer_code.focus();';
	} ?>

	<body onload="<?php echo $onloadjs; ?>">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div id="innerwrapper">
					<div class="windowcontentcolumn0">
						<?php
						echo $content->text;
						$content = array_shift($contents);
						$p_transfersclosed = $content->text;
						$content = array_shift($contents);
						$p_codenotvalid = $content->text;
						$content = array_shift($contents);
						$p_offerremoved = $content->text;
						$content = array_shift($contents);
						$p_offerconfirmed = $content->text;
						$content = array_shift($contents);
						$p_confirmoffer = $content->text;
						$content = array_shift($contents);
						$p_confirmbutton = $content->text;
						$content = array_shift($contents);
						$p_entercode = $content->text;
						$content = array_shift($contents);
						$p_submitbutton = $content->text;
						if (isset($transfer) && $race->transfersclosed()) {
							echo $p_transfersclosed;
						} else {
							if (!empty($_REQUEST['offer_code'])) {
								if ($p_invalidCode) {
									echo $p_codenotvalid;
								} elseif ($p_transfersAreRevoked) {
									echo $p_offerremoved;
								} else {
									if ($p_transferIsReady) {
										echo $p_offerconfirmed;
									} else {
										echo $p_confirmoffer; ?>
										<form name="form2" method="post" action="confirm_offer2.php">
											<input name="offerid" type="hidden" id="offerid" value="<?php echo $p_transfercode; ?>" />
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
									<div class="formfield"><input name="offer_code" type="text" id="offer_code" /></div>
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
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>