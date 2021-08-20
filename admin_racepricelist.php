<?php
/* ************************************************************************
' admin_racepricelist.php -- Administration - List race prices for maintenance
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
require_once(LIB_PATH. 'raceprice.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['raceprice_searchname']);
	$_SESSION['raceprice_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['raceprice_searchname'] = $_POST['raceprice_searchname'];
	$_SESSION['raceprice_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['raceprice_currentpage']) ? $_SESSION['raceprice_currentpage'] : 1;
} else {
	$_SESSION['raceprice_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

if (isset($_REQUEST['raceid'])) {
	$_SESSION['raceid'] = $_REQUEST['raceid'];
}
$raceid = $_SESSION['raceid'];
$searchname = "";
if (isset($_SESSION['raceprice_searchname'])) {
	$searchname = $_SESSION['raceprice_searchname'];
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

	<body onload="self.focus();document.form1.raceprice_searchname.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					$itemcount = RacePrice::countAll($raceid, $searchname);
					$paging = new Paging($currentpage, 17, $itemcount); ?>
	
					<div class="floatleft">
						<h2>Race Price List</h2>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							Search:&nbsp;<input type="text" name="raceprice_searchname" value="<?php echo $searchname; ?>" />
							<input type="submit" name="filter" value="Go" />
						</form>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_racelist.php">race list</a>&nbsp;&gt;&nbsp;
						race price list<br /><br /><br />
						<a href="admin_racepriceadd.php">add a race price</a>&nbsp;
					</div>
					<div class="clear"></div><br />
					<table width="100%" cellpadding="2" cellspacing="0">
						<tr class="darkgreybg">
							<td width="100" align="left"><strong>Name</strong></td>
							<td width="100" align="center"><strong>Start</strong></td>
							<td width="100" align="center"><strong>End</strong></td>
							<td width="100" align="right"><strong>Base Entry Fee</strong></td>
              <td width="100" align="right"><strong>Online Fee</strong></td>
							<td width="50" align="center"><strong>Actions</strong></td>
						</tr>
						<?php
						if ($paging->pagecount() == 0) { ?>
							<tr class="<?php echo $paging->colourNewRow(); ?>">
								<td colspan="4" align="center"><br /><br /><strong>No race prices found.</strong></td>
							</tr>
						<?php
						} else {
							$raceprices = RacePrice::findAllByPage($raceid, $paging, $searchname);
							foreach ($raceprices as $raceprice) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td class="smalltext" align="center"><a href="admin_racepriceupdate.php?id=<?php echo $raceprice->id; ?>"><?php echo  $raceprice->name; ?></a></td>
									<td class="smalltext" align="center" id="startdate_<?php echo $raceprice->id; ?>"></td>
									<?php $js .= "var d=new Date(". $raceprice->startyear(). ",". ($raceprice->startmonth() - 1). ",". $raceprice->startday(). ");document.getElementById('startdate_". $raceprice->id. "').innerHTML=dojo.date.locale.format(d,{selector:'date',formatLength:'short',locale:'". $cookieLanguageCode. "'});"; ?>
									<td class="smalltext" align="center" id="enddate_<?php echo $raceprice->id; ?>"></td>
									<?php $js .= "var d=new Date(". $raceprice->endyear(). ",". ($raceprice->endmonth() - 1). ",". $raceprice->endday(). ");document.getElementById('enddate_". $raceprice->id. "').innerHTML=dojo.date.locale.format(d,{selector:'date',formatLength:'short',locale:'". $cookieLanguageCode. "'});"; ?>
									<td class="smalltext" align="right" id="fee_<?php echo $raceprice->id; ?>"></td>
									<?php $js .= "document.getElementById('fee_". $raceprice->id. "').innerHTML=dojo.currency.format(". $raceprice->fee. ", {currency: '". $currentSite->currencycode. "'});"; ?>
                  <td class="smalltext" align="right" id="onlinefee_<?php echo $raceprice->id; ?>"></td>
									<?php $js .= "document.getElementById('onlinefee_". $raceprice->id. "').innerHTML=dojo.currency.format(". $raceprice->onlinefee. ", {currency: '". $currentSite->currencycode. "'});"; ?>
									<td class="smalltext" align="center"><a href="admin_racepricedelete.php?id=<?php echo $raceprice->id; ?>">delete</a></td>
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