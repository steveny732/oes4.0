<?php
/* ************************************************************************
' admin_changeclose.php -- Administration - Close an open change request
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
'		betap 2008-06-27 20:39:23
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'transfer.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (!empty($_POST['submit'])) {

	$transfer = Transfer::findByIdForUpdate($_REQUEST['id']);
	$entryid = $transfer->offertoentryid;
	if ($transfer->offereddate == 0) {
		$transfer->superceded = 1;
		$transfer->save();
	} else {
		$transfer->offereddate = '';
		$transfer->offertoentryid = 0;
		$transfer->save();

		$entry = Entry::findById($entryid);
		$entry->incrementOfferCount();
	}

	redirect_to("admin_changelist.php");
}

$transfer = Transfer::findById($_REQUEST['id']);

$race = Race::findById($transfer->raceid);
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
			dojo.require("dojo.parser");
			dojo.addOnLoad(function() {
				var year = parseInt(document.form1.olddob.value.substring(0, 4));
				var month = parseInt(document.form1.olddob.value.substring(5, 7)) - 1;
				var date = parseInt(document.form1.olddob.value.substring(8, 10))
				var d = new Date(year, month, date);
				MM_setTextOfLayer('displayolddob', '', dojo.date.locale.format(d, {selector: 'date', formatLength: 'long', locale: '<?php echo $cookieLanguageCode; ?>'}));
				<?php
				if ($transfer->newdob != '0000-00-00') { ?>
					year = parseInt(document.form1.newdob.value.substring(0, 4));
					month = parseInt(document.form1.newdob.value.substring(5, 7)) - 1;
					date = parseInt(document.form1.newdob.value.substring(8, 10))
					d = new Date(year, month, date);
					MM_setTextOfLayer('displaynewdob', '', dojo.date.locale.format(d, {selector: 'date', formatLength: 'long', locale: '<?php echo $cookieLanguageCode; ?>'}));
				<?php
				} ?>
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
						<h2>Close Change/Transfer/Offer</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_changelist.php">change/transfer/offer list</a>&nbsp;&gt;&nbsp;
						close change/transfer/offer<br /><br /><br />
					</div>
					<div class="clear"></div>
					<?php
					if (empty($transfer->offereddate)) { ?>
						Are you absolutely sure you want to close the following transfer/change/offer?
					<?php
					} else { ?>
						Are you absolutely sure you want to re-open this offer?
					<?php
					} ?><br /><br />
					<table width="100%" cellpadding="2" cellspacing="0">
						<tr class="whitebg">
							<td width="20%">Transfer Code:</td>
							<td width="40%"><strong><?php echo $transfer->code; ?></strong></td>
							<td width="40%">&nbsp;</td>
						</tr>
						<tr class="greybg">
							<td>Race Number:</td>
							<td><strong><?php echo $transfer->racenumber; ?></strong></td>
							<td>&nbsp;</td>
						</tr>
						<tr class="whitebg">
							<td width="20%">Name:</td>
							<td width="40%"><strong><?php echo $transfer->oldfirstname. " ". $transfer->oldsurname; ?></strong></td>
							<td width="40%"><strong><?php echo $transfer->newfirstname. " ". $transfer->newsurname; ?></strong></td>
						</tr>
						<tr class="greybg">
							<td>Address 1:</td>
							<td><strong><?php echo $transfer->oldaddress1; ?></strong></td>
							<td><strong><?php echo $transfer->newaddress1; ?></strong></td>
						</tr>
						<tr class="whitebg">
							<td>Address 2:</td>
							<td><strong><?php echo $transfer->oldaddress2; ?></strong></td>
							<td><strong><?php echo $transfer->newaddress2; ?></strong></td>
						</tr>
						<tr class="greybg">
							<td>Town:</td>
							<td><strong><?php echo $transfer->oldtown; ?></strong></td>
							<td><strong><?php echo $transfer->newtown; ?></strong></td>
						</tr>
						<tr class="whitebg">
							<td>County:</td>
							<td><strong><?php echo $transfer->oldcounty; ?></strong></td>
							<td><strong><?php echo $transfer->newcounty; ?></strong></td>
						</tr>
						<tr class="greybg">
							<td>Postcode:</td>
							<td><strong><?php echo $transfer->oldpostcode; ?></strong></td>
							<td><strong><?php echo $transfer->newpostcode; ?></strong></td>
						</tr>
						<tr class="whitebg">
							<td>Telephone (day):</td>
							<td><strong><?php echo $transfer->oldtelday; ?></strong></td>
							<td><strong><?php echo $transfer->newtelday; ?></strong></td>
						</tr>
						<tr class="greybg">
							<td>Telephone (Eve):</td>
							<td><strong><?php echo $transfer->oldteleve; ?></strong></td>
							<td><strong><?php echo $transfer->newteleve; ?></strong></td>
						</tr>
						<tr class="whitebg">
							<td>Gender:</td>
							<td><strong><?php echo $transfer->oldgender; ?></strong></td>
							<td><strong><?php echo $transfer->newgender; ?></strong></td>
						</tr>
						<tr class="greybg">
							<td>DOB:</td>
							<td><strong><div id="displayolddob"></div></strong></td>
							<td><strong><div id="displaynewdob"></div></strong></td>
						</tr>
						<tr class="whitebg">
							<td>Email:</td>
							<td><strong><?php echo $transfer->oldemail; ?></strong></td>
							<td><strong><?php echo $transfer->newemail; ?></strong></td>
						</tr>
						<?php
						if ($race->clubsenabled) { ?>
							<tr class="greybg">
								<td>Club:</td>
								<td><strong><?php echo $transfer->oldclubname; ?></strong></td>
								<td><strong><?php echo $transfer->newclubname; ?></strong></td>
							</tr>
						<?php
						}
						if ($race->teamsenabled) { ?>
							<tr class="greybg">
								<td>Team:</td>
								<td><strong><?php echo $transfer->oldteam; ?></strong></td>
								<td><strong><?php echo $transfer->newteam; ?></strong></td>
							</tr>
						<?php
						}
						if ($race->tshirtsenabled) { ?>
							<tr class="whitebg">
								<td>T-Shirt:</td>
								<td><strong><?php echo $transfer->oldtshirt; ?></strong></td>
								<td><strong><?php echo $transfer->newtshirt; ?></strong></td>
							</tr>
						<?php
						} ?>
					</table><br /><br />
					<div class="buttonblock">
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<?php
							if (empty($transfer->offered_date)) { ?>
								<input type="submit" name="submit" value="Confirm Close" />&nbsp;&nbsp;
							<?php
							} else { ?>
								<input type="submit" name="submit" value="Confirm Re-offer" />&nbsp;&nbsp;
							<?php
							} ?>
							<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
							<input type="hidden" name="id" value="<?php echo $transfer->id; ?>" />
							<input type="hidden" name="olddob" id="olddob" value="<?php echo $transfer->olddob; ?>" />
							<?php
							if (!empty($transfer->newdob)) { ?>
								<input type="hidden" name="newdob" id="newdob" value="<?php echo $transfer->newdob; ?>" />
							<?php
							} ?>
						</form>
					</div>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>