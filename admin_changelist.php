<?php
/* ************************************************************************
' admin_changelist.php -- Administration - List open change requests
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
'		betap 2008-06-29 16:02:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'paging.php');
require_once(LIB_PATH. 'entry.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['transfer_searchraceid']);
	unset($_SESSION['transfer_searchinclude']);
	$_SESSION['transfer_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['transfer_searchraceid'] = $_POST['raceid'];
	$_SESSION['transfer_searchinclude'] = $_POST['transfer_searchinclude'];
	$_SESSION['transfer_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['transfer_currentpage']) ? $_SESSION['transfer_currentpage'] : 1;
} else {
	$_SESSION['transfer_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

$searchraceid = "";
if (isset($_SESSION['transfer_searchraceid'])) {
	$searchraceid = $_SESSION['transfer_searchraceid'];
}
$searchinclude = "";
if (isset($_SESSION['transfer_searchinclude'])) {
	$searchinclude = $_SESSION['transfer_searchinclude'];
}

$races = Race::findAllExtended();
if (empty($searchraceid)) {
	$thisrace = $races[0];
	$searchraceid = $thisrace->id;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
		<!--[if IE 6]>
		<style>
		#innerwrapper { zoom: 1; }
		</style>
		<![endif]-->
	</head>

	<body>
		<div id="pagecontainer">
			<div id="maincontainer">
				<div id="innerwrapper">
					<div class="windowcontentcolumn0">
						<?php
						if (!empty($searchraceid)) {
							$itemcount = Transfer::countAll($searchraceid, $searchinclude);
						}
						$paging = new Paging($currentpage, 100, $itemcount); ?>
						<div class="floatleft">
							<h2>Change/Transfer/Offer List</h2>
						</div>
						<div class="floatright" style="text-align: right;">
							<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
							change/transfer/offer list<br /><br /><br />
						</div>
						<div class="clear"></div>
						<form method="post" name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<div class="searchblock">
								<div class="windowcontentcolumn1">
									<strong>Change/Transfer/Offer Search</strong>
									<div class="clear"></div><br />
									<div class="formcaption">Race:</div>
									<div class="formfield">
										<select name="raceid">
											<?php
											foreach ($races as $race) { ?>
												<option value="<?php echo $race->id; ?>"<?php if ($race->id == $searchraceid) { echo ' selected="selected"'; } ?>><?php echo $race->formatdate. " ". $race->name; ?></option>
											<?php
											} ?>
										</select>
									</div>
									<div class="clear"></div>
									<div class="formcaption">&nbsp;</div>
									<div class="formfield">
										<input type="submit" name="filter" value="Search" />&nbsp;&nbsp;&nbsp;(<span class="stylered">changes are shown below</span>)
									</div>
								</div>
								<div class="windowcontentcolumn2">
									<div class="formfield">Include:<br />
										<input type="radio" name="transfer_searchinclude" value="open"<?php if ($searchinclude == 'open' || empty($searchinclude)) { echo ' checked="checked"'; } ?> />
											Open Only<br />
										<input type="radio" name="transfer_searchinclude" value="all"<?php if ($searchinclude == 'all') { echo ' checked="checked"'; } ?> />
											All<br />
										<input type="radio" name="transfer_searchinclude" value="superceded"<?php if ($searchinclude == 'superceded') { echo ' checked="checked"'; } ?> />
											Superceded Only<br />
										<input type="radio" name="transfer_searchinclude" value="closed"<?php if ($searchinclude == 'closed') { echo ' checked="checked"'; } ?> />
											Closed Only<br />
										<input type="radio" name="transfer_searchinclude" value="offered"<?php if ($searchinclude == 'offered') { echo ' checked="checked"'; } ?> />
											Offered Entries Only<br />
									</div>
								</div>
							</div>
							<div class="clear"></div><br /><br />
								
							<table width="100%" cellpadding="2" cellspacing="0">
								<tr class="darkgreybg">
									<td width="50" align="left"><strong>Code</strong></td>
									<td width="100"><strong>Type</strong></td>
									<td width="80" align="center"><strong>Offered</strong></td>
									<td width="180"><strong>Original Name</strong></td>
									<td width="120"><strong>
										<?php
										if (isset($thisrace->teamsenabled) && $thisrace->teamsenabled &&
												isset($thisrace->clubsenabled) && $thisrace->clubsenabled) {
											echo 'Old Club/Team';
										} elseif (isset($thisrace->teamsenabled) && $thisrace->teamsenabled) {
											echo 'Old Team';
										} else {
											echo 'Old Club';
										} ?>
									</strong></td>
									<td width="120"><strong>
										<?php
										if (isset($thisrace->teamsenabled) && $thisrace->teamsenabled &&
												isset($thisrace->clubsenabled) && $thisrace->clubsenabled) {
											echo 'New Club/Team';
										} elseif (isset($thisrace->teamsenabled) && $thisrace->teamsenabled) {
											echo 'New Team';
										} else {
											echo 'New Club';
										} ?>
									</strong></td>
									<td width="50" align="center"><strong>Actions</strong></td>
								</tr>
								<?php
								if ($paging->pagecount() == 0) { ?>
									<tr class="<?php echo rowColour ?>bg">
										<td colspan="7" align="center"><br /><br /><strong>No changes found</strong></td>
									</tr>
								<?php
								} else {
									$transfers = Transfer::findAllByPage($searchraceid, $paging, $searchinclude);
									foreach ($transfers as $transfer) { ?>
										<tr class="<?php echo rowColour ?>bg">
											<td class="smalltext" align="left"><a href="admin_changeview.php?id=<?php echo $transfer->id; ?>"><?php echo $transfer->code; ?></a></td>
											<td class="smalltext">
												<?php
												if ($transfer->offerready || empty($transfer->newgender)) {
													echo 'Offer';
												} else {
													echo 'Trans/Chg';
												}
												if ($transfer->superceded) {
													echo '(Sup)';
												} else {
													if ($transfer->offertoentryid != 0 && ! $transfer->complete) {
														echo '(Offered)';
													} else {
														if (empty($transfer->newgender)) {
															if (!$transfer->offerready) {
																echo '(Unconfirmed)';
															} else {
																echo '(Not-Offered)';
															}
														} else {
															if (!$transfer->complete) {
																echo '(Open)';
															}
														}
													}
												} ?>
											</td>
											<td class="smalltext" align="center"><?php echo $transfer->offereddate != 0 ? date('d M Y H:i:s', strtotime($transfer->offereddate)) : '&nbsp;'; ?></td>
											<td class="smalltext"><?php echo $transfer->oldfirstname. " ". $transfer->oldsurname; ?></td>
											<td class="smalltext">
												<?php
												$teamname = '&nbsp;';
												if (isset($thisrace->teamsenabled) && $thisrace->teamsenabled &&
														isset($thisrace->clubsenabled) && $thisrace->clubsenabled) {
													if (!empty($transfer->oldteam) && !empty($transfer->oldclubname)) {
														$teamname = $transfer->oldclubname. '/'. $transfer->oldteam;
													} elseif (!empty($transfer->oldteam)) {
														$teamname = $transfer->oldteam;
													} else {
														$teamname = $transfer->oldclubname;
													}
												} elseif (isset($thisrace->teamsenabled) && $thisrace->teamsenabled) {
													$teamname = $transfer->oldteam;
												} else {
													$teamname = $transfer->oldclubname;
												}
												echo $teamname; ?>
											</td>
											<td class="smalltext">
												<?php
												$teamname = '&nbsp;';
												if (isset($thisrace->teamsenabled) && $thisrace->teamsenabled &&
														isset($thisrace->clubsenabled) && $thisrace->clubsenabled) {
													if (!empty($transfer->newteam) && !empty($transfer->newclubname)) {
														$teamname = $transfer->newclubname. '/'. $transfer->newteam;
													} elseif (!empty($transfer->newteam)) {
														$teamname = $transfer->newteam;
													} else {
														$teamname = $transfer->newclubname;
													}
												} elseif (isset($thisrace->teamsenabled) && $thisrace->teamsenabled) {
													$teamname = $transfer->newteam;
												} else {
													$teamname = $transfer->newclubname;
												}
												echo $teamname; ?>
											</td>
											<td class="smalltext" align="center"><a href="admin_changeclose.php?id=<?php echo $transfer->id ?>"><?php echo empty($transfer->offereddate) ? 'close' : 're-offer'; ?></a></td>
										</tr>
									<?php
									}
								} ?>
							</table>
							<?php
							echo $paging->pagingNav($_SERVER['PHP_SELF']); ?>
						</form>
					</div>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>