<?php
/* ************************************************************************
' admin_new1a.php -- Administration - Add an entry #1a
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
require_once(LIB_PATH. "content.php");

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$url = $_SERVER['SCRIPT_NAME'];
$url = substr($url, strrpos($url, "/") + 1, strlen($url) - strrpos($url, "/"));

$contents = Content::findByURL($url, $cookieLanguageCode);
$content = array_shift($contents);

$races = Race::findAllFutureByForm($currentSite->id, $cookieLanguageCode);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript">
			function toggleForm() {
				document.form1.clubtypeid.value = document.getElementById("clubtype_" + document.getElementById('formid').value).value
			}
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

	<body>
		<div id="pagecontainer">
			<div id="maincontainer">
				<div id="innerwrapper">
					<div class="windowcontentcolumn0">
						<?php
						echo $content->text;
						$content = array_shift($contents);
						$savedForms = ''; ?>
						<form method="post" name="form1" action="admin_new1.php">
							<div class="formcaption"><?php echo $content->text; ?></div>
							<div class="formfield">
								<select name="formid" id="formid" onChange="toggleForm();">
									<?php
									foreach ($races as $race) {
										if ($race->formid != $p_savedFormID) {
											$p_savedFormID = $race->formid;
											echo '<option value="'. $race->formid. '">'. $race->formname. '</option>';
											$savedForms .= '<input type="hidden" name="clubtype_'. $race->formid. '" id="clubtype_'. $race->formid. '" value="'. $race->clubtypeid. '" />';
										}
									} ?>
								</select>
							<?php echo $savedForms; ?>
							</div><br /><br /><br /><br />
							<div class="buttonblock">
								<div class="button"><a href="javascript:history.go(-1);">Cancel</a></div>
								<div class="button"><a href="javascript:document.form1.submit();">Continue to step 2</a></div>
							</div><br /><br />
							<input type="hidden" name="clubtypeid" value="">
							<input type="hidden" name="entrantid" value="<?php echo $_REQUEST['entrantid']; ?>" />
							<input type="hidden" name="surname" value="<?php echo $_REQUEST['surname']; ?>" />
						</form>
					</div>
				 </div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>
<script type="text/javascript">
	toggleForm();
</script>