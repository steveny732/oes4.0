<?php
/* ************************************************************************
' admin_uploadresults1.php -- Administration - Upload Results #1
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
'		betap 2008-07-18 13:46:20
'		New script.
'
' ************************************************************************ */
require_once('includes/initialise.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
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

	<body onload="self.focus();document.form1.filename.focus()">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Upload Results</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						upload results<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form action="admin_uploadresults2.php" method="post" name="form1" id="form1" enctype="multipart/form-data">
						<div class="formcaption">Results CSV File:</div>
						<div class="formfield"><input type="file" name="filename" size="50" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Clear Results for Race:</div>
						<div class="formfield"><input type="checkbox" name="clear" /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Submit" />
						<input type="hidden" name="formID" value="form1" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>