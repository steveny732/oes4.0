<?php
/* ************************************************************************
' enter4.php -- Public Entry Process #4 - Payment
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
'  betap 2008-07-16 18:28:10
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

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
	$entry = Entry::findByIdForUpdate($_REQUEST['id']);
	$entry->paid = 0;
	$entry->racenumber = 0;
	$entry->deleted = 1;
	$entry->save();
}

$contents = Content::findByURL($url, $cookieLanguageCode);
$content = array_shift($contents);

$entries = Entry::findByOrderNumberNotPaid($_SESSION['ordernumber']);

$sitepayments = SitePayment::findAllForSite($currentSite->id);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<?php
		$dojoParse = 'false';
		include(LIB_PATH. 'dojo.php'); ?>
		<script type="text/javascript">
			dojo.require("dojo.date.locale");
			dojo.require("dojo.currency");
			dojo.require("dojo.parser");

			dojo.addOnLoad(function() {
				loadValues();
				dojo.parser.parse();
				window.opener.location.reload();
			});
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body>
		<div id="pagecontainer">
			<div id="windowcontainer">
				<div class="windowcontentcolumn0">
					<form name="formdummy" id="formdummy" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<input type="hidden" name="currency" id="currency" value="<?php echo $currentSite->currencycode; ?>" />
						<?php
						echo $content->text;
						$content = array_shift($contents);
						$p_emptybasket = $content->text;
						$content = array_shift($contents);
						$p_delete = $content->text;
						$content = array_shift($contents);
						$JS = '<script type="text/javascript">function loadValues(){';
						$totalcost = 0;
						$singlerace = true;
						$p_racename = '';
						$paytext = $currentSite->title;
						if (!$entries) { ?>
							<div class="sumcaption"><br /><strong><?php echo $p_emptybasket; ?></strong></div>
							<div class="clear"></div><br />
							<?php
							// if all entries have been deleted, re-default the race details to the first on file
							$races = Race::findAllFuture($currentSite->id, $cookieLanguageCode);
							$race = array_shift($races);
							$p_raceid = $race->id;
							$p_firstraceid = $p_raceid;
							$p_formid = $race->formid;
							$p_clubtypeid = $race->clubtypeid;
						} else {
							foreach ($entries as $entry) {
								$p_raceid = $entry->raceid;
								if (empty($p_firstraceid)) {
									$p_firstraceid = $p_raceid;
								} else {
									if ($p_firstraceid != $p_raceid) {
										$singlerace = false;
									}
								}
								$race = Race::findByIdWithFormData($p_raceid);
								$p_racename = $race->name;
								$p_formid = $race->formid;
								$p_clubtypeid = $race->clubtypeid; ?>
								<input type="hidden" id="entryTotal_<?php echo $entry->id; ?>" value="<?php echo $entry->total; ?>" />
								<div class="sumcaption">
									<?php
									echo $entry->firstname. '&nbsp;'. $entry->surname;
									echo '<br />'. $race->name; ?>
								</div>
								<div class="sumfield" id="displayEntryTotal_<?php echo $entry->id; ?>"></div><br />
								<div class="sumfield" style="width: 7%;"><a href="enter4.php?action=delete&id=<?php echo $entry->id; ?>"><?php echo $p_delete; ?></a></div>
								<div class="clear"></div>
								<?php
								$JS .= "MM_setTextOfLayer('displayEntryTotal_". $entry->id. "', '', '<br /><strong>' + dojo.currency.format(document.formdummy.entryTotal_". $entry->id. ".value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');";
								$totalcost += $entry->total;
							}

							if ($singlerace) {
								$paytext .= ' '. $p_racename;
							}
							$entry = $entries[0];
							$p_address = $entry->address1;
							if (!empty($entry->address2)) {
								$p_address .= CRLF. $entry->address2;
							}
							if (!empty($entry->town)) {
								$p_address .= CRLF. $entry->town;
							}
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
						echo $content->text. ' '. $_SESSION['ordernumber'];
						$content = array_shift($contents); ?>
						<div class="clear"></div><br />
						<?php
						if ($totalcost > 0.00) {
							echo $content->text;
						} ?>
					</form>
					<?php
					$content = array_shift($contents);
					$p_paybutton = $content->text;
					$content = array_shift($contents);
					$p_noproviders = $content->text;
					$content = array_shift($contents);
					$p_anotherentry = $content->text;
					$content = array_shift($contents);
					$p_message = $content->text;
					$content = array_shift($contents);
					$p_acceptentry = $content->text;
					$content = array_shift($contents);
					$p_nopaymentmessage = $content->text;

					if (!$sitepayments) {
						echo $p_noproviders;
					} else { ?>
						<div class="buttonblock">
							<?php
							if ($totalcost > 0.00) {
								foreach ($sitepayments as $sitepayment) {
									if ($sitepayment->type == 1 && $entries) {
										if ($sitepayment->merchantid == "*NONE") {
											$nochexURL = 'test_apc.php';
										} else {
											$nochexURL = 'https://secure.nochex.com/';
										} ?>
										<form action="<?php echo $nochexURL; ?>" method="post" name="form<?php echo $sitepayment->type; ?>">
											<input name="merchant_id" type="hidden" value="<?php echo $sitepayment->merchantid; ?>" />
											<input name="amount" type="hidden" value="<?php echo number_format($totalcost, 2); ?>" />
											<input name="order_id" type="hidden" value="<?php echo $entry->ordernumber; ?>" />
											<input name="description" type="hidden" value="<?php echo $paytext; ?> Entry Fee" />
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
									} elseif ($sitepayment->type == 2 && $entries) {
										if ($sitepayment->merchantid == "*NONE") {
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
											<input name="item_name" type="hidden" value="<?php echo $paytext; ?> Entry Fee" />
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
									} elseif ($sitepayment->type == 3 && $entries) {
										if ($sitepayment->merchantid == "*NONE") {
											$vTPVURL = 'test_apc.php';
										} else {
											if ($race->testmode) {
												$vTPVURL = 'test URL for sermepa';
											} else {
												$vTPVURL = 'https://sis.sermepa.es/sis/realizarPago';
											}
										}
										$shaKey = '1234567890123456';			// Key provided by bank
										$userName = 'your name';					// Merchant's name
										$name = 'NAME';										// Name that appears on the transaction
										$code = '123456789';							// Commercial code provided by bank
										$terminal = '123';								// Terminal used
										$currency = '978';								// Currency (978=Euros)
										$typeOfOperation = '0';						// Type of operation. 0=authorisation
										$order = substr($entry->ordernumber, 10, 10);
										$amount = $totalcost * 100;
										$url = $currentSite->url;
	
										$signature = sha1($amount. $order. $code. $currency. $url. $typeOfOperation. $shaKey);
	?>
										<form action="<?php echo $vTPVURL; ?>" method="post" name="form<?php echo $sitepayment->type; ?>">
											<input type="hidden" name="Ds_Merchant_Amount" value="<?php echo $amount; ?>" />
											<input type="hidden" name="Ds_Merchant_Currency" value="<?php echo $currency; ?>" />
											<input type="hidden" name="Ds_Merchant_Order" value="<?php echo $order; ?>" />
											<input type="hidden" name="Ds_Merchant_ProductDescription" value="<?php echo $paytext; ?> Entry Fee" />
											<input type="hidden" name="Ds_Merchant_Titular" value="<?php echo $userName; ?>" />
											<input type="hidden" name="Ds_Merchant_MerchantCode" value="<?php echo $code; ?>" />
											<input type="hidden" name="Ds_Merchant_MerchantURL" value="<?php echo $url; ?>" />
											<input type="hidden" name="Ds_Merchant_UrlOK" value="<?php echo $currentSite->url; ?>/tpv.php" />
											<input type="hidden" name="Ds_Merchant_UrlKO" value="<?php echo $currentSite->url; ?>/payment_cancelled.php" />
											<input type="hidden" name="Ds_Merchant_MerchantName" value="<?php echo $sitepayment->merchantid; ?>" />
											<input type="hidden" name="Ds_Merchant_ConsumerLanguage" value="001" />
											<input type="hidden" name="Ds_Merchant_MerchantSignature" value="<?php echo $signature; ?>" />
											<input type="hidden" name="Ds_Merchant_Terminal" value="<?php echo $terminal; ?>" />
											<input type="hidden" name="Ds_Merchant_TransactionType" value="<?php echo $typeOfOperation ;?>" />
											<div class="button"><a href="javascript: document.form<?php echo $sitepayment->type; ?>.submit();"><?php echo str_replace('*PAYTYPE', $sitepayment->name, $p_paybutton); ?></a></div>
										</form>
									<?php
									}
								} ?>
							<?php
							} else { ?>
								<form action="test_apc.php" method="post" name="formnopayment">
									<input name="item_number" type="hidden" value="<?php echo $entry->ordernumber; ?>" />
									<div class="button"><a href="javascript: document.formnopayment.submit();"><?php echo $p_acceptentry; ?></a></div>
								</form>
							<?php
							} ?>
							<div class="button"><a href="enter2.php?raceid=<?php echo $p_firstraceid; ?>&formid=<?php echo $p_formid; ?>&clubtypeid=<?php echo $p_clubtypeid; ?>"><?php echo $p_anotherentry; ?></a></div>
						</div>
					<?php
					} ?>
					<div class="clear"></div>
					<?php
					if ($totalcost > 0.00) {
						if (!empty($race->payee)) {
							$p_message = str_replace('*TOTAL', '<span id="totalMessage"></span>', $p_message);
							$JS .= "MM_setTextOfLayer('totalMessage', '', '<strong>' + dojo.currency.format(document.formdummy.totalCost.value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');";
							$p_message = str_replace('*RACEPAYEE', $race->payee, $p_message);
							$p_message = str_replace('*RACEADDRESS', $race->postaladdress, $p_message);
							$p_message = str_replace('*ORDERNUMBER', $_SESSION['ordernumber'], $p_message);
							echo $p_message;
						}
					} else {
						echo $p_nopaymentmessage;
					} ?><br /><br />
				</div>
				<?php
				include(LIB_PATH. "inc_windowfooter.php"); ?>
			</div>
		</div>
	</body>
	<?php
	$JS .= '}</script>';
	echo $JS; ?>
</html>