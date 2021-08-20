<?php
/* ************************************************************************
' passwordchg.php -- Prompt for password change.
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
'		betap 2008-07-01 09:00:00
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

$p_usernameexists = $content->text;
$content = array_shift($contents);
$p_emailuserexists = $content->text;
$content = array_shift($contents);
$p_usernameerror = $content->text;
$content = array_shift($contents);
$p_new1error = $content->text;
$content = array_shift($contents);
$p_new2error = $content->text;
$content = array_shift($contents);
$p_newdifferenterror = $content->text;
$content = array_shift($contents);

if (!empty($_SESSION['entrantid'])) {
	$entrant = Entrant::findById($_SESSION['entrantid']);
} elseif (empty($_REQUEST['id']) && !empty($_REQUEST['entrantid'])) {
	$entrant = Entrant::findById($_REQUEST['entrantid']);
} else {
	$entrant = Entrant::findByPasswordref($_REQUEST['passwordref']);
}

if (!$entrant) { ?>
	<script type="text/javascript">
		window.close();
	</script>
<?php
}
$p_error = '';
if (empty($entrant->username)) {
	// Check that there isn't already a username registered against the email address of the current entrant
	$chkentrant = Entrant::findByEmailWithUsername($entrant->email);
	if ($chkentrant) {
		$p_error = $p_emailuserexists;
	}
}
if (isset($_REQUEST['MM_update']) && $_REQUEST['MM_update'] == 'form1') {
	if ($_REQUEST['username'] != '*NOCHANGE') {
		if (empty($p_error)) {
			// Check the username isn't already in use
			$chkentrant = Entrant::findByUsername($_POST['username']);
			if ($chkentrant) {
				$p_error = $p_usernameexists;
			}
		}
	}

	if (empty($p_error)) {
		// if (username not given, this must be an update for an existing user, so leave the
		// username unchanged (by changing it to itself)
		if ($_REQUEST['username'] != '*NOCHANGE') {
			$entrant->username = htmlspecialchars($_POST['username'], ENT_QUOTES);
		}
		$entrant->password = htmlspecialchars($_POST['new1'], ENT_QUOTES);
		$entrant->passwordref = '';
		$entrant->save();
	
		$_SESSION['entrantid'] = $entrant->id;
		$_SESSION['registered'] = true;
		if (!empty($_REQUEST['passwordref'])) {
			$content2 = Content::findByType('loginprocess');
			if (!empty($content2)) {
				redirect_to($content2->url);
			}
		} else { ?>
			<script type="text/javascript">
				window.close();
			</script>
		<?php
		}
	}
} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $currentSite->title; ?></title>
		<?php
		include(LIB_PATH. 'dojo.php'); ?>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script src="scripts/AC_RunActiveContent.js" type="text/javascript"></script>
		<script type="text/javascript">
			dojo.require("dojo.parser");
			dojo.require("dijit.form.TextBox");
			dojo.require("dijit.form.FilteringSelect");
	
			dojo.addOnLoad(function() {
				<?php
				if (empty($entrant->username)) { ?>
					dijit.byId('username').focus();
				<?php
				} else { ?>
					dijit.byId('new1').focus();
				<?php
				} ?>
			});
			function checkform() {
				missinginfo = "";
				if (document.form1.username.value == '') {
					missinginfo += "<?php echo $p_usernameerror; ?>";
				}
				if (document.form1.new1.value == '') {
					missinginfo += "\n<?php echo $p_new1error; ?>";
				}
				if (document.form1.new2.value == '') {
					missinginfo += "\n<?php echo $p_new2error; ?>";
				}
				if (document.form1.new1.value != document.form1.new2.value) {
					missinginfo += "\n<?php echo $p_newdifferenterror; ?>";
				}
				if (missinginfo != "") {
					alert(missinginfo);
				}	else { 
					document.form1.submit();
				}
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
					<div class="floatleft">
						<?php
						echo $content->text;	// <h2>Add/Update User Details
						$content = array_shift($contents); ?>
					</div>
					<div class="floatright">
						<a href="#" onClick="window.close();" style="color:#000000; text-decoration:none" tabindex="5"><?php echo $content->text; ?>&nbsp;<img src="images/close_icon.gif" width="8" height="8" alt="<?php echo $content->text; ?>" /></a>
					</div>
					<div class="clear"></div>
					<form method="post" name="form1" id="form1" action="<?php echo $_SERVER['PHP_SELF']; ?>">
						<div class="formcaption">
							<?php
							$content = array_shift($contents);
							echo $content->text;	// User Name:
							$content = array_shift($contents); ?>
						</div>
						<div class="formfield">
							<?php if (empty($entrant->username)) { ?>
								<input type="text" name="username" id="username" tabindex="1" value="<?php if (isset($_REQUEST['username'])) echo htmlspecialchars($_REQUEST['username'], ENT_QUOTES); ?>" maxlength="50" style="width: 100px;" dojoType="dijit.form.TextBox" />
								<?php
								if (!empty($p_error)) { ?>
									<br /><div class="stylered"><?php echo $p_error; ?></div>
								<?php
								} ?>
							<?php
							} else { ?>
								<input type="hidden" name="username" value="*NOCHANGE" />
								<strong><?php echo $entrant->username; ?></strong>
							<?php
							} ?>
						</div>
						<div class="clear"></div><br />
						<div class="formcaption">
							<?php
							echo $content->text;	// New Password:
							$content = array_shift($contents); ?>
						</div>
						<div class="formfield">
							<input type="password" name="new1" id="new1" tabindex="2" value="<?php if (isset($_REQUEST['new1'])) echo htmlspecialchars($_REQUEST['new1'], ENT_QUOTES); ?>" maxlength="50" style="width: 100px;" dojoType="dijit.form.TextBox" />
						</div>
						<div class="clear"></div>
						<div class="formcaption">
							<?php
							echo $content->text;	// Re-enter New Password:
							$content = array_shift($contents); ?>
						</div>
						<div class="formfield">
							<input type="password" name="new2" id="new2" tabindex="3" value="<?php if (isset($_REQUEST['new2'])) echo htmlspecialchars($_REQUEST['new2'], ENT_QUOTES); ?>" maxlength="50" style="width: 100px;" dojoType="dijit.form.TextBox" />
						</div>
						<div class="clear"></div><br /><br />
						<input type="hidden" name="MM_update" value="form1" />
						<input type="hidden" name="id" value="<?php echo $entrant->passwordref; ?>" />
						<input type="hidden" name="passwordref" value="<?php echo htmlspecialchars($_REQUEST['passwordref'], ENT_QUOTES); ?>" />
						<input type="hidden" name="entrantid" value="<?php echo htmlspecialchars($_REQUEST['entrantid'], ENT_QUOTES); ?>" />
						<div class="buttonblock">
							<div class="button"><a href="javascript:checkform();" tabindex="4"><?php echo $content->text; ?></a></div>
						</div><br /><br /><br /><br />
					</form>
				</div>
				<?php
				include(LIB_PATH. "inc_windowfooter.php"); ?>
			</div>
		</div>
	</body>
</html>