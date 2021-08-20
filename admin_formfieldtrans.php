<?php
/* ************************************************************************
' admin_formfieldtrans.php -- Administration - Translate a Form Field
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
require_once(LIB_PATH. 'formfield.php');
require_once(LIB_PATH. 'formfieldlang.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (isset($_REQUEST['form2']) && $_REQUEST['form2'] == "form2") {
	$_SESSION['languagecode'] = $_REQUEST['languagecode'];
}

$formfieldlang = FormFieldLang::findById($_REQUEST['id'], $_SESSION['languagecode']);

if (!empty($_POST['submit'])) {
	if (!$formfieldlang) {
		$formfieldlang = new FormFieldLang();
	}
	$formfieldlang->formfieldid = $_POST['id'];
	$formfieldlang->languagecode = $_SESSION['languagecode'];
	$formfieldlang->caption = $_POST['caption'];
	$formfieldlang->memo = $_POST['type'] == 'checkbox' ? $_POST['plainmemo'] : $_POST['memo'];
	$formfieldlang->save();

	redirect_to("admin_formfieldlist.php");
}

$languages = Language::findAll();
$formfield = FormField::findById($_REQUEST['id']);

$p_memo = $formfield->memo;
$p_caption = $formfield->caption;

if (!empty($formfieldlang)) {
	$p_memo = $formfieldlang->memo;
	$p_caption = $formfieldlang->caption;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript">
			function checkme() {
				missinginfo = "";
				// No validation
				
				if (missinginfo != "") {
					alert(missinginfo);
					return false;
				} else { 
					return true;
				}
			}
			function toggleFields() {
				// Only show memo box for checkboxes, memos and textboxes
				if ('<?php echo $formfield->type; ?>' == 'checkbox' ||
					'<?php echo $formfield->type; ?>' == 'memo' ||
					'<?php echo $formfield->type; ?>' == 'text') {
					// For checkboxes, the memo field is actually plain text (i.e. not html)
					if ('<?php echo $formfield->type; ?>' == 'checkbox') {
						document.getElementById('memodisplay').style.display = 'none';
						document.getElementById('memoplaindisplay').style.display = '';
					} else {
						document.getElementById('memodisplay').style.display = '';
						document.getElementById('memoplaindisplay').style.display = 'none';
					}
				} else {
					document.getElementById('memodisplay').style.display = 'none';
					document.getElementById('memoplaindisplay').style.display = 'none';
				}
				// Don't show caption for races
				if ('<?php echo $formfield->type; ?>' == 'races') {
					document.getElementById('captiondisplay').style.display = 'none';
					if (document.getElementById('memodisplay').style.display == '') {
						self.focus();
						document.form1.memo.focus();
					} else {
						if (document.getElementById('memoplaindisplay').style.display == '') {
							self.focus();
							document.form1.plainmemo.focus();
						}
					}
				} else {
					document.getElementById('captiondisplay').style.display = '';
					self.focus();
					document.form1.caption.focus();
				}
			}
		</script>
		<script type="text/javascript" src="scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript">
			tinyMCE.init({
				mode : "textareas",
				elements : "ajaxfilemanager",
				theme : "advanced",
				plugins : "advimage,advlink,media,contextmenu",
				file_browser_callback : "ajaxfilemanager",
				content_css : "css/styles.css",
				width : "700px",
				height : "400px",
				relative_urls : true
			});
			function ajaxfilemanager(field_name, url, type, win) {
				var ajaxfilemanagerurl = "../../../../jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php";
				switch (type) {
					case "image":
						break;
					case "media":
						break;
					case "flash": 
						break;
					case "file":
						break;
					default:
						return false;
				}
				tinyMCE.activeEditor.windowManager.open({
						url: "../../../../jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php",
						width: 782,
						height: 440,
						inline : "yes",
						close_previous : "no"
				},{
						window : win,
						input : field_name
				});
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onload="toggleFields();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2>Translate Form Field</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_formlist.php">page list</a>&nbsp;&gt;&nbsp;
						<a href="admin_formfieldlist.php">form field list</a>&nbsp;&gt;&nbsp;
						translate form field<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form2" id="langform2" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Language Name:</div>
						<div class="formfield">
							<select name="languagecode" id="languagecode" onchange="document.form2.submit();">
								<?php
								foreach ($languages as $language) {
									if ($language->code != $currentSite->languagecode) {
										echo '<option value="'. $language->code. '"';
										if (empty($_SESSION['languagecode'])) {
											$_SESSION['languagecode'] = $language->code;
										}
										if ($_SESSION['languagecode'] == $language->code) {
											echo ' selected="selected"';
										}
										echo '>'. $language->name. '</option>';
									}
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<input type="hidden" name="form2" value="form2" />
						<input type="hidden" name="id" value="<?php echo $formfield->id; ?>" />
					</form>
					<form name="form1" method="post" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div id="captiondisplay">
							<div class="formcaption">Caption:</div>
							<div class="formfield"><input type="text" name="caption" maxlength="100" style="width: 200px;" value="<?php echo $p_caption; ?>" /></div>
							<div class="clear"></div>
						</div>
						<div id="memodisplay">
							<div class="formcaption">Memo:</div>
							<div class="clear"></div>
							<textarea name="memo" id="memo"><?php echo $p_memo; ?></textarea>
							<div class="clear"></div>
						</div>
						<div id="memoplaindisplay">
							<div class="formcaption">Memo:</div>
							<div class="formfield"><input type="text" name="plainmemo" id="plainmemo" value="<?php echo $p_memo; ?>" style="width: 400px;" /></div>
							<div class="clear"></div>
						</div>
						<br /><br /><br />
						<input type="submit" name="submit" value="Add/Update" />
						<a href="admin_formfieldlist.php"><input type="button" name="submit2" value="Cancel (back)" /></a>
						<input type="hidden" name="id" value="<?php echo $formfield->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	
	</body>
</html>