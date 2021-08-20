<?php
/* ************************************************************************
' admin_formlist.php -- List the forms for maintenance
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
require_once("includes/initialise.php");
require_once(LIB_PATH. 'paging.php');
require_once(LIB_PATH. 'form.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

if (isset($_REQUEST['clearfilter'])) {
	unset($_SESSION['form_searchname']);
	$_SESSION['form_currentpage'] = 1;
}

if(isset($_POST['filter'])) {
	$_SESSION['form_searchname'] = $_POST['form_searchname'];
	$_SESSION['form_currentpage'] = 1;
	redirect_to($_SERVER['PHP_SELF']);
}

if (empty($_REQUEST['page'])) {
	$currentpage = !empty($_SESSION['form_currentpage']) ? $_SESSION['form_currentpage'] : 1;
} else {
	$_SESSION['form_currentpage'] = $_REQUEST['page'];
	redirect_to($_SERVER['PHP_SELF']);
}

$searchname = "";
if (isset($_SESSION['form_searchname'])) {
	$searchname = $_SESSION['form_searchname'];
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
	</head>

	<body onLoad="self.focus();document.form1.form_searchname.focus();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<?php
					$itemcount = Form::countAll($searchname);
					$paging = new Paging($currentpage, 17, $itemcount); ?>
					<div class="floatleft">
						<h2>Form List</h2>
						<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							Search:&nbsp;<input type="text" name="form_searchname" value="<?php echo $searchname; ?>" />
							<input type="submit" name="filter" value="Go" />
						</form>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						form list<br /><br /><br />
						<a href="admin_formadd.php">add a form</a>
					</div>
					<div class="clear"></div><br />
					<table width="100%" cellpadding="2" cellspacing="0">
						<tr class="darkgreybg">
							<td width="400" align="left"><strong>Name</strong></td>
							<td width="280" align="center"><strong>Action</strong></td>
						</tr>
						<?php
						if ($paging->pagecount() == 0) { ?>
							<tr class="<?php echo $paging->colourNewRow(); ?>">
								<td colspan="2" align="center"><br /><br /><strong>No forms found.</strong></td>
							</tr>
						<?php
						} else {
							$forms = Form::findAllByPage($paging, $searchname);
							foreach ($forms as $form) { ?>
								<tr class="<?php echo $paging->colourNewRow(); ?>">
									<td class="smalltext" align="left"><a href="admin_formupdate.php?id=<?php echo $form->id; ?>"><?php echo $form->name; ?></a></td>
									<td class="smalltext" align="center">
										<a href="admin_formfieldlist.php?clearfilter=1&formid=<?php echo $form->id; ?>">edit form layout</a>&nbsp;
										<?php
										if (Language::countAll() > 1) { ?>
											<a href="admin_formtrans.php?id=<?php echo $form->id; ?>">translate</a>&nbsp;
										<?php
										} ?>
										<a href="admin_formcopy.php?id=<?php echo $form->id; ?>">copy</a>&nbsp;
										<a href="admin_formdelete.php?id=<?php echo $form->id; ?>">delete</a>
									</td>
								</tr>
							<?php
							}
						}
						?>
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