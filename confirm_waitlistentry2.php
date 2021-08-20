<?php
/* ************************************************************************
' confirm_waitlistentry2.php -- Confirm waiting list acceptance #2 - Perform update & begin payment process
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
' Version: 1.0
' Last Modified By:
'  betap 2008-07-16 18:30:12
'   Use site title rather than race name (as multiple races might have been entered).
'  betap 2008-06-29 21:22:00
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

$entry = Entry::findById($_REQUEST['waitlistid']);

$race = Race::findById($entry->raceid);

$sitepayments = SitePayment::findAllForSite($currentSite->id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<?php
		$dojoParse = "false";
		include(LIB_PATH. 'dojo.php'); ?>
		<script type="text/javascript">
			dojo.require("dojo.date.locale");
			dojo.require("dojo.currency");
			dojo.require("dojo.parser");

			dojo.addOnLoad(function() {
				loadValues();
				dojo.parser.parse();
			});
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body>
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<form name="formdummy" id="formdummy" method="post" action="enter4.php">
						<input type="hidden" name="currency" id="currency" value="<?php echo $currentSite->currencycode; ?>" />
						<?php
						echo $content->text;
						$content = array_shift($contents);
						$JS = '<script type="text/javascript">function loadValues(){';
						$totalcost = 0; ?>
						<input type="hidden" id="entryTotal_<?php echo $entry->id; ?>" value="<?php echo $entry->total; ?>" />
						<div class="sumcaption">
							<?php
							echo $entry->firstname. '&nbsp;'. $entry->surname;
							echo '<br />'. $race->name; ?>
						</div>
						<div class="sumfield" id="displayEntryTotal_<?php echo $entry->id; ?>"></div>
						<div class="clear"></div>
						<?php
						$JS .= "MM_setTextOfLayer('displayEntryTotal_". $entry->id. "', '', '<br /><strong>' + dojo.currency.format(document.formdummy.entryTotal_". $entry->id. ".value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');";
						$totalcost += $entry->total;
						
						$p_address = $entry->address1;
						if (!empty($entry->address2)) {
							$p_address .= CRLF. $entry->address2;
						}
						if (!empty($entry->town)) {
							$p_address .= CRLF. $entry->town;
						} ?>
						<br />
						<div class="sumcaption"><strong><?php echo $content->text; ?></strong></div>
						<?php
						$content = array_shift($contents); ?>
						<input type="hidden" id="totalCost" value="<?php echo $totalcost; ?>" />
						<div class="sumfield" id="displayTotalCost"></div>
						<?php
						$JS .= "MM_setTextOfLayer('displayTotalCost', '', '<strong>' + dojo.currency.format(document.formdummy.totalCost.value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');" ?>
						<div class="clear"></div><br />
						<?php
						echo $content->text. ' '. $entry->ordernumber;
						$content = array_shift($contents); ?>
						<div class="clear"></div><br />
						<?php
						$p_paybutton = $content->text;
						$content = array_shift($contents); ?>
					</form>
					<?php
					$p_noproviders = $content->text;
					$content = array_shift($contents);
					if (!$sitepayments) {
						echo $p_noproviders;
					} else { ?>
						<div class="buttonblock">
							<?php
							foreach ($sitepayments as $sitepayment) {
								if ($sitepayment->type == 1) {
									if ($sitepayment->merchantid == '*NONE') {
										$nochexURL = 'test_apc.php';
									} else {
										$nochexURL = 'https://secure.nochex.com/';
									} ?>
									<form action="<?php echo $nochexURL; ?>" method="post" name="form<?php echo $sitepayment->type; ?>">
										<input name="merchant_id" type="hidden" value="<?php echo $sitepayment->merchantid; ?>" />
										<input name="amount" type="hidden" value="<?php echo number_format($totalcost, 2); ?>" />
										<input name="order_id" type="hidden" value="<?php echo $entry->ordernumber; ?>" />
										<input name="description" type="hidden" value="<?php echo $currentSite->title; ?> Entry Fee" />
										<input name="success_url" type="hidden" value="<?php echo $currentSite->url; ?>/payment_confirmed.php" />
										<input name="cancel_url" type="hidden" value="<?php echo $currentSite->url; ?>/payment_cancelled.php" />
										<input name="billing_fullname" type="hidden" value="<?php echo $entry->firstname. ' '. $entry->surname; ?>" />
										<input name="billing_address" type="hidden" value="<?php echo $p_address; ?>" />
										<input name="billing_postcode" type="hidden" value="<?php echo $entry->postcode; ?>" />
										<input name="email_address" type="hidden" value="<?php echo $entry->email; ?>" />
										<input name="customer_phone_number" type="hidden" value="<?php echo $entry->telday; ?>" />
										<input name="callback_url" type="hidden" value="<?php echo $currentSite->url; ?>/nochex_apc.php" />
										<div class="button"><a href="javascript: document.form<?php echo $sitepayment->type; ?>.submit();"><?php echo str_replace('*PAYTYPE', $sitepayment->name, $p_paybutton); ?></a></div>
										<?php
										if ($race->testmode) { ?>
											<input name="test_transaction" type="hidden" value="100" />
											<input name="test_success_url" type="hidden" value="<?php echo $currentSite->url; ?>/payment_confirmed.php" />
										<?php
										} ?>
									</form>
								<?php
								} elseif ($sitepayment->type == 2) {
									if ($sitepayment->merchantid == '*NONE') {
										$paypalURL = 'test_apc.php';
									} else {
										if ($race->testmode) {
											$paypalURL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
										} else {
											$paypalURL = 'https://www.paypal.com/cgi-bin/webscr';
										}
									} ?>
									<form action="<?php echo $paypalURL; ?>" method="post" name="form<?php echo $sitepayment->type; ?>">
										<input name="cmd" type="hidden" value="_xclick" />
										<input name="charset" type="hidden" value="utf-8" />
										<input name="currency_code" type="hidden" value="<?php echo $currentSite->currencycode; ?>" />
										<input name="business" type="hidden" value="<?php echo $sitepayment->merchantid; ?>" />
										<input name="amount" type="hidden" value="<?php echo number_format($totalcost, 2) ?>" />
										<input name="quantity" type="hidden" value="1" />
										<input name="item_number" type="hidden" value="<?php echo $entry->ordernumber; ?>" />
										<input name="item_name" type="hidden" value="<?php echo $currentSite->title; ?> Entry Fee" />
										<input name="first_name" type="hidden" value="<?php echo $entry->firstname; ?>" />
										<input name="last_name" type="hidden" value="<?php echo $entry->surname; ?>" />
										<input name="address1" type="hidden" value="<?php echo $entry->address1; ?>" />
										<input name="address2" type="hidden" value="<?php echo $entry->address2; ?>" />
										<input name="city" type="hidden" value="<?php echo $entry->town; ?>" />
										<input name="state" type="hidden" value="<?php echo $entry->county; ?>" />
										<input name="zip" type="hidden" value="<?php echo $entry->postcode; ?>" />
										<input name="email" type="hidden" value="<?php echo $entry->email; ?>" />
										<input name="night_phone_b" type="hidden" value="<?php echo $entry->telday; ?>" />
										<input name="return" type="hidden" value="<?php echo $currentSite->url; ?>/payment_confirmed.php" />
										<input name="cancel_return" type="hidden" value="<?php echo $currentSite->url; ?>/payment_cancelled.php" />
										<input name="notify_url" type="hidden" value="<?php echo $currentSite->url; ?>/paypal_ipn.php" />
										<div class="button"><a href="javascript: document.form<?php echo $sitepayment->type; ?>.submit();"><?php echo str_replace('*PAYTYPE', $sitepayment->name, $p_paybutton); ?></a></div>
									</form>
								<?php
								}
							} ?>
						</div>
					<?php
					} ?>
					<div class="clear"></div>
					<?php
					$p_message = $content->text;
					$p_message = str_replace('*TOTAL', '<span id="totalMessage"></span>', $p_message);
					$JS .= "MM_setTextOfLayer('totalMessage', '', '<strong>' + dojo.currency.format(document.formdummy.totalCost.value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');";
					$p_message = str_replace('*RACEPAYEE', $race->payee, $p_message);
					$p_message = str_replace('*RACEADDRESS', $race->postaladdress, $p_message);
					$p_message = str_replace('*ORDERNUMBER', $entry->ordernumber, $p_message);
					echo $p_message; ?>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
	<?php
	$JS .= '}</script>';
	echo $JS; ?>
</html>