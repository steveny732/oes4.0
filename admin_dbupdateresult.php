<?php
/* ************************************************************************
' admin_dbupdateresult.php -- Show Database Update Result
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
'	Version: 2.0
'	Last Modified By:
'		betap 2008-07-10 17:54:05
'		New script to complete the formal database upgrade procedure.
'
' ************************************************************************ */
require_once("includes/initialise.php");

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body>
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					if ($_REQUEST['status'] == 'OK') { ?>
						<h2>Database Upgraded Successfully</h2>
						The database has been upgraded to version <?php echo $_REQUEST["dbversion"]; ?>.<br /><br />
						Click <a href="admin_loggedin.php">here</a> to continue.<br /><br /><br /><br /><br /><br />
					<?php
					} else if ($_REQUEST['status'] == 'WARN') { ?>
						<h2>Database Upgrade Completed with Problems</h2>
						The database was upgraded to version <?php echo $_REQUEST["dbversion"]; ?>, but it was not possible to write to the configuration file includes/dbconfig.php.<br /><br /><br /><br /><br /><br /><br /><br />
					<?php
					} else { ?>
						<h2>Database Upgrade Failed</h2>
						The database cannot be directly upgraded to version <?php echo $_REQUEST["dbversion"]; ?> from version <?php echo $_REQUEST["currentdbversion"]; ?>.<br /><br />Please upgrade to version <?php echo $_REQUEST["requireddbversion"]; ?> first and then reapply this version.<br /><br /><br /><br /><br /><br /><br /><br />
					<?php } ?>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>