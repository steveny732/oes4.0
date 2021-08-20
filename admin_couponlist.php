<?php
/* ************************************************************************
' admin_couponlist.php -- Administration - List coupons for maintenance
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
require_once(LIB_PATH. 'coupon.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['coupon_searchname']);
	$_SESSION['coupon_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['coupon_searchname'] = $_POST['coupon_searchname'];
	$_SESSION['coupon_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['coupon_currentpage']) ? $_SESSION['coupon_currentpage'] : 1;
} else {
	$_SESSION['coupon_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

if (isset($_REQUEST['raceid'])) {
	$_SESSION['raceid'] = $_REQUEST['raceid'];
}
$raceid = $_SESSION['raceid'];
$searchname = "";
if (isset($_SESSION['coupon_searchname'])) {
	$searchname = $_SESSION['coupon_searchname'];
}

$js = '<script type="text/javascript">function loadValues(){';
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

	<body onload="self.focus();document.form1.coupon_searchname.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					$itemcount = Coupon::countAll($raceid, $searchname);
					$paging = new Paging($currentpage, 17, $itemcount); ?>
	
					<div class="floatleft">
						<h2>Coupon List</h2>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							Search:&nbsp;<input type="text" name="coupon_searchname" value="<?php echo $searchname; ?>" />
							<input type="submit" name="filter" value="Go" />
						</form>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_racelist.php">race list</a>&nbsp;&gt;&nbsp;
						coupon list<br /><br /><br />
						<a href="admin_couponadd.php">add a coupon</a>&nbsp;
					</div>
					<div class="clear"></div><br />
					<table width="100%" cellpadding="2" cellspacing="0">
						<tr class="darkgreybg">
							<td width="70" align="center"><strong>Code</strong></td>
							<td width="100" align="left"><strong>Name</strong></td>
							<td width="100" align="center"><strong>Start</strong></td>
							<td width="100" align="center"><strong>End</strong></td>
							<td width="100" align="right"><strong>Discount</strong></td>
							<td width="50" align="center"><strong>Actions</strong></td>
						</tr>
						<?php
						if ($paging->pagecount() == 0) { ?>
							<tr class="<?php echo $paging->colourNewRow(); ?>">
								<td colspan="4" align="center"><br /><br /><strong>No coupons found.</strong></td>
							</tr>
						<?php
						} else {
							$coupons = Coupon::findAllByPage($raceid, $paging, $searchname);
							foreach ($coupons as $coupon) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td class="smalltext" align="center"><a href="admin_couponupdate.php?id=<?php echo $coupon->id; ?>"><?php echo  $coupon->code; ?></a></td>
									<td class="smalltext"><?php echo $coupon->name; ?></td>
									<td class="smalltext" align="center" id="startdate_<?php echo $coupon->id; ?>"></td>
									<?php $js .= "var d=new Date(". $coupon->startyear(). ",". ($coupon->startmonth() - 1). ",". $coupon->startday(). ");document.getElementById('startdate_". $coupon->id. "').innerHTML=dojo.date.locale.format(d,{selector:'date',formatLength:'short',locale:'". $cookieLanguageCode. "'});"; ?>
									<td class="smalltext" align="center" id="enddate_<?php echo $coupon->id; ?>"></td>
									<?php $js .= "var d=new Date(". $coupon->endyear(). ",". ($coupon->endmonth() - 1). ",". $coupon->endday(). ");document.getElementById('enddate_". $coupon->id. "').innerHTML=dojo.date.locale.format(d,{selector:'date',formatLength:'short',locale:'". $cookieLanguageCode. "'});"; ?>
									<td class="smalltext" align="right" id="discount_<?php echo $coupon->id; ?>"></td>
									<?php $js .= "document.getElementById('discount_". $coupon->id. "').innerHTML=dojo.currency.format(". $coupon->discount. ", {currency: '". $currentSite->currencycode. "'});"; ?>
									<td class="smalltext" align="center"><a href="admin_coupondelete.php?id=<?php echo $coupon->id; ?>">delete</a></td>
								</tr>
							<?php
							}
						} ?>
					</table>
					<?php
					echo $paging->pagingNav($_SERVER['PHP_SELF']); ?>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
		<?php
		$js .= "}</script>";
		echo $js; ?>
	</body>
</html>