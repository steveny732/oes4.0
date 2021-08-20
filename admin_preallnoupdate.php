<?php
/* ************************************************************************
' admin_preallnoupdate.php -- Administration - Update a pre-allocation
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
'		betap 2008-06-29 20:27:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'preallocation.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$preallno = Preallocation::findById($_REQUEST['id']);

if (!empty($_POST['submit'])) {

	$preallno->clubid = $_POST['clubid'];
	$preallno->save();

	redirect_to("admin_preallnolist.php");
}

$clubs = Club::findAllForRace($_SESSION['raceid']);
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
	</head>

	<body onload="self.focus();document.form1.clubid.focus()">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Update Preallocation</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_racelist.php">race list</a>&nbsp;&gt;&nbsp;
						<a href="admin_preallnolist.php">preallocation list</a>&nbsp;&gt;&nbsp;
						update preallocation<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Club:</div>
						<div class="formfield">
							<select name="clubid">
								<option value="0"<?php if (empty($preallono->clubid)) { echo ' selected="selected"'; } ?>>Administration</option>
								<?php
								foreach ($clubs as $club) { ?>
									<option value="<?php echo $club->id; ?>"<?php if ($preallono->clubid == $club->id) { echo ' selected="selected"'; } ?>><?php echo $club->name ?></option>
								<?php
								} ?>
							</select>
						</div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Confirm Update" />&nbsp;&nbsp;
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />
						<input type="hidden" name="id" value="<?php echo $preallno->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>