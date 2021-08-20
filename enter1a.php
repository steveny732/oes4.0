<?php
/* ************************************************************************
' enter1a.php -- Public Entry Process #1a - Select Race Type
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

if (!defined('DB_SERVER') || $currentSite->downformaint && !$session->isLoggedIn()) {
	redirect_to("downformaint.php");
}

$url = $_SERVER['SCRIPT_NAME'];
$url = substr($url, strrpos($url, "/") + 1, strlen($url) - strrpos($url, "/"));

$contents = Content::findByURL($url, $cookieLanguageCode);
$content = array_shift($contents);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<?php
		include(LIB_PATH. 'dojo.php'); ?>
		<script type="text/javascript">
			dojo.require("dijit.form.FilteringSelect");
		</script>
		<script type="text/javascript">
			function toggleForm() {
				document.form1.clubtypeid.value = document.getElementById("clubtype_" + dijit.byId('formid').value).value
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body class="tundra">
		<div id="pagecontainer">
			<div id="windowcontainer">
				<div class="windowcontentcolumn0">
					<?php
					echo $content->text;
					$content = array_shift($contents); ?>
					<form action="enter2.php" method="post" name="form1">
						<input type="hidden" name="entrantid" value="<?php if (!empty($_SESSION['entrantid']) && !empty($_REQUEST['entrantid'])) { echo htmlspecialchars($_REQUEST['entrantid'], ENT_QUOTES); } ?>" />
						<div class="formcaption"><?php echo $content->text; ?></div>
						<div class="formfield">
							<select name="formid" id="formid" onChange="toggleForm();" dojoType="dijit.form.FilteringSelect">
								<?php
								$p_savedFormID = '';
								
								// Only list forms associated with active races with available places
								
								$races = Race::findAllFutureByForm($currentSite->id, $cookieLanguageCode);

								$savedForms = '';
								foreach ($races as $race) {
									$noofentries = Entry::countAllPaid($race->id);
									$noofpreallocations = Preallocation::countAllUnused($race->id);
									$numberleft = $race->limit - $noofentries - $noofpreallocations;
									if ($numberleft > 0 || !$race->allocatenumbers) {
										if ($race->formid != $p_savedFormID) {
											$p_savedFormID = $race->formid;
											echo '<option value="'. $race->formid. '">'. $race->formname. '</option>';
											$savedForms .= '<input type="hidden" name="clubtype_'. $race->formid. '" id="clubtype_'. $race->formid. '" value="'. $race->clubtypeid. '" />';
										}
									}
								} ?>
							</select>
							<?php
							echo $savedForms; ?>
						</div><br /><br /><br /><br />
						<input type="hidden" name="clubtypeid" value="">
						<div class="buttonblock">
							<?php
							$content = array_shift($contents); ?>
							<div class="button"><a href="javascript:window.close();"><?php echo $content->text; ?></a></div>
							<?php
							$content = array_shift($contents); ?>
							<div class="button"><a href="javascript:document.form1.submit();"><?php echo $content->text; ?></a></div>
						</div><br /><br />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_windowfooter.php"); ?>
			</div>
		</div>
	</body>
</html>