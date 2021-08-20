<?php
/* ************************************************************************
' admin_summary.php -- Administration - Reconciliation
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
'  betap 2008-07-16 08:17:47
'		New script.
'
' ************************************************************************ */
require_once("includes/initialise.php");
require_once(LIB_PATH. "content.php");

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$js = '<script type="text/javascript">var siteCurrency = document.form1.currency.value;';

$entrytotals = PayHistory::findTotals('entry');
$entrytotal = $entrytotals->total;
$entrycharge = $entrytotals->charge * -1;

$othertotals = PayHistory::findTotals('other');
$othertotal = $othertotals->total;
$othercharge = $othertotals->charge * -1;

$banked = PayHistory::findBanked();
$balance = PayHistory::findBalance();
$chequetotal = Entry::findOverallChequeTotal();
$discounttotal = Entry::findDiscountTotal();

$grandRaceFees = 0;
$grandOnlineFees = 0;
$grandAffilFees = 0;
$grandCharityFees = 0;
$grandRaceTotal = 0;
$grandRaceOnlineIncome = 0;
$grandRaceChequeIncome = 0;
$grandRaceActual = 0;
$grandRaceDifference = 0;
$grandRaceOnlineCommission = 0;
$grandRaceDiscounts = 0;
$grandRaceActualIncome = 0;

$JS = '<script type="text/javascript">function loadValues(){';
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
			dojo.require("dojo.currency");
			
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
					<div class="floatleft">
						<h2>Financial Reconciliation</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						financial reconciliation<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<h2>Summary</h2>
					<form method="post" name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="sumcaption">Online Income (entries)</div>
						<div class="sumfield" id="displayEntryIncomeTotal"></div>
						<input type="hidden" name="entryIncomeTotal" id="entryIncomeTotal" value="<?php echo $entrytotal; ?>" />
						<?php
						$JS .= "MM_setTextOfLayer('displayEntryIncomeTotal', '', dojo.currency.format(document.form1.entryIncomeTotal.value, {currency: '". $currentSite->currencycode. "'}));"; ?>
						<div class="clear"></div>
						<div class="sumcaption">Online Income (other)</div>
						<div class="sumfield" id="displayOtherIncomeTotal"></div>
						<input type="hidden" name="otherIncomeTotal" id="otherIncomeTotal" value="<?php echo $othertotal; ?>" />
						<?php
						$JS .= "MM_setTextOfLayer('displayOtherIncomeTotal', '', dojo.currency.format(document.form1.otherIncomeTotal.value, {currency: '". $currentSite->currencycode. "'}));"; ?>
						<div class="clear"></div>
						<div class="sumcaption"><strong>Total Online Income</strong></div>
						<div class="sumfield" id="displayIncomeTotal"></div>
						<input type="hidden" name="incomeTotal" id="incomeTotal" value="<?php echo $entrytotal + $othertotal; ?>" />
						<?php
						$JS .= "MM_setTextOfLayer('displayIncomeTotal', '', '<strong>' + dojo.currency.format(document.form1.incomeTotal.value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');"; ?>
						<div class="clear"></div><br />
						<div class="sumcaption">Online Charges (entries)</div>
						<div class="sumfield" id="displayPaytypeEntryCharges"></div>
						<input type="hidden" name="paytypeEntryCharges" id="paytypeEntryCharges" value="<?php echo $entrycharge; ?>" />
						<?php
						$JS .= "MM_setTextOfLayer('displayPaytypeEntryCharges', '', dojo.currency.format(document.form1.paytypeEntryCharges.value, {currency: '". $currentSite->currencycode. "'}));"; ?>
						<div class="clear"></div>
						<div class="sumcaption">Online Charges (other)</div>
						<div class="sumfield" id="displayPaytypeOtherCharges"></div>
						<input type="hidden" name="paytypeOtherCharges" id="paytypeOtherCharges" value="<?php echo $othercharge; ?>" />
						<?php
						$JS .= "MM_setTextOfLayer('displayPaytypeOtherCharges', '', dojo.currency.format(document.form1.paytypeOtherCharges.value, {currency: '". $currentSite->currencycode. "'}));"; ?>
						<div class="clear"></div>
						<div class="sumcaption">Banked from Online System</div>
						<div class="sumfield" id="displayPaytypeBanked"></div>
						<input type="hidden" name="paytypeBanked" id="paytypeBanked" value="<?php echo $banked; ?>" />

						<div class="clear"></div>
						<div class="sumcaption"><strong>Total Online Outgoings</strong></div>
						<div class="sumfield" id="displayOutgoingsTotal"></div>
						<input type="hidden" name="outgoingsTotal" id="outgoingsTotal" value="<?php echo $entrycharge + $othercharge + $banked; ?>" />
						<?php
						$JS .= "MM_setTextOfLayer('displayOutgoingsTotal', '', '<strong>' + dojo.currency.format(document.form1.outgoingsTotal.value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');";
						$JS .= "MM_setTextOfLayer('displayPaytypeBanked', '', dojo.currency.format(document.form1.paytypeBanked.value, {currency: '". $currentSite->currencycode. "'}));"; ?>
						<div class="clear"></div><br />
						<div class="sumcaption"><strong>Online Balance</strong></div>
						<div class="sumfield" id="displayPaytypeCalcBal"></div>
						<div class="sumfield" id="displayPaytypeActBal"></div>
						<input type="hidden" name="paytypeCalcBal" id="paytypeCalcBal" value="<?php echo ($entrytotal + $othertotal + $entrycharge + $othercharge + $banked) * -1; ?>" />
						<input type="hidden" name="paytypeActBal" id="paytypeActBal" value="<?php echo $balance; ?>" />
						<?php
						$JS .= "MM_setTextOfLayer('displayPaytypeCalcBal', '', '<strong>' + dojo.currency.format(document.form1.paytypeCalcBal.value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');";
						$JS .= "MM_setTextOfLayer('displayPaytypeActBal', '', '(' + dojo.currency.format(document.form1.paytypeActBal.value, {currency: '". $currentSite->currencycode. "'}) + ')');"; ?>
						<div class="clear"></div><br />
						<div class="sumcaption"><strong>Cheque Entries</strong></div>
						<div class="sumfield" id="displayChequeTotal"></div>
						<input type="hidden" name="chequeTotal" id="chequeTotal" value="<?php echo $chequetotal; ?>" />
						<?php
						$JS .= "MM_setTextOfLayer('displayChequeTotal', '', '<strong>' + dojo.currency.format(document.form1.chequeTotal.value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');"; ?>
						<div class="clear"></div><br />
						<div class="sumcaption"><strong>Net Income</strong></div>
						<div class="sumfield" id="displayOverallTotal"></div>
						<input type="hidden" name="overallTotal" id="overallTotal" value="<?php echo $entrytotal + $othertotal + $entrycharge + $othercharge + $chequetotal; ?>" />
						<?php
						$JS .= "MM_setTextOfLayer('displayOverallTotal', '', '<strong>' + dojo.currency.format(document.form1.overallTotal.value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');"; ?>
						<div class="clear"></div><br />
						<div class="sumcaption"><strong>Net Income (entries only)</strong></div>
						<div class="sumfield" id="displayNetEntriesTotal"></div>
						<input type="hidden" name="netEntriesTotal" id="netEntriesTotal" value="<?php echo $entrytotal + $entrycharge + $chequetotal; ?>" />
						<?php
						$JS .= "MM_setTextOfLayer('displayNetEntriesTotal', '', '<strong>' + dojo.currency.format(document.form1.netEntriesTotal.value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');"; ?>
						<div class="clear"></div>
						<?php
						// Now read all open/future races
						$races = Race::findAllFuture($currentSite->id, $cookieLanguageCode);

						// Race entry analysis
						foreach ($races as $race) {
							$p_raceid = $race->id;
							echo '<h2>'. $race->name. ' Entry Analysis</h2>';

							$totalentries = Entry::countAllPaid($p_raceid);
							$totalonline = Entry::countAllOnline($p_raceid);
							$totalnonaffil = Entry::countAllNonaffil($p_raceid);

							$totalRaceFees = $race->fee * $totalentries;
							$totalOnlineFees = $race->onlinefee * $totalonline;
							$totalAffilFees = $race->affilfee * $totalnonaffil;
							$totalCharityFees = $race->charityfee * $totalentries;
							$raceTotal = $totalRaceFees + $totalOnlineFees + $totalAffilFees + $totalCharityFees; ?>
							<div class="sumcaption">Entries</div>
							<div class="sumfield"><?php echo $totalentries; ?></div>
							<div class="sumfield" id="displayRaceFee_<?php echo $p_raceid; ?>"></div>
							<div class="sumfield" id="displayTotalRaceFees_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="raceFee_<?php echo $p_raceid; ?>" id="raceFee_<?php echo $p_raceid; ?>" value="<?php echo $race->fee; ?>" />
							<input type="hidden" name="totalRaceFees_<?php echo $p_raceid; ?>" id="totalRaceFees_<?php echo $p_raceid; ?>" value="<?php echo $totalRaceFees ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayRaceFee_". $p_raceid. "', '', dojo.currency.format(document.form1.raceFee_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));";
							$JS .= "MM_setTextOfLayer('displayTotalRaceFees_". $p_raceid. "', '', dojo.currency.format(document.form1.totalRaceFees_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
							<div class="clear"></div>
							<?php
							if (!empty($race->onlinefee)) { ?>
								<div class="sumcaption">Online Fees</div>
								<div class="sumfield"><?php echo $totalonline; ?></div>
								<div class="sumfield" id="displayOnlineFee_<?php echo $p_raceid; ?>"></div>
								<div class="sumfield" id="displayTotalOnlineFees_<?php echo $p_raceid; ?>"></div>
								<input type="hidden" name="onlineFee_<?php echo $p_raceid; ?>" id="onlineFee_<?php echo $p_raceid; ?>" value="<?php echo $race->onlinefee; ?>" />
								<input type="hidden" name="totalOnlineFees_<?php echo $p_raceid; ?>" id="totalOnlineFees_<?php echo $p_raceid; ?>" value="<?php echo $totalOnlineFees; ?>" />
								<?php
								$JS .= "MM_setTextOfLayer('displayOnlineFee_". $p_raceid. "', '', dojo.currency.format(document.form1.onlineFee_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));";
								$JS .= "MM_setTextOfLayer('displayTotalOnlineFees_". $p_raceid. "', '', dojo.currency.format(document.form1.totalOnlineFees_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
								<div class="clear"></div>
							<?php
							}
							if (!empty($race->affilfee)) { ?>
								<div class="sumcaption">Affiliation Fees</div>
								<div class="sumfield"><?php echo $totalnonaffil; ?></div>
								<div class="sumfield" id="displayAffilFee_<?php echo $p_raceid; ?>"></div>
								<div class="sumfield" id="displayTotalAffilFees_<?php echo $p_raceid; ?>"></div>
								<input type="hidden" name="affilFee_<?php echo $p_raceid; ?>" id="affilFee_<?php echo $p_raceid; ?>" value="<?php echo $race->affilfee; ?>" />
								<input type="hidden" name="totalAffilFees_<?php echo $p_raceid; ?>" id="totalAffilFees_<?php echo $p_raceid; ?>" value="<?php echo $totalAffilFees; ?>" />
								<?php
								$JS .= "MM_setTextOfLayer('displayAffilFee_". $p_raceid. "', '', dojo.currency.format(document.form1.affilFee_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));";
								$JS .= "MM_setTextOfLayer('displayTotalAffilFees_". $p_raceid. "', '', dojo.currency.format(document.form1.totalAffilFees_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
								<div class="clear"></div>
							<?php
							}
							if (!empty($race->charityfee)) { ?>
								<div class="sumcaption">Charity Fees</div>
								<div class="sumfield"><?php echo $totalentries; ?></div>
								<div class="sumfield" id="displayCharityFee_<?php echo $p_raceid; ?>"></div>
								<div class="sumfield" id="displayTotalCharityFees_<?php echo $p_raceid; ?>"></div>
								<input type="hidden" name="charityFee_<?php echo $p_raceid; ?>" id="charityFee_<?php echo $p_raceid; ?>" value="<?php echo $race->charityfee; ?>" />
								<input type="hidden" name="totalCharityFees_<?php echo $p_raceid; ?>" id="totalCharityFees_<?php echo $p_raceid; ?>" value="<?php echo $totalCharityFees; ?>" />
								<?php
								$JS .= "MM_setTextOfLayer('displayCharityFee_". $p_raceid. "', '', dojo.currency.format(document.form1.charityFee_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));";
								$JS .= "MM_setTextOfLayer('displayTotalCharityFees_". $p_raceid. "', '', dojo.currency.format(document.form1.totalCharityFees_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
								<div class="clear"></div>
							<?php
							} ?>
							<div class="sumcaption"><strong>Race Total (back calc.)</strong></div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayRaceTotal_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="raceTotal_<?php echo $p_raceid; ?>" id="raceTotal_<?php echo $p_raceid; ?>" value="<?php echo $raceTotal; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayRaceTotal_". $p_raceid. "', '', '<strong>' + dojo.currency.format(document.form1.raceTotal_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');";

							$raceonlinetotals = PayHistory::findRaceTotals($p_raceid);
							$raceonlinetotal = $raceonlinetotals->total;
							$racecommissiontotal = $raceonlinetotals->charge; ?>
							<div class="clear"></div><br />
							<div class="sumcaption">Online Income</div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayRaceOnlineIncome_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="totalRaceOnlineIncome_<?php echo $p_raceid; ?>" id="totalRaceOnlineIncome_<?php echo $p_raceid; ?>" value="<?php echo $raceonlinetotal; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayRaceOnlineIncome_". $p_raceid. "', '', dojo.currency.format(document.form1.totalRaceOnlineIncome_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));";

							$racechequetotals = Entry::findChequeTotal($p_raceid);
							$racechequetotal = $racechequetotals->total;
							$racediscounttotal = $racechequetotals->discount; ?>

							<div class="clear"></div>
							<div class="sumcaption">Cheque Income</div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayRaceChequeIncome_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="totalRaceChequeIncome_<?php echo $p_raceid; ?>" id="totalRaceChequeIncome_<?php echo $p_raceid; ?>" value="<?php echo $racechequetotal; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayRaceChequeIncome_". $p_raceid. "', '', dojo.currency.format(document.form1.totalRaceChequeIncome_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
							<div class="clear"></div>
							<div class="sumcaption"><strong>Race Total (actual)</strong></div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayRaceActual_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="totalRaceActual_<?php echo $p_raceid; ?>" id="totalRaceActual_<?php echo $p_raceid; ?>" value="<?php echo $raceonlinetotal + $racechequetotal; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayRaceActual_". $p_raceid. "', '', '<strong>' + dojo.currency.format(document.form1.totalRaceActual_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');" ?>
							<div class="clear"></div><br />
							<div class="sumcaption"><strong>Difference</strong></div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayRaceDifference_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="totalRaceDifference_<?php echo $p_raceid; ?>" id="totalRaceDifference_<?php echo $p_raceid; ?>" value="<?php echo $raceTotal - ($raceonlinetotal + $racechequetotal); ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayRaceDifference_". $p_raceid. "', '', '<strong>' + dojo.currency.format(document.form1.totalRaceDifference_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');"; ?>
							<div class="clear"></div><br />
							<div class="sumcaption">Online Commission</div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayRaceOnlineCommission_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="totalRaceOnlineCommission_<?php echo $p_raceid; ?>" id="totalRaceOnlineCommission_<?php echo $p_raceid; ?>" value="<?php echo $racecommissiontotal; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayRaceOnlineCommission_". $p_raceid. "', '', dojo.currency.format(document.form1.totalRaceOnlineCommission_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
							<div class="clear"></div>
							<div class="sumcaption">Discounts</div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayRaceDiscounts_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="totalRaceDiscounts_<?php echo $p_raceid; ?>" id="totalRaceDiscounts_<?php echo $p_raceid; ?>" value="<?php echo $racediscounttotal; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayRaceDiscounts_". $p_raceid. "', '', dojo.currency.format(document.form1.totalRaceDiscounts_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
							<div class="clear"></div>
							<div class="sumcaption"><strong>Actual Income</strong></div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayRaceActualIncome_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="totalRaceActualIncome_<?php echo $p_raceid; ?>" id="totalRaceActualIncome_<?php echo $p_raceid; ?>" value="<?php echo $raceonlinetotal + $racechequetotal + $racecommissiontotal + $racediscounttotal; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayRaceActualIncome_". $p_raceid. "', '', '<strong>' + dojo.currency.format(document.form1.totalRaceActualIncome_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');"; ?>
							<div class="clear"></div><br />
							<?php
							$grandRaceFees += $totalRaceFees;
							$grandOnlineFees += $totalOnlineFees;
							$grandAffilFees += $totalAffilFees;
							$grandCharityFees += $totalCharityFees;
							$grandRaceTotal += $raceTotal;

							$grandRaceOnlineIncome += $raceonlinetotal;
							$grandRaceChequeIncome += $racechequetotal;
							$grandRaceActual += $raceonlinetotal + $racechequetotal;

							$grandRaceDifference += $raceTotal - ($raceonlinetotal + $racechequetotal);

							$grandRaceOnlineCommission += $racecommissiontotal;
							$grandRaceDiscounts += $racediscounttotal;
							$grandRaceActualIncome += $raceonlinetotal + $racechequetotal + $racecommissiontotal + $racediscounttotal;
						} ?>
						<h2>Race Summary</h2>
							<div class="sumcaption">Entries</div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayGrandTotalRaceFees_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="grandTotalRaceFees_<?php echo $p_raceid; ?>" id="grandTotalRaceFees_<?php echo $p_raceid; ?>" value="<?php echo $grandRaceFees; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayGrandTotalRaceFees_". $p_raceid. "', '', dojo.currency.format(document.form1.grandTotalRaceFees_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
							<div class="clear"></div>
							<?php
							if (!empty($grandOnlineFees)) { ?>
								<div class="sumcaption">Online Fees</div>
								<div class="sumfield"></div>
								<div class="sumfield"></div>
								<div class="sumfield" id="displayGrandTotalOnlineFees_<?php echo $p_raceid; ?>"></div>
								<input type="hidden" name="grandTotalOnlineFees_<?php echo $p_raceid; ?>" id="grandTotalOnlineFees_<?php echo $p_raceid; ?>" value="<?php echo $grandOnlineFees; ?>" />
								<?php
								$JS .= "MM_setTextOfLayer('displayGrandTotalOnlineFees_". $p_raceid. "', '', dojo.currency.format(document.form1.grandTotalOnlineFees_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
								<div class="clear"></div>
							<?php
							}
							if (!empty($grandAffilFees)) { ?>
								<div class="sumcaption">Affiliation Fees</div>
								<div class="sumfield"></div>
								<div class="sumfield"></div>
								<div class="sumfield" id="displayGrandTotalAffilFees_<?php echo $p_raceid; ?>"></div>
								<input type="hidden" name="grandTotalAffilFees_<?php echo $p_raceid; ?>" id="grandTotalAffilFees_<?php echo $p_raceid; ?>" value="<?php echo $grandAffilFees; ?>" />
								<?php
								$JS .= "MM_setTextOfLayer('displayGrandTotalAffilFees_". $p_raceid. "', '', dojo.currency.format(document.form1.grandTotalAffilFees_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
								<div class="clear"></div>
							<?php
							}
							if (!empty($grandCharityFees)) { ?>
								<div class="sumcaption">Charity Fees</div>
								<div class="sumfield"></div>
								<div class="sumfield"></div>
								<div class="sumfield" id="displayGrandTotalCharityFees_<?php echo $p_raceid; ?>"></div>
								<input type="hidden" name="grandTotalCharityFees_<?php echo $p_raceid; ?>" id="grandTotalCharityFees_<?php echo $p_raceid; ?>" value="<?php echo $grandCharityFees; ?>" />
								<?php
								$JS .= "MM_setTextOfLayer('displayGrandTotalCharityFees_". $p_raceid. "', '', dojo.currency.format(document.form1.grandTotalCharityFees_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
								<div class="clear"></div>
							<?php
							} ?>
							<div class="sumcaption"><strong>Race Total (back calc.)</strong></div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayRaceGrandTotal_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="raceGrandTotal_<?php echo $p_raceid; ?>" id="raceGrandTotal_<?php echo $p_raceid; ?>" value="<?php echo $grandRaceTotal; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayRaceGrandTotal_". $p_raceid. "', '', '<strong>' + dojo.currency.format(document.form1.raceGrandTotal_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');"; ?>
							<div class="clear"></div><br />
							<div class="sumcaption">Online Income</div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayGrandRaceOnlineIncome_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="grandTotalRaceOnlineIncome_<?php echo $p_raceid; ?>" id="grandTotalRaceOnlineIncome_<?php echo $p_raceid; ?>" value="<?php echo $grandRaceOnlineIncome; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayGrandRaceOnlineIncome_". $p_raceid. "', '', dojo.currency.format(document.form1.grandTotalRaceOnlineIncome_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
							<div class="clear"></div>
							<div class="sumcaption">Cheque Income</div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayGrandRaceChequeIncome_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="grandTotalRaceChequeIncome_<?php echo $p_raceid; ?>" id="grandTotalRaceChequeIncome_<?php echo $p_raceid; ?>" value="<?php echo $grandRaceChequeIncome; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayGrandRaceChequeIncome_". $p_raceid. "', '', dojo.currency.format(document.form1.grandTotalRaceChequeIncome_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
							<div class="clear"></div>
							<div class="sumcaption"><strong>Race Total (actual)</strong></div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayGrandRaceActual_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="grandTotalRaceActual_<?php echo $p_raceid; ?>" id="grandTotalRaceActual_<?php echo $p_raceid; ?>" value="<?php echo $grandRaceActual; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayGrandRaceActual_". $p_raceid. "', '', '<strong>' + dojo.currency.format(document.form1.grandTotalRaceActual_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');"; ?>
							<div class="clear"></div><br />
							<div class="sumcaption"><strong>Difference</strong></div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayGrandRaceDifference_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="grandTotalRaceDifference_<?php echo $p_raceid; ?>" id="grandTotalRaceDifference_<?php echo $p_raceid; ?>" value="<?php echo $grandRaceDifference; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayGrandRaceDifference_". $p_raceid. "', '', '<strong>' + dojo.currency.format(document.form1.grandTotalRaceDifference_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');"; ?>
							<div class="clear"></div><br />
							<div class="sumcaption">Online Commission</div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayGrandRaceOnlineCommission_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="grandTotalRaceOnlineCommission_<?php echo $p_raceid; ?>" id="grandTotalRaceOnlineCommission_<?php echo $p_raceid; ?>" value="<?php echo $grandRaceOnlineCommission; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayGrandRaceOnlineCommission_". $p_raceid. "', '', dojo.currency.format(document.form1.grandTotalRaceOnlineCommission_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
							<div class="clear"></div>
							<div class="sumcaption">Discounts</div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayGrandRaceDiscounts_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="totalGrandRaceDiscounts_<?php echo $p_raceid; ?>" id="totalGrandRaceDiscounts_<?php echo $p_raceid; ?>" value="<?php echo $grandRaceDiscounts; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayGrandRaceDiscounts_". $p_raceid. "', '', dojo.currency.format(document.form1.totalGrandRaceDiscounts_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}));"; ?>
							<div class="clear"></div>
							<div class="sumcaption"><strong>Actual Income</strong></div>
							<div class="sumfield"></div>
							<div class="sumfield"></div>
							<div class="sumfield" id="displayGrandRaceActualIncome_<?php echo $p_raceid; ?>"></div>
							<input type="hidden" name="grandTotalRaceActualIncome_<?php echo $p_raceid; ?>" id="grandTotalRaceActualIncome_<?php echo $p_raceid; ?>" value="<?php echo $grandRaceActualIncome; ?>" />
							<?php
							$JS .= "MM_setTextOfLayer('displayGrandRaceActualIncome_". $p_raceid. "', '', '<strong>' + dojo.currency.format(document.form1.grandTotalRaceActualIncome_". $p_raceid. ".value, {currency: '". $currentSite->currencycode. "'}) + '</strong>');" ?>
							<div class="clear"></div><br /><br />
						<input type="hidden" name="currencycode" id="currencycode" value="<?php echo $currentSite->currencycode; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
	<?php
	$JS .= '}</script>';
	echo $JS;
	?>
</html>