<?php
/* ************************************************************************
' admin_download1.php -- Administration - Select Download Criteria
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
require_once(LIB_PATH. 'race.php');
require_once(LIB_PATH. 'series.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$races = Race::findAllExtended();
$series = Series::findAll();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript">
			function checkme() {
				missinginfo = "";
				<?php
				if (!empty($series)) { ?>
					if (document.form1.seriesid.selectedIndex == 0 && document.form1.raceid.selectedIndex == 0) {
						missinginfo += "\nYou must select either a series or a race for download";
					}
					if (document.form1.seriesid.selectedIndex != 0 && document.form1.type.value == "unpaid") {
						missinginfo += "\nSeries download can only be for paid entries";
					}
				<?php
				} else { ?>
					if (document.form1.raceid.selectedIndex == 0) {
						missinginfo += "\nYou must select a race for download";
					}
				<?php
				} ?>
				if (missinginfo != "") {
					alert(missinginfo);
					return false;
				}	else { 
					return true;
				}
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onLoad="self.focus();document.form1.raceid.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Select Information for Download to Excel</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						excel download<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form1" method="post" action="admin_download2.php" onSubmit="return checkme();" />
						<?php
						if (!empty($series)) { ?>
							<div class="formcaption">Series:&nbsp;</div>
							<div class="formfield">
								<select name="seriesid">
									<option value="" selected="selected">None</option>
									<?php
									foreach ($series as $serie) { ?>
										<option value="<?php echo $serie->id; ?>"><?php echo $serie->name; ?></option>
									<?php 
									} ?>
								</select>
							</div>
							<div class="clear"></div><br />
						<?php
						} ?>
						<div class="formcaption">Race:</div>
						<div class="formfield">
							<select name="raceid">
								<option value="" selected="selected">None</option>
								<?php
								foreach ($races as $race) { ?>
									<option value="<?php echo $race->id; ?>"><?php echo $race->formatdate. ' '. $race->name; ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div><br />
						<div class="formcaption">Type:</div>
						<div class="formfield">
							<select name="type">
								<option value="paid" selected="selected">Paid</option>
								<option value="unpaid">Unpaid</option>
							</select>
						</div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="Submit" value="Submit" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>