<?php
/* ************************************************************************
' admin_siteupdate.php -- Administration - Update a site
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
'		betap 2008-07-21 20:26:01
'		Add image urls and colours.
'		betap 2008-07-10 22:02:41
'		Add site_entryformurl, site_payee and site_copyright.
'		betap 2008-06-27 20:39:23
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'site.php');

if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

$site = Site::findById($_REQUEST['id']);

if (!empty($_POST['submit'])) {
	
	$site->title = $_POST['title'];
	$site->entrybcc = $_POST['entrybcc'];
	$site->url = $_POST['url'];
	$site->securesiteurl = $_POST['securesiteurl'];
	$site->smtpserver = $_POST['smtpserver'];
	$site->smtpusername = $_POST['smtpusername'];
	$site->smtppassword = $_POST['smtppassword'];
	$site->copyright = $_POST['copyright'];
	$site->bannerimgurl = $_POST['bannerimgurl'];
	$site->clubimgurl = $_POST['clubimgurl'];
	$site->newsimgurl = $_POST['newsimgurl'];
	$site->menucolour = $_POST['menucolour'];
	$site->menutextcolour = $_POST['menutextcolour'];
	$site->menurocolour = $_POST['menurocolour'];
	$site->menutextrocolour = $_POST['menutextrocolour'];
	$site->buttoncolour = $_POST['buttoncolour'];
	$site->buttontextcolour = $_POST['buttontextcolour'];
	$site->buttonrocolour = $_POST['buttonrocolour'];
	$site->buttontextrocolour = $_POST['buttontextrocolour'];
	$site->headingcolour = $_POST['headingcolour'];
	$site->downformaint = isset($_POST['downformaint']) ? 1 : 0;
	$site->paylogactive = isset($_POST['paylogactive']) ? 1 : 0;
	$site->currencycode = $_POST['currencycode'];
	$site->googleanalyticscode = $_POST['googleanalyticscode'];
	$site->languagecode = $_POST['languagecode'];
	$site->homepageid = $_POST['homepageid'];
	$site->emailfrom = $_POST['emailfrom'];
	$site->save();

	redirect_to("admin_sitelist.php");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $currentSite->title; ?></title>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript" src="scripts/prototype.js"></script>
		<script type="text/javascript" src="scripts/colourpicker.js"></script>
		<script type="text/javascript">
			function checkme() {
				missinginfo = "";
				if (document.form1.title.value == "") {
					missinginfo += "\nYou must enter a site title";
				}
				if (document.form1.url.value == "") {
					missinginfo += "\nYou must enter a site URL";
				}
				if (document.form1.homepageid.value == "") {
					missinginfo += "\nYou must select a site home page";
				}
				if (document.form1.googleanalyticscode.value == "") {
					missinginfo += "\nYou must enter a Google Analytics ID (or *NONE)";
				} else {
					if (document.form1.googleanalyticscode.value.toUpperCase() == "*NONE") {
						document.form1.googleanalyticscode.value = document.form1.googleanalyticscode.value.toUpperCase();
					}
				}
				if (document.form1.bannerimgurl.value == "") {
					missinginfo += "\nYou must enter a banner image URL";
				}
				if (document.form1.clubimgurl.value == "") {
					missinginfo += "\nYou must enter a club image URL";
				}
				if (document.form1.newsimgurl.value == "") {
					missinginfo += "\nYou must enter a news image URL";
				}
				if (document.form1.emailfrom.value == "") {
					missinginfo += "\nYou must enter a sender email address (or *NONE)";
				} else {
					if (document.form1.emailfrom.value.toUpperCase() == "*NONE") {
						document.form1.emailfrom.value = document.form1.emailfrom.value.toUpperCase();
					} else {
						document.form1.emailfrom.value = document.form1.emailfrom.value.toLowerCase();
					}
				}
				if (document.form1.entrybcc.value == "") {
					missinginfo += "\nYou must enter an email address for entry BCCs (or *NONE)";
				} else {
					if (document.form1.entrybcc.value.toUpperCase() == "*NONE") {
						document.form1.entrybcc.value = document.form1.entrybcc.value.toUpperCase();
					} else {
						document.form1.entrybcc.value = document.form1.entrybcc.value.toLowerCase();
					}
				}
				if (document.form1.smtpserver.value == "") {
					missinginfo += "\nYou must enter an SMTP server (or *NONE)";
				}
				if (document.form1.smtpserver.value.toUpperCase() == "*NONE") {
					document.form1.smtpserver.value = document.form1.smtpserver.value.toUpperCase();
				}
				if (document.form1.smtpserver.value != "*NONE") {
					if (document.form1.smtpusername.value == "") {
						missinginfo += "\nYou must enter an SMTP username";
					}
					if (document.form1.smtppassword.value == "") {
						missinginfo += "\nYou must enter an SMTP password";
					}
				}
				if (document.form1.copyright.value == "") {
					missinginfo += "\nYou must enter a site copyright message";
				}
				if (missinginfo != "") {
					alert(missinginfo);
					return false;
				} else { 
					return true;
				}
			}
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<style type="text/css">
			table.colorPicker {
				position: absolute;
				background-color: #FFFFFF;
				border: solid thin #000000;
				z-index: 1;
			}
			table.colorPicker td {
				width: 15px;
				height: 15px;
				border: solid 1px #FFFFFF;
			}
		</style>
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body onload="self.focus();document.form1.title.focus()">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<div class="floatleft">
						<h2 id="heading">Update Site</h2>
					</div>
					<div class="floatright" style="text-align: right;">
						<a href="admin_loggedin.php">admin menu</a>&nbsp;&gt;&nbsp;
						<a href="admin_sitelist.php">site list</a>&nbsp;&gt;&nbsp;
						update site<br /><br /><br />
					</div>
					<div class="clear"></div><br />
					<form name="form1" method="post" onsubmit="return checkme();" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">Site Title:</div>
						<div class="formfield"><input type="text" name="title" maxlength="50" style="width: 200px;" value="<?php echo $site->title; ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Site URL:</div>
						<div class="formfield"><input type="text" name="url" maxlength="200" style="width: 200px;" value="<?php echo $site->url; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Site Home Page:</div>
						<div class="formfield">
							<select name="homepageid">
								<?php
								$pages = Page::findAllPublic();
								foreach ($pages as $page) {
									echo '<option value="'. $page->id. '"';
									if ($site->homepageid == $page->id) {
										echo ' selected="selected"';
									}
									echo '>'. $page->name. '</option>';
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<div class="formcaption">Secure Site URL:</div>
						<div class="formfield"><input type="text" name="securesiteurl" maxlength="200" style="width: 200px;" value="<?php echo $site->securesiteurl; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Google Analytics ID:</div>
						<div class="formfield"><input type="text" name="googleanalyticscode" maxlength="200" style="width: 200px;" value="<?php echo $site->googleanalyticscode; ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Site Currency:</div>
						<div class="formfield">
							<select name="currencycode">
								<?php
								$currencies = Currency::findAll();
								foreach ($currencies as $currency) {
									echo '<option value="'. $currency->code. '"';
									if ($site->currencycode == $currency->code) {
										echo ' selected="selected"';
									}
									echo '>'. $currency->name. '</option>';
								} ?>
							</select>
						</div>
						<div class="clear"></div>
						<div class="formcaption">Base Language/Locale:</div>
						<div class="formfield">
							<select name="languagecode">
								<?php
								$languages = Language::findAll();
								foreach ($languages as $language) {
									echo '<option value="'. $language->code. '"';
									if ($site->languagecode == $language->code) {
										echo ' selected="selected"';
									}
									echo '>'. $language->name. '</option>';
								} ?>
							</select>
							</div>
						<div class="clear"></div><br />
						<div class="formcaption">Site Banner Image:</div>
						<div class="formfield"><input type="text" name="bannerimgurl" maxlength="200" style="width: 200px;" value="<?php echo $site->bannerimgurl; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Club Image:</div>
						<div class="formfield"><input type="text" name="clubimgurl" maxlength="200" style="width: 200px;" value="<?php echo $site->clubimgurl; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">News Image:</div>
						<div class="formfield"><input type="text" name="newsimgurl" maxlength="200" style="width: 200px;" value="<?php echo $site->newsimgurl; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Menu Colour:</div>
						<div class="formfield"><input type="text" name="menucolour" id="colour1" maxlength="7" style="width: 50px;" value="<?php echo $site->menucolour; ?>" />&nbsp;<a href="#" id="pickcolour1">choose colour</a></div>
						<div class="clear"></div>
						<div class="formcaption">Menu Text Colour:</div>
						<div class="formfield"><input type="text" name="menutextcolour" id="colour2" maxlength="7" style="width: 50px;" value="<?php echo $site->menutextcolour; ?>" />&nbsp;<a href="#" id="pickcolour2">choose colour</a>&nbsp;
							<span id="demo1" style="display: inline-block; width: 200px; background-color: <?php if (empty($site->menucolour)) { echo "#FF9933"; } else { echo $site->menucolour; } ?>; color: <?php if (empty($site->menutextcolour)) { echo "#000000"; } else { echo $site->menutextcolour; } ?>;">&nbsp;Example Menu Item</span></div>
						<div class="clear"></div>
						<div class="formcaption">Menu Rollover Colour:</div>
						<div class="formfield"><input type="text" name="menurocolour" id="colour3" maxlength="7" style="width: 50px;" value="<?php echo $site->menurocolour; ?>" />&nbsp;<a href="#" id="pickcolour3">choose colour</a></div>
						<div class="clear"></div>
						<div class="formcaption">Menu Text Rollover Colour:</div>
						<div class="formfield"><input type="text" name="menutextrocolour" id="colour4" maxlength="7" style="width: 50px;" value="<?php echo $site->menutextrocolour; ?>" />&nbsp;<a href="#" id="pickcolour4">choose colour</a>&nbsp;
							<span id="demo2" style="display: inline-block; width: 200px; background-color: <?php if (empty($site->menurocolour)) { echo "#FFB953"; } else { echo $site->menurocolour; } ?>; color: <?php if (empty($site->menutextrocolour)) { echo "#000000"; } else { echo $site->menutextrocolour; } ?>;">&nbsp;Example Menu Item Rollover</span></div>
						<div class="clear"></div>
						<div class="formcaption">Button Colour:</div>
						<div class="formfield"><input type="text" name="buttoncolour" id="colour5" maxlength="7" style="width: 50px;" value="<?php echo $site->buttoncolour; ?>" />&nbsp;<a href="#" id="pickcolour5">choose colour</a></div>
						<div class="clear"></div>
						<div class="formcaption">Button Text Colour:</div>
						<div class="formfield"><input type="text" name="buttontextcolour" id="colour6" maxlength="7" style="width: 50px;" value="<?php echo $site->buttontextcolour; ?>" />&nbsp;<a href="#" id="pickcolour6">choose colour</a>&nbsp;
							<span id="demo3" style="display: inline-block; width: 200px; background-color: <?php if (empty($site->buttoncolour)) { echo "#FF9933"; } else { echo $site->buttoncolour; } ?>; color: <?php if (empty($site->buttontextcolour)) { echo "#000000"; } else { echo $site->buttontextcolour; } ?>;">&nbsp;Example Button</span></div>
						<div class="clear"></div>
						<div class="formcaption">Button Rollover Colour:</div>
						<div class="formfield"><input type="text" name="buttonrocolour" id="colour7" maxlength="7" style="width: 50px;" value="<?php echo $site->buttonrocolour; ?>" />&nbsp;<a href="#" id="pickcolour7">choose colour</a></div>
						<div class="clear"></div>
						<div class="formcaption">Button Text Rollover Colour:</div>
						<div class="formfield"><input type="text" name="buttontextrocolour" id="colour8" maxlength="7" style="width: 50px;" value="<?php echo $site->buttontextrocolour; ?>" />&nbsp;<a href="#" id="pickcolour8">choose colour</a>&nbsp;
							<span id="demo4" style="display: inline-block; width: 200px; background-color: <?php if (empty($site->buttonrocolour)) { echo "#FFB953"; } else { echo $site->buttonrocolour; } ?>; color: <?php if (empty($site->buttontextrocolour)) { echo "#000000"; } else { echo $site->buttontextrocolour; } ?>;">&nbsp;Example Button Rollover</span></div>
						<div class="clear"></div>
						<div class="formcaption">Heading Colour:</div>
						<div class="formfield"><input type="text" name="headingcolour" id="colour9" maxlength="7" style="width: 50px;" value="<?php echo $site->headingcolour; ?>" />&nbsp;<a href="#" id="pickcolour9">choose colour</a></div>
						<div class="clear"></div><br />
						<div class="formcaption">Sender Email:</div>
						<div class="formfield"><input type="text" name="emailfrom" maxlength="100" style="width: 200px;" value="<?php echo $site->emailfrom; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">Entry BCC:</div>
						<div class="formfield"><input type="text" name="entrybcc" maxlength="100" style="width: 200px;" value="<?php echo $site->entrybcc; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">SMTP Server:</div>
						<div class="formfield"><input type="text" name="smtpserver" maxlength="50" style="width: 200px;" value="<?php echo $site->smtpserver; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">SMTP Username:</div>
						<div class="formfield"><input type="text" name="smtpusername" maxlength="50" style="width: 200px;" value="<?php echo $site->smtpusername; ?>" /></div>
						<div class="clear"></div>
						<div class="formcaption">SMTP Password:</div>
						<div class="formfield"><input type="password" name="smtppassword" maxlength="50" style="width: 200px;" value="<?php echo $site->smtppassword; ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Copyright:</div>
						<div class="formfield"><input type="text" name="copyright" maxlength="50" style="width: 200px;" value="<?php echo $site->copyright; ?>" /></div>
						<div class="clear"></div><br />
						<div class="formcaption">Log Payments?:</div>
						<div class="formfield"><input type="checkbox" name="paylogactive"<?php if ($site->paylogactive) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div>
						<div class="formcaption">Down for Maintenance?:</div>
						<div class="formfield"><input type="checkbox" name="downformaint"<?php if ($site->downformaint) { echo ' checked="checked"'; } ?> /></div>
						<div class="clear"></div><br /><br /><br />
						<input type="submit" name="submit" value="Confirm Update" />
						<input type="button" name="submit2" value="Cancel (back)" onclick="javascript:history.go(-1);" />&nbsp;&nbsp;
						<input type="hidden" name="id" value="<?php echo $site->id; ?>" />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>
<script type="text/javascript">
	var picker1 = new ColourPicker('colour1', 'pickcolour1', 'demo1', 'backgroundColor');
	var picker2 = new ColourPicker('colour2', 'pickcolour2', 'demo1', 'color');
	var picker3 = new ColourPicker('colour3', 'pickcolour3', 'demo2', 'backgroundColor');
	var picker4 = new ColourPicker('colour4', 'pickcolour4', 'demo2', 'color');
	var picker5 = new ColourPicker('colour5', 'pickcolour5', 'demo3', 'backgroundColor');
	var picker6 = new ColourPicker('colour6', 'pickcolour6', 'demo3', 'color');
	var picker7 = new ColourPicker('colour7', 'pickcolour7', 'demo4', 'backgroundColor');
	var picker8 = new ColourPicker('colour8', 'pickcolour8', 'demo4', 'color');
	var picker9 = new ColourPicker('colour9', 'pickcolour9', 'heading', 'color');
</script>