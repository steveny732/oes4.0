<?php
/* ************************************************************************
' admin_entrylist.php -- Administration - Entry list
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
'  betap 2008-07-14 08:17:32
'   Incorporate waiting list and unpaid lists.
'  betap 2008-06-29 16:02:00
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
	unset($_SESSION['entry_searchraceid']);
	unset($_SESSION['entry_searchsurname']);
	unset($_SESSION['entry_searchracenumber']);
	unset($_SESSION['entry_searchagecatcode']);
	unset($_SESSION['entry_searchteamname']);
	unset($_SESSION['entry_searchclubid']);
	unset($_SESSION['entry_searchtype']);
	unset($_SESSION['entry_searchsort']);
	$_SESSION['entry_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['entry_searchraceid'] = $_POST['raceid'];
	$_SESSION['entry_searchsurname'] = $_POST['surname'];
	$_SESSION['entry_searchracenumber'] = $_POST['racenumber'];
	$_SESSION['entry_searchagecatcode'] = $_POST['agecatcode'];
	$_SESSION['entry_searchteamname'] = $_POST['teamname'];
	$_SESSION['entry_searchclubid'] = $_POST['clubid'];
	$_SESSION['entry_searchtype'] = $_POST['type'];
	$_SESSION['entry_searchsort'] = $_POST['sort'];
	$_SESSION['entry_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['entry_currentpage']) ? $_SESSION['entry_currentpage'] : 1;
} else {
	$_SESSION['entry_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

if (isset($_SESSION['entry_searchraceid'])) {
	$searchraceid =  $_SESSION['entry_searchraceid'];
}
$searchsurname = "";
if (isset($_SESSION['entry_searchsurname'])) {
	$searchsurname = $_SESSION['entry_searchsurname'];
}
$searchracenumber = "";
if (isset($_SESSION['entry_searchracenumber'])) {
	$searchracenumber = $_SESSION['entry_searchracenumber'];
}
$searchagecatcode = "";
if (isset($_SESSION['entry_searchagecatcode'])) {
	$searchagecatcode = $_SESSION['entry_searchagecatcode'];
}
$searchteamname = "";
if (isset($_SESSION['entry_searchteamname'])) {
	$searchteamname = $_SESSION['entry_searchteamname'];
}
$searchclubid = "";
if (isset($_SESSION['entry_searchclubid'])) {
	$searchclubid = $_SESSION['entry_searchclubid'];
}
$searchtype = "0";
if (isset($_SESSION['entry_searchtype'])) {
	$searchtype = $_SESSION['entry_searchtype'];
}
$searchsort = "0";
if (isset($_SESSION['entry_searchsort'])) {
	$searchsort = $_SESSION['entry_searchsort'];
}

$races = Race::findAllExtended();
if (empty($searchraceid)) {
	$thisrace = $races[0];
	$searchraceid = $thisrace->id;
}
if (!empty($searchraceid)) {
	$agecategories = Entry::findAllUsedAgeCategories($searchraceid, $searchtype);
	$clubs = Entry::findAllUsedClubs($searchraceid, $searchtype);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
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

	<body onload="self.focus();document.form1.surname.focus()">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div id="innerwrapper">
					<div class="windowcontentcolumn0">
						<?php
						if (!empty($searchraceid)) {
							$itemcount = Entry::countAll($searchraceid, $searchsurname, $searchracenumber, $searchagecatcode, $searchteamname, $searchclubid, $searchtype);
						}
						$paging = new Paging($currentpage, 100, $itemcount); ?>
						<div class="floatleft">
							<h2>Entry List</h2>
						</div>
						<div class="floatright" style="text-align: right;">
							<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
							entry list<br /><br /><br />
						</div>
						<div class="clear"></div><br />
						<form method="post" name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<div class="searchblock">
								<div class="windowcontentcolumn1">
									<div class="formcaption"><strong>Entry Search</strong></div>
									<div class="clear"></div><br />
									<div class="formcaption">Race:</div>
									<div class="formfield">
										<select name="raceid">
											<?php
											foreach ($races as $race) {
												if ($race->id == $searchraceid) {
													$thisrace = $race;
												} ?>
												<option value="<?php echo $race->id; ?>"<?php if ($race->id == $searchraceid) { echo ' selected="selected"'; } ?>><?php echo $race->formatdate. " ". $race->name; ?></option>
											<?php
											} ?>
										</select>
									</div>
									<div class="clear"></div>
									<div class="formcaption">Surname:</div>
									<div class="formfield"><input name="surname" type="text" id="surname" value="<?php echo $searchsurname; ?>" /></div>
									<div class="clear"></div>
									<div class="formcaption">Race Number:</div>
									<div class="formfield"><input name="racenumber" type="text" id="racenumber" value="<?php echo $searchracenumber; ?>" /></div>
									<div class="clear"></div>
									<div class="formcaption">Category:</div>
									<div class="formfield">
										<select name="agecatcode" id="agecatcode">
											<option value=""<?php if (empty($searchagecatcode)) { echo ' selected="selected"'; } ?>></option>
											<?php
											foreach ($agecategories as $agecategory) { ?>
												<option value="<?php echo $agecategory->agecategorycode; ?>"<?php if ($agecategory->agecategorycode == $searchagecatcode) { echo ' selected="selected"'; } ?>><?php echo $agecategory->agecategorycode; ?></option>
											<?php
											} ?>
										</select>
									</div>
									<div class="clear"></div>
									<?php
									if ($thisrace->teamsenabled) { ?>
										<div class="formcaption">Team:</div>
										<div class="formfield"><input name="teamname" type="text" id="teamname" value="<?php echo $searchteamname; ?>" /></div>
										<div class="clear"></div>
									<?php
									}
									if ($thisrace->clubsenabled) { ?>
										<div class="formcaption">Club:</div>
										<div class="formfield">
											<select name="clubid" id="clubid">
												<option value=""<?php if ($searchclubid == '') { echo ' selected="selected"'; } ?>></option>
												<option value="0"<?php if ($searchclubid == '0') { echo ' selected="selected"'; } ?>>Unattached</option>
												<?php
												foreach ($clubs as $club) { ?>
													<option value="<?php echo $club->id; ?>"<?php if ($club->id == $searchclubid) { echo ' selected="selected"'; } ?>><?php echo $club->name; ?></option>
												<?php
												} ?>
											</select>
										</div>
										<div class="clear"></div>
									<?php
									} ?>
									<div class="formcaption">List Type:</div>
									<div class="formfield">
										<select name="type" id="type">
											<option value=""<?php if ($searchtype == '') { echo ' selected="selected"'; } ?>>Entries</option>
											<option value="0"<?php if ($searchtype == '0') { echo ' selected="selected"'; } ?>>Unpaid</option>
											<option value="1"<?php if ($searchtype == "1") { echo ' selected="selected"'; } ?>>Waiting List</option>
										</select>
									</div>
									<div class="clear"></div>
									<div class="formcaption">&nbsp;</div>
									<div class="formfield">
										<input type="submit" name="submit1" value="Search" />&nbsp;&nbsp;&nbsp;(<span class="stylered">entries are shown below</span>)
										<input type="hidden" name="filter" value="filter" />
									</div>
								</div>
								<div class="windowcontentcolumn2">
									<div class="formfield">Sort entries by:<br />
										<input type="radio" name="sort" value="surname"<?php if ($searchsort == 'surname' || empty($searchsort)) { echo ' checked="checked"'; } ?> />
										Surname (alphabetical)<br />
										<input type="radio" name="sort" value="agecategorycode"<?php if ($searchsort == "agecategorycode") { echo ' checked="checked"'; } ?> />
										Category<br />
										<?php
										if ($thisrace->teamsenabled) { ?>
											<input type="radio" name="sort" value="teamname"<?php if ($searchsort == "teamname") { echo ' checked="checked"'; } ?> />
											Team<br />
										<?php
										}
										if ($thisrace->clubsenabled) { ?>
											<input type="radio" name="sort" value="clubname"<?php if ($searchsort == "clubname") { echo ' checked="checked"'; } ?> />
											Club (unattached show last)<br />
										<?php
										} ?>
										<input type="radio" name="sort" value="racenumber"<?php if ($searchsort == "racenumber") { echo ' checked="checked"'; } ?> />
										Race number<br />
										<input type="radio" name="sort" value="date"<?php if ($searchsort == "date") { echo ' checked="checked"'; } ?> />
										Sequence received
									</div>
								</div>
								<div class="clear"></div>
								Please note <strong>you do not need to fill in all the fields</strong>. You could enter just your club to see all the entries for your club or you can leave all the fields blank to view all the entries.
							</div>
						</form>
						<?php
						if ($paging->pagecount() > 0) { ?>
							<br /><strong><?php echo $thisrace->formatdate. ' '. $thisrace->name; ?>:</strong>
						<?php
						} ?>
						<div class="clear"></div>
						<table width="100%" cellspacing="0" cellpadding="2">
							<tr class="darkgreybg">
								<td width="40"><strong>No.</strong></td>
								<td width="150" align="left"><strong>Name</strong></td>
								<td width="100" align="left"><strong>Order</strong></td>
								<td width="50"><strong>Cat</strong></td>
								<td width="200" align="left"><strong>
									<?php
									if ($thisrace->teamsenabled && $thisrace->clubsenabled) {
										echo 'Club/Team';
									} elseif ($thisrace->teamsenabled) {
										echo 'Team';
									} else {
										echo 'Club';
									} ?>
									</strong>
								</td>
								<td width="100"><strong>Action</strong></td>
							</tr>
							<?php
							if ($paging->pagecount() == 0) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td colspan="6" align="center"><br /><br /><strong>No entries found.</strong></td>
								</tr>
							<?php
							} else {
								$entries = Entry::findAllByPage($searchraceid, $paging, $searchsurname, $searchracenumber, $searchagecatcode, $searchteamname, $searchclubid, $searchtype, $searchsort);
								foreach ($entries as $entry) { ?>
									<tr class="<?php echo $paging->colourNewRow(); ?>">
										<td class="smalltext"><?php if ($entry->racenumber != 0) { echo $entry->racenumber; } ?></td>
										<td class="smalltext" align="left"><a href="admin_entryupdate1.php?id=<?php echo $entry->id; ?>"><?php echo strtoupper($entry->surname). ', '. $entry->firstname; ?></a></td>
										<td class="smalltext"><?php echo $entry->ordernumber; ?></td>
										<td class="smalltext"><?php echo $entry->agecategorycode; ?></td>
										<?php
										$teamname = '&nbsp;';
										if ($thisrace->teamsenabled && $thisrace->clubsenabled) {
											if (!empty($entry->team) && !empty($entry->clubname)) {
												$teamname = $entry->clubname. '/'. $entry->team;
											} elseif (!empty($entry->team)) {
												$teamname = $entry->team;
											} else {
												$teamname = $entry->clubname;
											}
										} elseif ($thisrace->teamsenabled) {
											$teamname = $entry->team;
										} else {
											$teamname = $entry->clubname;
										} ?>
										<td class="smalltext" align="left"><?php echo $teamname; ?></td>
										<td class="smalltext">
											<?php
											if (Change::countAll($entry->id) > 0) { ?>
												<a href="admin_entrychangelist.php?clearfilter=1&entryid=<?php echo $entry->id; ?>">list changes</a>&nbsp;
											<?php
											} ?>
											<a href="admin_entrydelete.php?id=<?php echo $entry->id; ?>">delete</a>
										</td>
									</tr>
									<?php
									}
								} ?>
						</table>
						<?php
						echo $paging->pagingNav($_SERVER['PHP_SELF']); ?>
					</div>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>