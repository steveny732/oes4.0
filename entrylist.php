<?php
/* ************************************************************************
' entrylist.php -- Public Entry List Display
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
'		betap 2008-06-29 21:22:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once("includes/initialise.php");
require_once(LIB_PATH. "paging.php");
require_once(LIB_PATH. "entry.php");

if (!defined('DB_SERVER') || $currentSite->downformaint && !$session->isLoggedIn()) {
	redirect_to("downformaint.php");
}

$url = $_SERVER['SCRIPT_NAME'];
$url = substr($url, strrpos($url, "/") + 1, strlen($url) - strrpos($url, "/"));

$contents = Content::findByURL($url, $cookieLanguageCode);
$content = array_shift($contents);

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['entrylist_searchraceid']);
	unset($_SESSION['entrylist_searchsurname']);
	unset($_SESSION['entrylist_searchteamname']);
	unset($_SESSION['entrylist_searchclubid']);
	unset($_SESSION['entrylist_searchsort']);
	$_SESSION['entrylist_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['entrylist_searchraceid'] = htmlspecialchars($_POST['raceid'], ENT_QUOTES);
	$_SESSION['entrylist_searchsurname'] = htmlspecialchars($_POST['searchsurname'], ENT_QUOTES);
	$_SESSION['entrylist_searchteamname'] = htmlspecialchars($_POST['searchteamname'], ENT_QUOTES);
	$_SESSION['entrylist_searchclubid'] = htmlspecialchars($_POST['searchclubid'], ENT_QUOTES);
	$_SESSION['entrylist_searchsort'] = htmlspecialchars($_POST['searchsort'], ENT_QUOTES);
	$_SESSION['entrylist_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}
if (!empty($_REQUEST['raceid'])) {
	$_SESSION['entrylist_searchraceid'] = htmlspecialchars($_REQUEST['raceid'], ENT_QUOTES);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['entrylist_currentpage']) ? $_SESSION['entrylist_currentpage'] : 1;
} else {
	$_SESSION['entrylist_currentpage'] = htmlspecialchars($_REQUEST['page'], ENT_QUOTES);
	redirect_to($_SERVER['PHP_SELF']);
}

$searchraceid = $_SESSION['entrylist_searchraceid'];
$searchsurname = $_SESSION['entrylist_searchsurname'];
$searchteamname = $_SESSION['entrylist_searchteamname'];
$searchclubid = $_SESSION['entrylist_searchclubid'];
$searchsort = $_SESSION['entrylist_searchsort'];

$races = Race::findAllFuture($currentSite->id, $cookieLanguageCode);

$JS = '';
$p_noracesfound = false;
if (empty($races)) {
	$p_noracesfound = true;
	$searchraceid = '';
} else {
	$JS = '<script type="text/javascript">function loadRaces(){var d=new Date();var strText;var i=0;';
	foreach ($races as $race) {
		if (empty($searchraceid)) {
			$searchraceid = $race->id;
		}
		if ($searchraceid == $race->id) {
			$thisrace = $race;
		}
		$JS .= 'd.setFullYear('. $race->year(). ','. ($race->month() - 1). ','. $race->day(). ');strText=dojo.date.locale.format(d,{selector:\'date\',formatLength:\'medium\',locale:\''. $cookieLanguageCode. '\'}) + \' '. $race->name. '\';document.form1.raceid.options[i++]=new Option(strText,'. $race->id. ');';
	}
}

$clubs = Entry::findAllUsedClubs($searchraceid);
$searchracenumber = '';
$searchagecategorycode = '';
$searchtype = '';
$itemcount = Entry::countAll($searchraceid, $searchsurname, $searchracenumber, $searchagecategorycode, $searchteamname, $searchclubid, $searchtype);
$paging = new Paging($currentpage, 100, $itemcount);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $currentSite->title; ?></title>
		<?php $dojoParse = 'false';
		include(LIB_PATH. 'dojo.php'); ?>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script src="scripts/AC_RunActiveContent.js" type="text/javascript"></script>
		<script src="SpryAssets/SpryMenuBar.js" type="text/javascript"></script>
		<script type="text/javascript">
			dojo.require("dojo.parser");
			dojo.require("dojo.date.locale");
			dojo.require("dijit.form.CheckBox");
			dojo.require("dijit.form.TextBox");
			dojo.require("dijit.form.Select");
			dojo.require("dijit.form.FilteringSelect");
			
			dojo.addOnLoad(function() {
				loadRaces();
				dojo.parser.parse();
				dijit.byId('searchsurname').focus();
				dijit.byId('raceid').setValue('<?php echo $searchraceid; ?>');
			});
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
		<!--[if IE 6]>
		<style>
		#innerwrapper { zoom: 1; }
		</style>
		<![endif]-->
	</head>

	<body class="tundra">
		<div id="pagecontainer">
			<div id="windowcontainer">
				<div id="innerwrapper">
					<div class="windowcontentcolumn0">
						<div class="floatleft">
							<?php
							echo $content->text;	// <h2>Entry List</h2>
							$content = array_shift($contents); ?>
						</div>
						<div class="floatright">
							<a href="#" onClick="window.close();" style="color:#000000; text-decoration:none"><?php echo $content->text; ?>&nbsp;<img src="images/close_icon.gif" width="8" height="8" alt="<?php echo $content->text; ?>" /></a>
						</div>
						<div class="clear"></div>
						<form method="post" name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<div class="searchblock">
								<div class="windowcontentcolumn1">
									<div class="formcaption">
										<?php
										$content = array_shift($contents);
										echo '<strong>'. $content->text. '</strong>';
										$content = array_shift($contents); ?>
									</div>
									<div class="clear"></div>
									<div class="formcaption">
										<?php
										echo $content->text; // Race:
										$content = array_shift($contents);
										$p_noraces = $content->text; // No races set up
										$content = array_shift($contents); ?>
									</div>
									<div class="formfield">
										<select name="raceid" id="raceid" tabindex="1" style="width: 250px;" dojoType="dijit.form.Select">
											<?php
											if ($p_noracesfound) { ?>
												<option value="" selected="selected"><?php echo $p_noraces; ?></option>
											<?php
											} ?>
										</select>
									</div>
									<div class="clear"></div>
									<div class="formcaption">
										<?php
										echo $content->text; // Surname:
										$content = array_shift($contents); ?>
									</div>
									<div class="formfield"><input name="searchsurname" type="text" id="searchsurname" value="<?php echo $searchsurname; ?>" tabindex="2" style="width: 200px;" dojoType="dijit.form.TextBox" propercase="true" /></div>
									<div class="clear"></div>
									<?php
									if ($thisrace->teamsenabled) { ?>
										<div class="formcaption">
											<?php
											echo $content->text; // Team: ?>
										</div>
										<div class="formfield"><input name="searchteamname" type="text" id="searchteamname" value="<?php echo $searchteamname; ?>" tabindex="3" style="width: 200px;" dojoType="dijit.form.TextBox" propercase="true" /></div>
										<div class="clear"></div>
									<?php
									}
									$content = array_shift($contents); ?>
									<?php
									if ($thisrace->clubsenabled) { ?>
										<div class="formcaption">
											<?php
											echo $content->text; // Club:
											$content = array_shift($contents);
											$p_unattached = $content->text; // Unattached ?>
										</div>
										<div class="formfield">
											<select name="searchclubid" id="searchclubid" tabindex="4" style="width: 250px;" dojoType="dijit.form.FilteringSelect">
												<option value=""<?php if ($searchclubid == '') { echo ' selected="selected"'; } ?>></option>
												<option value="0"<?php if ($searchclubid == '0') { echo ' selected="selected"'; } ?>>
													<?php
													echo $p_unattached; // Unattached ?>
												</option>
												<?php
												foreach ($clubs as $club) { ?>
													<option value="<?php echo $club->id; ?>"<?php if ($searchclubid == $club->id) { echo ' selected="selected"'; } ?>><?php echo $club->name; ?></option>
												<?php
												} ?>
											</select>
										</div>
										<div class="clear"></div>
									<?php
									} else {
										$content = array_shift($contents);
									}
									$content = array_shift($contents); ?>
									<div class="formcaption">&nbsp;</div>
									<div class="formfield">
										<input type="submit" name="filter" tabindex="8" value="<?php echo $content->text; ?>" />
										<?php
										$content = array_shift($contents);
										echo '&nbsp;&nbsp;&nbsp;(<span class="stylered">';
										echo $content->text; // Entries are shown below
										echo '</span>)';
										$content = array_shift($contents); ?>
									</div>
								</div>
								<div class="windowcontentcolumn2">
									<div class="formfield">
										<?php
										echo $content->text. '<br />'; // Sort entries by:
										$content = array_shift($contents); ?>
										<input type="radio" name="searchsort" tabindex="5" value="surname"<?php if ($searchsort == 'surname' || empty($searchsort)) { echo ' checked="checked"'; } ?> dojotype="dijit.form.RadioButton" />
										<?php
										echo $content->text. '<br />'; // Surname (alphabetical)
										$content = array_shift($contents);
										if ($thisrace->teamsenabled) { ?>
											<input type="radio" name="searchsort" tabindex="6" value="teamname"<?php if ($searchsort == "teamname") { echo ' checked="checked"'; } ?> dojotype="dijit.form.RadioButton" />
											<?php
											echo $content->text. '<br />'; // Team
										}
										$content = array_shift($contents);
										if ($thisrace->clubsenabled) { ?>
											<input type="radio" name="searchsort" tabindex="7" value="clubname"<?php if ($searchsort == "clubname") { echo ' checked="checked"'; } ?> dojotype="dijit.form.RadioButton" />
											<?php
											echo $content->text; // Club (unattached show last)
										}
										$content = array_shift($contents); ?>
									</div>
								</div>
								<div class="clear"></div>
								<?php
								echo $content->text; // Please note etc..
								$content = array_shift($contents);
								$p_counts = $content->text;
								$content = array_shift($contents);
								$p_previous = $content->text;
								$content = array_shift($contents);
								$p_next = $content->text;
								?>
							</div>
						</form>
						<?php
						echo $paging->pagingNav($_SERVER['PHP_SELF']); ?>
						<strong id="racename">
							<?php
							$JS .= 'var d=new Date('. $thisrace->year(). ','. ($thisrace->month() - 1). ','. $thisrace->day(). ');document.getElementById(\'racename\').innerHTML=dojo.date.locale.format(d,{selector:\'date\',formatLength:\'medium\',locale:\''. $cookieLanguageCode. '\'}) + \' '. $thisrace->name. ':\';'; ?>
						</strong><br />
						<div class="clear"></div>
						<?php
						$content = array_shift($contents);
						$p_number = $content->text;
						$content = array_shift($contents);
						$p_name = $content->text;
						$content = array_shift($contents);
						$p_cat = $content->text;
						$content = array_shift($contents);
						$p_team = $content->text;
						$content = array_shift($contents);
						$p_club = $content->text;
						$content = array_shift($contents);
						$p_empty = $content->text; ?>

						<table width="100%" cellspacing="0" cellpadding="2">
							<tr class="darkgreybg">
								<td><strong><?php echo $p_number; ?></strong></td>
								<td align="left"><strong><?php echo $p_name; ?></strong></td>
								<td><strong><?php echo $p_cat; ?></strong></td>
								<?php
								if ($thisrace->teamsenabled && $thisrace->clubsenabled) { ?>
									<td align="left"><strong><?php echo $p_club. '/'. $p_team; ?></strong></td>
								<?php
								} elseif ($thisrace->teamsenabled) { ?>
									<td align="left"><strong><?php echo $p_team; ?></strong></td>
								<?php
								} else { ?>
									<td align="left"><strong><?php echo $p_club; ?></strong></td>
								<?php
								} ?>
							</tr>
							<?php
							if ($paging->pagecount() == 0) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td colspan="5" align="center"><br /><strong><?php echo $p_empty; ?></strong></td>
								</tr>
							<?php
							} else {
								$entries = Entry::findAllByPage($searchraceid, $paging, $searchsurname, $searchracenumber, $searchagecategorycode, $searchteamname, $searchclubid, $searchtype, $searchsort);
								foreach ($entries as $entry) { ?>
									<tr class="<?php echo $paging->colourNewRow(); ?>">
										<td class="smalltext"><?php if (!empty($entry->racenumber)) { echo $entry->racenumber; } ?></td>
										<td class="smalltext" align="left"><?php echo strtoupper($entry->surname); ?>, <?php echo $entry->firstname; ?></td>
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
				include(LIB_PATH. "inc_windowfooter.php"); ?>
			</div>
		</div>
		<?php
		$JS .= '}</script>';
		echo $JS; ?>
	</body>
</html>