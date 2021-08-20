<?php
/* ************************************************************************
' admin_loggedin.php -- Administration - Administration Menu
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
'		betap 2008-07-11 13:34:27
'		Add 'edit contacts' option.
'		betap 2008-06-27 20:39:23
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/ajax.js"></script>
		<script type="text/javascript" src="scripts/ajaxGetEntrants.js"></script>
		<script type="text/javascript" src="scripts/ajaxGetPotentialDups.js"></script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onLoad="self.focus();document.form1.surname.focus();getEntrants(1);getPotentialDups(1);">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<h2>New Entries</h2>
					<form id="form1" name="form1" method="post" action="admin_new1.php">
						<input type="hidden" name="entrantid" value="" />
						<div class="formsection">&nbsp;</div>
						<div class="formcaption">
							Surname:
						</div>
						<div class="formfield">
							<input id="surname" name="surname" type="text" onKeyUp="getEntrants(1);getPotentialDups(1);" style="width: 200px"/>&nbsp;
							<input type="submit" name="Submit" value="New Entry" />
						</div>
						<?php
						if (!empty($_SESSION['racenumber'])) {
							echo '<div class="clear"></div><br /><div class="largetext">Last Race Number(s) Allocated: '. $_SESSION['racenumber']. '</div>';
						} ?>
						<div class="clear"></div>
						<div class="alignleft" id="surnamesearch" style="display: none; padding: 10px; margin-left: 10px; border: thin solid">
							<h3>Entrants Found (click on an entrant to default their details)</h3>
							<table width="500" id="entranttable">
							</table><br />
							<h3>Potential Duplicates Found</h3>
							<table width="500" id="duplicatestable">
							</table>
						</div>
					</form><br />
					<h3>Operations Menu</h3>
					<div class="windowcontentcolumn1" style="line-height: 20px;">
						<a href="admin_entrylist.php?clearfilter=1">view/update entries/unpaid list/waiting list</a><br />
						<a href="admin_changelist.php?clearfilter=1">view/update changes/transfers/offers</a><br />
						<a href="admin_match_waitlist.php?clearfilter=1">waiting list matching</a><br /><br />
						<a href="admin_download1.php">download to Excel</a><br /><br />
						<a href="admin_uploadnochex1.php">upload Nochex CSV</a><br />
						<a href="#" >upload Paypal CSV (not implemented yet)</a><br /><br />
						<a href="admin.php?logout=1">log out</a><br />
					</div>
					<div class="windowcontentcolumn2" style="line-height: 20px;">
						<a href="admin_summary.php">reconciliation summary</a><br />
						<a href="admin_recon1.php">reconciliation detail - entries different from payment system</a><br />
						<a href="admin_recon2.php">reconciliation detail - payment system different from entries</a><br /><br />
						<a href="admin_uploadresults1.php">upload results</a><br />
					</div>
					<div class="clear"></div>
					<h3>Maintenance Menu</h3>
					<div class="windowcontentcolumn1" style="line-height: 20px;">
						<a href="admin_languagelist.php?clearfilter=1">edit languages</a><br />
						<a href="admin_currencylist.php?clearfilter=1">edit currencies</a><br />
						<a href="admin_sitelist.php?clearfilter=1">edit sites</a><br />
						<a href="admin_levellist.php?clearfilter=1">edit levels</a><br />
						<a href="admin_userlist.php?clearfilter=1">edit users</a><br />
						<a href="admin_contactlist.php?clearfilter=1">edit contacts</a><br />
						<a href="admin_pagelist.php?clearfilter=1">edit pages</a><br />
						<a href="admin_newslist.php?clearfilter=1">edit news</a><br />
					</div>
					<div class="windowcontentcolumn2" style="line-height: 20px;">
						<a href="admin_commentlist.php?clearfilter=1">edit feedback</a><br />
						<a href="admin_serieslist.php?clearfilter=1">edit series</a><br />
						<a href="admin_agecategorytypelist.php?clearfilter=1">edit age category types</a><br />
						<a href="admin_racelist.php?clearfilter=1">edit races</a><br />
						<a href="admin_clubtypelist.php?clearfilter=1">edit club types</a><br />
						<a href="admin_dbgrouplist.php?clearfilter=1">edit database groups</a><br />
						<a href="admin_formlist.php?clearfilter=1">edit forms</a><br />
						<a href="admin_salesitemlist.php?clearfilter=1">edit sales items</a><br />
					</div>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>