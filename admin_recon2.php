<?php
/* ************************************************************************
' admin_recon2.php -- Show Nochex/entries Descrepancies
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
'		betap 2008-08-27 07:55:27
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'paging.php');
require_once(LIB_PATH. 'contact.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	$_SESSION['recon2_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['recon2_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['recon2_currentpage']) ? $_SESSION['recon2_currentpage'] : 1;
} else {
	$_SESSION['recon2_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

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
		include(LIB_PATH. 'style_overrides.php'); ?>
  </head>

	<body>
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Online Payment/Entry Descrepancies</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						online payment/entry descrepancies<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form method="post" name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<?php
						$p_count = 0;
						$diffs = PayHistory::reconcileAgainstEntries(false);
						$paging = new Paging($currentpage, 50, count($diffs));
						$diffs = PayHistory::reconcileAgainstEntries($paging); ?>
						<table border="0" cellspacing="0" cellpadding="2">
							<tr class="darkgreybg">
								<td width="40" align="right"><strong>Order</strong></td>
								<td width="20" align="right"><strong>No.</strong></td>
								<td width="200" align="left"><strong>Name</strong></td>
								<td width="20" align="center"><strong>Online?</strong></td>
								<td width="20" align="center"><strong>Affil?</strong></td>
								<td width="70" align="right"><strong>Entry Amt.</strong></td>
								<td width="70" align="right"><strong>Entry Disc.</strong></td>
								<td width="70" align="right"><strong>Online Amt.</strong></td>
							</tr>
							<?php
							if ($paging->pagecount() == 0) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td colspan="8" align="center"><br /><br /><strong>No descrepancies found.</strong></td>
								</tr>
							<?php
							} else {
								foreach ($diffs as $diff) {
								
									if (empty($diff->entryordernumber)) {
										if (!empty($diff->payamount)) { ?>
											<input type="hidden" name="payhistoryAmount<?php echo $p_count; ?>" id="payhistoryAmount<?php echo $p_count; ?>" value="<?php echo $diff->payamount; ?>" />
											<tr class="<?php echo $paging->colourNewRow(); ?>">
												<td class="smalltext" align="right"><?php echo $diff->ordernumber; ?></td>
												<td class="smalltext" align="right">&nbsp;</td>
												<td class="smalltext" align="left">No entry found</td>
												<td class="smalltext" align="center">&nbsp;</td>
												<td class="smalltext" align="center">&nbsp;</td>
												<td class="smalltext" align="right">&nbsp;</td>
												<td class="smalltext" align="right">&nbsp;</td>
												<td class="smalltext" align="right" id="displayAmount<?php echo $p_count; ?>"></td>
											</tr>
											<?php
											$JS .= "MM_setTextOfLayer('displayAmount". $p_count. "', '', dojo.currency.format(document.form1.payhistoryAmount". $p_count. ".value, {currency: '". $currentSite->currencycode. "'}));";
											$p_count++;
										}
									} else {
										$neworder = true;
										$entries = Entry::findByOrderNumber($diff->ordernumber);
										foreach ($entries as $entry) { ?>
											<input type="hidden" name="payhistoryAmount<?php echo $p_count; ?>" id="payhistoryAmount<?php echo $p_count; ?>" value="<?php echo $diff->payamount; ?>" />
											<input type="hidden" name="entryTotal<?php echo $p_count; ?>" id="entryTotal<?php echo $p_count; ?>" value="<?php echo $entry->total; ?>" />
											<input type="hidden" name="entryDiscount<?php echo $p_count; ?>" id="entryDiscount<?php echo $p_count; ?>" value="<?php if (!empty($entry->discount)) { echo $entry->discount; } else { echo '0'; } ?>" />
											<tr class="<?php echo $paging->colourNewRow(); ?>">
												<td class="smalltext" align="right"><?php if ($neworder) { echo $diff->ordernumber; } ?></td>
												<td class="smalltext" align="right"><?php echo $entry->racenumber; ?></td>
												<td class="smalltext" align="left"><?php echo strtoupper($entry->surname). ', '. $entry->firstname; ?></td>
												<td class="smalltext" align="center"><?php echo $entry->online; ?></td>
												<td class="smalltext" align="center"><?php echo $entry->affil; ?></td>
												<td class="smalltext" align="right" id="displayEntryTotal<?php echo $p_count; ?>"></td>
												<td class="smalltext" align="right" id="displayDiscount<?php echo $p_count; ?>"></td>
												<td class="smalltext" align="right" id="displayAmount<?php echo $p_count; ?>"></td>
											</tr>
											<?php
											if ($neworder) {
												$JS .="MM_setTextOfLayer('displayAmount". $p_count. "', '', dojo.currency.format(document.form1.payhistoryAmount". $p_count. ".value, {currency: '". $currentSite->currencycode. "'}));";
											}
											$JS .= "MM_setTextOfLayer('displayEntryTotal". $p_count. "', '', dojo.currency.format(document.form1.entryTotal". $p_count. ".value, {currency: '". $currentSite->currencycode. "'}));";
											$JS .= "MM_setTextOfLayer('displayDiscount". $p_count. "', '', dojo.currency.format(document.form1.entryDiscount". $p_count. ".value, {currency: '". $currentSite->currencycode. "'}));";
											$neworder = false;
											$p_count++;
										}
									}
								}
							} ?>
						</table>
						<?php
						echo $paging->pagingNav($_SERVER['PHP_SELF']); ?>
						<input type="hidden" name="currency" id="currency" value="<?php echo $currentSite->currencycode; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
	<?php $JS .= '}</script>';
	echo $JS; ?>
</html>