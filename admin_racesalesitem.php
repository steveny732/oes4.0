<?php
/* ************************************************************************
' admin_racesalesitem.php -- Administration - List race sales items for maintenance
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
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'paging.php');
require_once(LIB_PATH. 'racesalesitem.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['racesalesitem_searchname']);
	$_SESSION['racesalesitem_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['racesalesitem_searchname'] = $_POST['racesalesitem_searchname'];
	$_SESSION['racesalesitem_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['racesalesitem_currentpage']) ? $_SESSION['racesalesitem_currentpage'] : 1;
} else {
	$_SESSION['racesalesitem_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

if (isset($_REQUEST['raceid'])) {
	$_SESSION['raceid'] = $_REQUEST['raceid'];
}
$raceid = $_SESSION['raceid'];
$searchname = $_SESSION['racesalesitem_searchname'];

$itemcount = RaceSalesItem::countAll($raceid, $searchname);
$paging = new Paging($currentpage, 17, $itemcount);
if(isset($_POST['update'])) {

	// Update items
	$racesalesitems = RaceSalesItem::findAllByPage($raceid, $paging, $searchnumber);
	foreach ($racesalesitems as $racesalesitem) {
		if ($_POST['selected_'. $racesalesitem->id]) {
			if (!$racesalesitem->selected) {
				$thisitem = new RaceSalesItem();
				$thisitem->raceid = $raceid;
				$thisitem->salesitemid = $racesalesitem->id;
				$thisitem->add();
			}
		} else {
			if ($racesalesitem->selected) {
				$thisitem = RaceSalesItem::findById($raceid, $racesalesitem->id);
				$thisitem->delete();
			}
		}
	}
	redirect_to('admin_racelist.php');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<?php $dojoParse = "false";
		include(LIB_PATH. 'dojo.php') ?>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript">
			dojo.require("dojo.parser");
			dojo.require("dojo.currency");
			dojo.require("dojo.date.locale");
			dojo.addOnLoad(function() {
				loadValues();
				dojo.parser.parse();
			});
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onload="self.focus();document.form1.racesalesitem_searchname.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Race Sales Item List</h2>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							Search:&nbsp;<input type="text" name="racesalesitem_searchname" value="<?php echo $searchname; ?>" />
							<input type="submit" name="filter" value="Go" />
						</form>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_racelist.php">race list</a>&nbsp;&gt;&nbsp;
						race sales item list<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form2" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<table width="100%" cellpadding="2" cellspacing="0">
							<tr class="darkgreybg">
								<td width="50" align="center"><strong>Selected</strong></td>
								<td width="200" align="left"><strong>Name</strong></td>
								<td width="50" align="right"><strong>Price</strong></td>
								<td width="50" align="center"><strong>Allow Qty</strong></td>
							</tr>
							<?php
							if ($paging->pagecount() == 0) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td colspan="4" align="center"><br /><br /><strong>No sales items found.</strong></td>
								</tr>
							<?php
							} else {
								$racesalesitems = RaceSalesItem::findAllByPage($raceid, $paging, $searchnumber);
								foreach ($racesalesitems as $racesalesitem) { ?>
									<tr class="<?php echo $paging->colourNewRow(); ?>">
										<td class="smalltext" align="center">
											<input type="checkbox" name="selected_<?php echo $racesalesitem->id; ?>" id="selected_<?php echo $racesalesitem->id; ?>"<?php if ($racesalesitem->selected) { echo ' checked="checked"'; } ?> />
										</td>
										<td class="smalltext" align="left"><?php echo $racesalesitem->name; ?></td>
										<td class="smalltext" align="right"><?php echo $racesalesitem->price; ?></td>
										<td class="smalltext" align="center"><?php if ($racesalesitem->allowquantity) { echo 'Yes'; } else { echo 'No'; } ?></td>
									</tr>
								<?php
								}
							} ?>
						</table>
						<input type="submit"name="update" value="Update" />
					</form>
					<?php
					echo $paging->pagingNav($_SERVER['PHP_SELF']); ?>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>