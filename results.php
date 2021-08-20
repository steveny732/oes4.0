<?php
/* ************************************************************************
' results.php -- Display Race Results
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
'		betap 2008-07-18 13:31:31
'		New script for race result display.
'
' ************************************************************************ */
require_once("includes/initialise.php");
require_once(LIB_PATH. "content.php");
require_once(LIB_PATH. "race.php");

if (!defined('DB_SERVER') || $currentSite->downformaint && !$session->isLoggedIn()) {
	redirect_to("downformaint.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['results_raceid']);
	unset($_SESSION['results_name']);
	unset($_SESSION['results_agecategory']);
	unset($_SESSION['results_clubid']);
	unset($_SESSION['results_clubname']);
	unset($_SESSION['results_racenumber']);
	unset($_SESSION['results_sort']);
	$_SESSION['results_currentpage'] = 1;
}

if (isset($_REQUEST['raceid'])) {
	$_SESSION['results_raceid'] = htmlspecialchars($_REQUEST['raceid'], ENT_QUOTES);
}

if(isset($_POST['filter'])) {
	$_SESSION['results_name'] = htmlspecialchars($_POST['name'], ENT_QUOTES);
	$_SESSION['results_agecategory'] = htmlspecialchars($_POST['agecategory'], ENT_QUOTES);
	$_SESSION['results_clubid'] = htmlspecialchars($_POST['clubid'], ENT_QUOTES);
	$_SESSION['results_clubname'] = htmlspecialchars($_POST['clubname'], ENT_QUOTES);
	$_SESSION['results_racenumber'] = htmlspecialchars($_POST['racenumber'], ENT_QUOTES);
	$_SESSION['results_sort'] = htmlspecialchars($_POST['sort'], ENT_QUOTES);
	$_SESSION['results_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['results_currentpage']) ? $_SESSION['results_currentpage'] : 1;
} else {
	$_SESSION['results_currentpage'] = htmlspecialchars($_REQUEST['page'], ENT_QUOTES);
	redirect_to($_SERVER['PHP_SELF']);
}
if (substr($_SESSION['results_raceid'], 0, 1) == 'O') {
	$source = 'old';
} else {
	$source = '';
}

$searchraceid = "";
if (isset($_SESSION['results_raceid'])) {
	$searchraceid = substr($_SESSION['results_raceid'], 1);
}
$searchname = "";
if (isset($_SESSION['results_name'])) {
	$searchname = $_SESSION['results_name'];
}
$searchagecategory = "";
if (isset($_SESSION['results_agecategory'])) {
	$searchagecategory = $_SESSION['results_agecategory'];
}
$searchclubid = "";
if (isset($_SESSION['results_clubid'])) {
	$searchclubid = $_SESSION['results_clubid'];
}
$searchclubname = "";
if (isset($_SESSION['results_clubname'])) {
	$searchclubname = $_SESSION['results_clubname'];
}
$searchracenumber = "";
if (isset($_SESSION['results_racenumber'])) {
	$searchracenumber = $_SESSION['results_racenumber'];
}
$searchsort = "";
if (isset($_SESSION['results_sort'])) {
	$searchsort = $_SESSION['results_sort'];
}

$url = $_SERVER['SCRIPT_NAME'];
$url = substr($url, strrpos($url, "/") + 1, strlen($url) - strrpos($url, "/"));

if (!empty($searchraceid)) {
	$url .= '?raceid='. $searchraceid;
} else {
	$url .= '?raceid=1';
}

$contents = Content::findByURL('results', $cookieLanguageCode);
$content = array_shift($contents);

$races = Race::findAllWithResults($currentSite->id, $cookieLanguageCode);
if (empty($searchraceid)) {
	$race = $races[0];
	$searchraceid = $race->id;
}
foreach ($races as $race) {
	if ($searchraceid == $race->id) {
		$thisrace = $race;
		break;
	}
}

if (!empty($searchraceid)) {
	if ($source == 'old') {
		$agecategories = OldResult::findAllAgeCategories($searchraceid);
		$clubs = OldResult::findAllClubs($searchraceid);
		$gotAgeGrades = OldResult::gotAgeGrades($searchraceid);
		$itemcount = OldResult::countAll($searchraceid, $searchname, $searchagecategory, $searchclubid, $searchracenumber);
		$paging = new Paging($currentpage, 100, $itemcount);
		$results = OldResult::findAllByPage($searchraceid, $paging, $searchname, $searchagecategory, $searchclubname, $searchracenumber, $searchsort);
	} else {
		$agecategories = Result::findAllAgeCategories($searchraceid);
		$clubs = Result::findAllClubs($searchraceid);
		$gotAgeGrades = Result::gotAgeGrades($searchraceid);
		$itemcount = Result::countAll($searchraceid, $searchname, $searchagecategory, $searchclubid, $searchracenumber);
		$paging = new Paging($currentpage, 100, $itemcount);
		$results = Result::findAllByPage($searchraceid, $paging, $searchname, $searchagecategory, $searchclubid, $searchracenumber, $searchsort);
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $currentSite->title; ?></title>
		<?php $dojoParse = "false";
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

			function initMenu() {
				var myMenuBar = new Spry.Widget.MenuBar('myMenuBar', {imgDown:'SpryAssets/SpryMenuBarDownHover.gif', imgRight:'SpryAssets/SpryMenuBarRightHover.gif'});
			}
			
			dojo.addOnLoad(function() {
				dojo.parser.parse();
				loadHeading();
				dijit.byId('name').focus();
			});
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<link href="SpryAssets/SpryMenuBarVertical.css" rel="stylesheet" type="text/css" />
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body class="tundra" onload="initMenu();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div id="banner">
					<img src="<?php echo $currentSite->bannerimgurl; ?>" />
				</div>
				<?php
				include(LIB_PATH. "inc_language.php"); ?>
				<div id="menucolumn">
					<img src="<?php echo $currentSite->clubimgurl; ?>" /><br />
					<?php
					include(LIB_PATH. "inc_menu.php"); ?>
				</div>
				<div id="contentcolumn">
					<?php
					$p_heading = $content->text;
					if (!empty($searchraceid)) {
						$headingJS = '<script type="text/javascript">function loadHeading(){var d=new Date('. $thisrace->year(). ', '. ($thisrace->month() - 1). ', '. $thisrace->day(). ');var strText=dojo.date.locale.format(d,{selector:\'date\',formatLength:\'long\',locale:\''. $cookieLanguageCode. '\'});';
						$p_heading = str_replace('*RACEDATE', '<span id="racedate"></span>', $p_heading); // Race Results
						$p_heading = str_replace('*RACENAME', $thisrace->name, $p_heading);
						$headingJS .= 'document.getElementById(\'raceheading\').innerHTML = \''. $p_heading. '\';';
						$headingJS .= 'document.getElementById(\'racedate\').innerHTML = strText;';
						$headingJS .= '}</script>';
						echo $headingJS;
					}?>
					<h2 id="raceheading"></h2>
					<?php
					$content = array_shift($contents); ?>
					<form method="post" name="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="searchblock">
							<div class="windowcontentcolumn1">
								<?php
								echo '<strong>'. $content->text. '</strong>';
								$content = array_shift($contents); ?><br />
								<div class="formcaption">
									<?php
									echo $content->text; //Race:
									$content = array_shift($contents); ?>
								</div>
								<div class="formfield">
									<select name="raceid" id="raceid" tabindex="1" style="width: 200px;" dojoType="dijit.form.Select">
										<?php
										foreach ($races as $race) { ?>
											<option value="<?php echo $race->xraceid; ?>"<?php if ($_SESSION['results_raceid'] == $race->xraceid) { echo ' selected="selected"'; } ?>><?php echo $race->name; ?></option>
										<?php
										} ?>
									</select>
								</div>
								<div class="clear"></div>
								<div class="formcaption">
									<?php
									echo $content->text; // First OR Last Name:
									$content = array_shift($contents);
									$p_category = $content->text;
									$content = array_shift($contents);
									$p_malecode = $content->text;
									$content = array_shift($contents);
									$p_allmale = $content->text;
									$content = array_shift($contents);
									$p_femalecode = $content->text;
									$content = array_shift($contents);
									$p_allfemale = $content->text;
									$content = array_shift($contents);
									$p_club = $content->text;
									$content = array_shift($contents);
									$p_racenumber1 = $content->text;
									$content = array_shift($contents);
									$p_search = $content->text;
									$content = array_shift($contents);
									$p_entries = $content->text;
									$content = array_shift($contents);
									$p_sortby = $content->text;
									$content = array_shift($contents);
									$p_time = $content->text;
									$content = array_shift($contents);
									$p_chiptime = $content->text;
									$content = array_shift($contents);
									$p_surname = $content->text;
									$content = array_shift($contents);
									$p_racenumber2 = $content->text;
									$content = array_shift($contents);
									$p_agegrade = $content->text;
									$content = array_shift($contents); ?>
								</div>
								<div class="formfield"><input name="name" type="text" id="name" value="<?php echo $searchname; ?>" dojoType="dijit.form.TextBox" /></div>
								<div class="clear"></div>
								<div class="formcaption">
									<?php
									echo $p_category; // Category: ?>
								</div>
								<div class="formfield">
									<select name="agecategory" id="agecategory" style="width: 150px;" dojoType="dijit.form.FilteringSelect">
										<option value=""<?php if (empty($searchagecategory)) { echo ' selected="selected"'; } ?>></option>
										<option value="<?php echo $p_malecode; ?>"<?php if ($searchagecategory == $p_malecode) { echo ' selected="selected"'; } ?>><?php echo $p_allmale; ?></option>
										<option value="<?php echo $p_femalecode; ?>"<?php if ($searchagecategory == $p_femalecode) { echo ' selected="selected"'; } ?>><?php echo $p_allfemale; ?></option>
										<?php
										foreach ($agecategories as $agecategory) { ?>
											<option value="<?php echo $agecategory->agecategorycode; ?>"<?php if ($searchagecategory == $agecategory->agecategorycode) { echo ' selected="selected"'; } ?>><?php echo $agecategory->agecategorycode; ?></option>
										<?php
										} ?>
									</select>
								</div>
								<div class="clear"></div>
								<?php
								if (!$thisrace->teamsenabled) { ?>
									<div class="formcaption"><?php echo $p_club; ?></div>
									<div class="formfield">
										<?php
										if ($source == 'old') { ?>
											<select name="clubname" id="clubname" style="width: 200px;" dojoType="dijit.form.FilteringSelect">
												<option value=""<?php if (empty($searchclubname)) { echo ' selected="selected"'; } ?>></option>
												<?php
												foreach ($clubs as $club) { ?>
													<option value="<?php echo $club->clubname; ?>"<?php if ($searchclubname == $club->clubname) { echo ' selected="selected"'; } ?>><?php echo $club->clubname; ?></option>
												<?php
												} ?>
											</select>
										<?php
										} else { ?>
											<select name="clubid" id="clubid" style="width: 200px;" dojoType="dijit.form.FilteringSelect">
												<option value=""<?php if (empty($searchclubid)) { echo ' selected="selected"'; } ?>></option>
												<?php
												foreach ($clubs as $club) { ?>
													<option value="<?php echo $club->clubid; ?>"<?php if ($searchclubid == $club->clubid) { echo ' selected""selected"'; } ?>><?php echo $club->clubname; ?></option>
												<?php
												} ?>
											</select>
										<?php
										} ?>
									</div>
									<div class="clear"></div>
								<?php
								}
								if ($thisrace->allocatenumbers) { ?>
									<div class="formcaption"><?php echo $p_racenumber1; ?></div>
									<div class="formfield"><input name="racenumber" type="text" id="racenumber" value="<?php echo $searchracenumber; ?>" dojoType="dijit.form.TextBox" /></div>
									<div class="clear"></div>
								<?php
								} ?>
								<div class="formcaption">&nbsp;</div>
								<div class="formfield">
									<input type="submit" name="filter" value="<?php echo $p_search; ?>" />
									<?php
									echo '&nbsp;(<span class="stylered">'. $p_entries. '</span>)'; // Entries are shown below ?>
								</div>
							</div>
							<div class="windowcontentcolumn2"> 
								<?php
								echo $p_sortby. '<br />'; // Sort results by: ?>
								<input name="sort" type="radio" value="pos"<?php if ($searchsort == 'pos' || empty($searchsort)) { echo ' checked="checked"'; } ?> dojotype="dijit.form.RadioButton" />
								<?php
								if (empty($thisrace->timetext)) {
									echo $p_time. '<br />'; // Gun time (official finish position)
								} else {
									echo $thisrace->timetext. '<br />'; // Gun time (official finish position)
								}
								if ($thisrace->chiptimes > 0) { ?>
									<input type="radio" name="sort" value="chiptime"<?php if ($searchsort == "chiptime") { echo ' checked="checked"'; } ?> dojotype="dijit.form.RadioButton" />
								<?php
									echo $thisrace->chiptimetext. '<br />'; // Chip time
								}
								if ($thisrace->chiptimes > 1) { ?>
									<input type="radio" name="sort" value="chiptime2"<?php if ($searchsort == "chiptime2") { echo ' checked="checked"'; } ?> dojotype="dijit.form.RadioButton" />
									<?php
									echo $thisrace->chiptime2text. '<br />'; // Chip time 2
								}
								if ($thisrace->chiptimes > 2) { ?>
									<input type="radio" name="sort" value="chiptime3"<?php if ($searchsort == "chiptime3") { echo ' checked="checked"'; } ?> dojotype="dijit.form.RadioButton" />
									<?php
									echo $thisrace->chiptime3text. '<br />'; // Chip time 3
								}
								if ($thisrace->chiptimes > 3) { ?>
									<input type="radio" name="sort" value="chiptime4"<?php if ($searchsort == "chiptime4") { echo ' checked="checked"'; } ?> dojotype="dijit.form.RadioButton" />
									<?php
									echo $thisrace->chiptime4text. '<br />'; // Chip time 4
								}
								if ($thisrace->chiptimes > 4) { ?>
									<input type="radio" name="sort" value="chiptime5"<?php if ($searchsort == "chiptime5") { echo ' checked="checked"'; } ?> dojotype="dijit.form.RadioButton" />
									<?php
									echo $thisrace->chiptime5text. '<br />'; // Chip time 5
								}
								if ($thisrace->chiptimes > 5) { ?>
									<input type="radio" name="sort" value="chiptime6"<?php if ($searchsort == "chiptime6") { echo ' checked="checked"'; } ?> dojotype="dijit.form.RadioButton" />
									<?php
									echo $thisrace->chiptime6text. '<br />'; // Chip time 6
								} ?>
								<input type="radio" name="sort" value="name"<?php if ($searchsort == 'name') { echo ' checked="checked"'; } ?> dojotype="dijit.form.RadioButton" />
								<?php
								echo $p_surname. '<br />'; // Surname (alphabetical)
								if ($thisrace->allocatenumbers) { ?>
									<input type="radio" name="sort" value="racenumber"<?php if ($searchsort == 'racenumber') { echo ' checked="checked"'; } ?> dojotype="dijit.form.RadioButton" />
									<?php
									echo $p_racenumber2. '<br />'; // Race number
								}
								if ($gotAgeGrades) { ?>
									<input type="radio" name="sort" value="agegrade"<?php if ($searchsort == 'agegrade') { echo ' checked="checked"'; } ?> dojotype="dijit.form.RadioButton" />
									<?php
									echo $p_agegrade; // Age grade
								} ?>
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
							$content = array_shift($contents);
							$p_pos = $content->text;
							$content = array_shift($contents);
							$p_time = $content->text;
							$content = array_shift($contents);
							$p_chiptime = $content->text;
							$content = array_shift($contents);
							$p_name = $content->text;
							$content = array_shift($contents);
							$p_team = $content->text;
							$content = array_shift($contents);
							$p_clubname = $content->text;
							$content = array_shift($contents);
							$p_agecategory = $content->text;
							$content = array_shift($contents);
							$p_racenumber = $content->text;
							$content = array_shift($contents);
							$p_agegrade = $content->text;
							$content = array_shift($contents);
							$p_noresults = $content->text;
							?>
						</div>
					</form>
					<div class="clear"></div>
					<?php
					if (!empty($searchraceid)) {
						echo $paging->pagingNav($_SERVER['PHP_SELF']);
					}
					$p_nocols = 3; ?>
					<table width="100%" cellspacing="0" cellpadding="2">
						<tr class="darkgreybg">
							<td width="30" align="center"><strong><?php echo $p_pos; ?></strong></td>
							<td width="50" align="center"><strong>
								<?php
								if (empty($thisrace->timetext)) {
									echo $p_time; // Gun time
								} else {
									echo $thisrace->timetext;
								} ?>
							</strong></td>
							<?php
							if ($thisrace->chiptimes > 0) {
								$p_nocols++; ?>
								<td width="50" align="center"><strong><?php echo $thisrace->chiptimetext; ?></strong></td>
							<?php
							}
							if ($thisrace->chiptimes > 1) {
								$p_nocols++; ?>
								<td width="50" align="center"><strong><?php echo $thisrace->chiptime2text; ?></strong></td>
							<?php
							}
							if ($thisrace->chiptimes > 2) {
								$p_nocols++; ?>
								<td width="50" align="center"><strong><?php echo $thisrace->chiptime3text; ?></strong></td>
							<?php
							}
							if ($thisrace->chiptimes > 3) {
								$p_nocols++; ?>
								<td width="50" align="center"><strong><?php echo $thisrace->chiptime4text; ?></strong></td>
							<?php
							}
							if ($thisrace->chiptimes > 4) {
								$p_nocols++; ?>
								<td width="50" align="center"><strong><?php echo $thisrace->chiptime5text; ?></strong></td>
							<?php
							}
							if ($thisrace->chiptimes > 5) {
								$p_nocols++; ?>
								<td width="50" align="center"><strong><?php echo $thisrace->chiptime6text; ?></strong></td>
							<?php
							} ?>
							<td width="200"><strong><?php echo $p_name; ?></strong></td>
							<?php
							if ($thisrace->teamsenabled && $thisrace->clubsenabled) {
								$p_nocols++; ?>
								<td width="150"><strong><?php echo $p_clubname. '/'. $p_team; ?></strong></td>
							<?php
							} elseif ($thisrace->teamsenabled) {
								$p_nocols++; ?>
								<td width="150"><strong><?php echo $p_team; ?></strong></td>
							<?php
							} else {
								$p_nocols++; ?>
								<td width="150"><strong><?php echo $p_clubname; ?></strong></td>
							<?php
							} ?>
							<td width="50" align="center"><strong><?php echo $p_agecategory; ?></strong></td>
							<?php
							if ($thisrace->allocatenumbers) {
								$p_nocols++; ?>
								<td width="10"><strong><?php echo $p_racenumber; ?></strong></td>
							<?php
							}
							if ($gotAgeGrades) {
								$p_nocols++; ?>
								<td width="30" align="center"><strong><?php echo $p_agegrade; ?></strong></td>
							<?php
							} ?>
						</tr>
						<?php
						if (!empty($searchraceid)) {
							if ($paging->pagecount() == 0) { ?>
								<tr align="center" class="<?php echo $paging->colourNewRow(); ?>">
									<td colspan="<?php echo $p_nocols; ?>"><br /><br /><strong><?php echo $p_noresults; ?></strong></td>
								</tr>
							<?php
							} else {
								foreach ($results as $result) {
									$col = 1; ?>
									<tr valign="top" class="<?php echo $paging->colourNewRow(); ?>"> 
										<td class="smalltext<?php echo $col++ % 2; ?>" align="center"><?php echo $result->pos; ?></td>
										<td class="smalltext<?php echo $col++ % 2; ?>"><?php echo $result->time; ?></td>
										<?php
										if ($thisrace->chiptimes > 0) { ?>
											<td class="smalltext<?php echo $col++ % 2; ?>"><?php echo $result->chiptime; ?></td>
										<?php
										}
										if ($thisrace->chiptimes > 1) { ?>
											<td class="smalltext<?php echo $col++ % 2; ?>"><?php echo $result->chiptime2; ?></td>
										<?php
										} 
										if ($thisrace->chiptimes > 2) { ?>
											<td class="smalltext<?php echo $col++ % 2; ?>"><?php echo $result->chiptime3; ?></td>
										<?php
										} 
										if ($thisrace->chiptimes > 3) { ?>
											<td class="smalltext<?php echo $col++ % 2; ?>"><?php echo $result->chiptime4; ?></td>
										<?php
										} 
										if ($thisrace->chiptimes > 4) { ?>
											<td class="smalltext<?php echo $col++ % 2; ?>"><?php echo $result->chiptime5; ?></td>
										<?php
										} 
										if ($thisrace->chiptimes > 5) { ?>
											<td class="smalltext<?php echo $col++ % 2; ?>"><?php echo $result->chiptime6; ?></td>
										<?php
										} ?>
										<td class="smalltext<?php echo $col++ % 2; ?>"><?php echo $result->firstname. ' '. strtoupper($result->surname); ?></td>
										<?php
										if ($thisrace->teamsenabled && $thisrace->clubsenabled) { ?>
											<td class="smalltext<?php echo $col++ % 2; ?>">
												<?php
												if (!empty($result->team)) {
													echo $result->team;
												} else {
													echo $result->clubname;
												} ?>
											</td>
										<?php
										} elseif ($thisrace->teamsenabled) { ?>
											<td class="smalltext<?php echo $col++ % 2; ?>"><?php echo $result->team; ?></td>
										<?php
										} else { ?>
											<td class="smalltext<?php echo $col++ % 2; ?>"><?php echo $result->clubname; ?></td>
										<?php
										} ?>
										<td class="smalltext<?php echo $col++ % 2; ?>"><?php echo $result->agecategorycode; ?></td>
										<?php
										if ($thisrace->allocatenumbers) { ?>
											<td class="smalltext<?php echo $col++ % 2; ?>" align="right"><?php echo $result->racenumber; ?></td>
										<?php
										}
										if ($gotAgeGrades) { ?>
											<td class="smalltext<?php echo $col++ % 2; ?>" align="center"><?php echo number_format($result->agegrade, 2); ?>%</td>
										<?php
										} ?>
									</tr>
								<?php
								}
							}
						}?>
					</table>
					<?php
					if (!empty($searchraceid)) {
						echo $paging->pagingNav($_SERVER['PHP_SELF']);
					} ?>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>