<?php
/* ************************************************************************
' admin_racelist.php -- List the races for maintenance
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
require_once(LIB_PATH. 'paging.php');
require_once(LIB_PATH. 'race.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['race_searchname']);
	$_SESSION['race_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['race_searchname'] = $_POST['race_searchname'];
	$_SESSION['race_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['race_currentpage']) ? $_SESSION['race_currentpage'] : 1;
} else {
	$_SESSION['race_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

$searchname = "";
if (isset($_SESSION['race_searchname'])) {
	$searchname = $_SESSION['race_searchname'];
}

$js = '<script type="text/javascript">function loadDates(){';
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
			dojo.require("dojo.date.locale");
			dojo.addOnLoad(function() {
				loadDates();
				dojo.parser.parse();
			});
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onLoad="self.focus();document.form1.race_searchname.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					$itemcount = Race::countAll($searchname);
					$paging = new Paging($currentpage, 17, $itemcount); ?>
					<div class="floatleft">
						<h2>Race List</h2>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							Search:&nbsp;<input type="text" name="race_searchname" value="<?php echo $searchname; ?>" />
							<input type="submit" name="filter" value="Go" />
						</form>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						race list<br /><br /><br />
						<a href="admin_raceadd.php">add a race</a>
					</div>
					<div class="clear"></div><br />
					<table width="100%" cellpadding="2" cellspacing="0">
						<tr class="darkgreybg">
							<td width="300" align="left"><strong>Name</strong></td>
							<td width="100" align="center"><strong>Date</strong></td>
							<td width="100" align="center"><strong>Test Mode?</strong></td>
							<td width="200" align="center"><strong>Action</strong></td>
						</tr>
						<?php
						if ($paging->pagecount() == 0) { ?>
							<tr class="<?php echo $paging->colourNewRow(); ?>">
								<td colspan="4" align="center"><br /><br /><strong>No races found.</strong></td>
							</tr>
						<?php
						} else {
							$races = Race::findAllByPage($paging, $searchname);
							foreach ($races as $race) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td class="smalltext" align="left"><a href="admin_raceupdate.php?id=<?php echo $race->id; ?>"><?php echo $race->name; ?></a></td>
									<td class="smalltext" align="center" id="racedate_<?php echo $race->id; ?>"></td>
									<?php $js .= "var d=new Date(". $race->year(). ",". ($race->month() - 1). ",". $race->day(). ");document.getElementById('racedate_". $race->id. "').innerHTML=dojo.date.locale.format(d,{selector:'date',formatLength:'short',locale:'". $cookieLanguageCode. "'});"; ?>
									<td class="smalltext" align="center"><?php echo $race->testmode ? 'Yes' : 'No'; ?></td>
									<td class="smalltext" align="center">
										<a href="admin_preallnolist.php?clearfilter=1&raceid=<?php echo $race->id; ?>">preallocations</a>&nbsp;
										<a href="admin_couponlist.php?clearfilter=1&raceid=<?php echo $race->id; ?>">coupons</a>&nbsp;
										<a href="admin_racepricelist.php?clearfilter=1&raceid=<?php echo $race->id; ?>">race prices</a><br />
										<?php
										if (SalesItem::countAll() > 0) { ?>
											<a href="admin_racesalesitem.php?raceid=<?php echo $race->id; ?>">sales items</a>&nbsp;
										<?php
										} ?>
										<?php
										if (Language::countAll() > 1) { ?>
											<a href="admin_racetrans.php?id=<?php echo $race->id; ?>">translate</a>&nbsp;
										<?php
										} ?>
										<a href="admin_racecopy.php?id=<?php echo $race->id; ?>">copy</a>&nbsp;
										<a href="admin_racedelete.php?id=<?php echo $race->id; ?>">delete</a>
									</td>
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