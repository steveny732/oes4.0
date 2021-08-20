<?php
/* ************************************************************************
' admin_formfieldlist.php -- Administration - List Form Fields for Maintenance
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
require_once("includes/initialise.php");
require_once(LIB_PATH. 'paging.php');
require_once(LIB_PATH. 'formfield.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'ren') {

	$p_count = 10;
	$formfields = FormField::findAllForForm($_SESSION['formid']);
	foreach ($formfields as $formfield) {
		$formfield->sequence = $p_count;
		$formfield->save();
		$p_count = $p_count + 10;
	}

	redirect_to($_SERVER['PHP_SELF']);
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['formfield_searchtext']);
	$_SESSION['formfield_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['formfield_searchtext'] = $_POST['formfield_searchtext'];
	$_SESSION['formfield_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['formfield_currentpage']) ? $_SESSION['formfield_currentpage'] : 1;
} else {
	$_SESSION['formfield_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

if (isset($_REQUEST['formid'])) {
	$_SESSION['formid'] = $_REQUEST['formid'];
}
$formid = $_SESSION['formid'];

$searchtext = "";
if (isset($_SESSION['formfield_searchtext'])) {
	$searchtext = $_SESSION['formfield_searchtext'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script src="scripts/AC_RunActiveContent.js" type="text/javascript"></script>
		<script type="text/javascript" src="scripts/general.js"></script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onload="self.focus();document.form1.formfield_searchtext.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					$itemcount = FormField::countAll($formid, $searchtext);
					$paging = new Paging($currentpage, 17, $itemcount); ?>
					<div class="floatleft">
						<h2>Form Field List</h2>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							Search:&nbsp;<input type="text" name="formfield_searchtext" value="<?php echo $searchtext; ?>" />
							<input type="submit" name="filter" value="Go" />
						</form>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_formlist.php">form list</a>&nbsp;&gt;&nbsp;
						form field list<br /><br /><br />
						<a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=ren">re-number</a>&nbsp;
						<a href="admin_formfieldadd.php">add form field</a>
					</div>
					<div class="clear"></div><br />
					<table width="100%" cellpadding="2" cellspacing="0">
						
						<tr class="darkgreybg">
							<td width="20" align="center"><strong>Seq.</strong></td>
							<td width="50" align="center"><strong>Show?</strong></td>
							<td width="50"><strong>Type</strong></td>
							<td width="230"><strong>Caption</strong></td>
							<td width="100"><strong>Control</strong></td>
							<td width="100"><strong>Database Name</strong></td>
							<td width="100" align="center"><strong>Actions</strong></td>
						</tr>
						<?php
						if ($paging->pagecount() == 0) { ?>
							<tr class="<?php echo $paging->colourNewRow(); ?>">
								<td colspan="7" align="center"><br /><br /><strong>No form fields found.</strong></td>
							</tr>
						<?php
						} else {
							$formfields = FormField::findAllByPage($formid, $paging, $searchtext);
							foreach ($formfields as $formfield) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td class="smalltext" align="center">
										<a href="admin_formfieldupdate.php?id=<?php echo $formfield->id; ?>"><?php echo $formfield->sequence; ?></a>
									</td>
									<td class="smalltext" align="center">
										<?php
										echo $formfield->show ? "Yes" : "No"; ?>
									</td>
									<td class="smalltext"><?php echo $formfield->type; ?></td>
									<td class="smalltext"><?php echo $formfield->caption; ?></td>
									<td class="smalltext"><?php echo $formfield->control; ?></td>
									<td class="smalltext"><?php echo $formfield->dbname; ?></td>
									<td class="smalltext">
										<?php
										if (Language::countAll() > 1) { ?>
											<a href="admin_formfieldtrans.php?id=<?php echo $formfield->id; ?>">translate</a>&nbsp;
										<?php
										} ?>
										<a href="admin_formfielddelete.php?id=<?php echo $formfield->id; ?>">delete</a>
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
	</body>
</html>