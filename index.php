<?php
/* ************************************************************************
' index.php -- Main Public Entry Point
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
require_once(LIB_PATH. "content.php");

if (!defined('DB_SERVER') || $currentSite->downformaint && !$session->isLoggedIn()) {
	redirect_to("downformaint.php");
}
if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php?notimeout=1");
}

$url = $_SERVER['SCRIPT_NAME'];
$url = substr($url, strrpos($url, "/") + 1, strlen($url) - strrpos($url, "/"));

if (!empty($_REQUEST["id"])) {
	$url .= "?id=". htmlspecialchars($_REQUEST["id"], ENT_QUOTES);
} else {
	if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'pl') {
		$url = $_SERVER['REQUEST_URI'];
		$url = substr($url, strrpos($url, "/") + 1, strlen($url) - strrpos($url, "/"));
	} else {
		if (!empty($currentSite->homepageid)) {
			$url .= "?id=". $currentSite->homepageid;
		}
	}
}

$contents = Content::findByURL($url, $cookieLanguageCode);
// If the login page was requested are a register user is already logged on, redirect to
// the logged-in page (we have to do it here before headers are output)
if ($contents[0]->type == 'login') {
	if (!empty($_SESSION['entrantid'])) {
		$content1 = Content::findByType('loginprocess');
		// If already logged-in, retrieve url of the logged-in page and redirect
		if (!empty($content1)) {
			redirect_to($content1->url);
		}
	}
// If we're logging out of the registered user system, re-direct to
// the login page (we have to do it here before headers are output)
} elseif ($contents[0]->type == 'loginprocess') {
	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'logout') {
		$_SESSION['entrantid'] = '';
		$_SESSION['registered'] = '';
		setcookie('entrantid', '');

		// Retrieve url of the login page and redirect
		$content1 = Content::findByType('login');
		if (!empty($content1)) {
			redirect_to($content1->url);
		}
	} else {
		$p_loginerror = '';
		$contents1 = Content::findByURL('loginprocess', $cookieLanguageCode);
		$content1 = array_shift($contents1);
		$loginerror = $content1->text;
		$content1 = array_shift($contents1);

		if (empty($_SESSION['entrantid'])) {
			if (!$entrant1 = Entrant::authenticate($_REQUEST['username'], $_REQUEST['password'])) {

				// Retrieve url of the login page (a page with a login block)
				$content2 = Content::findByType('login');
				if (!empty($content2)) {
					$p_loginerror = str_replace('*LOGINURL', $content2->url, $loginerror);
				}
			} else {
				$_SESSION['entrantid'] = $entrant1->id;
				$_SESSION['registered'] = true;
			}
		}
		if (!empty($_SESSION['entrantid'])) {
			// Store a cookie if 'Remember Me' was checked
			if (isset($_REQUEST['rememberme']) && $_REQUEST['rememberme'] == 'on') {
				setcookie('entrantid', $_SESSION['entrantid']);
			}
		}
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $currentSite->title; ?></title>
		<?php $dojoParse = "false";
		include(LIB_PATH. "dojo.php"); ?>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script src="scripts/AC_RunActiveContent.js" type="text/javascript"></script>
		<script src="SpryAssets/SpryMenuBar.js" type="text/javascript"></script>
		<script type="text/javascript">
			dojo.require("dojo.parser");
			dojo.require("dojo.date.locale");
			dojo.require("dojo.currency");
			dojo.require("dijit.form.TextBox");
			dojo.require("dijit.form.CheckBox");
			dojo.require("dijit.form.Textarea");
			dojo.require("dijit.form.FilteringSelect");

			function initMenu() {
				var myMenuBar = new Spry.Widget.MenuBar('myMenuBar', {imgDown:'SpryAssets/SpryMenuBarDownHover.gif', imgRight:'SpryAssets/SpryMenuBarRightHover.gif'});
			}
			dojo.addOnLoad(function() {
				loadStuff();
				loadBasketValues();
				dojo.parser.parse();
				try {
					dijit.byId('username').focus();
				} catch(e) {
				}
			});
		</script>
		<link href="css/styles.css" rel="stylesheet" type="text/css" />
		<link href="SpryAssets/SpryMenuBarVertical.css" rel="stylesheet" type="text/css" />
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
		<!--[if IE 6]>
		<style>
		#innerwrapper { zoom: 1; }
		</style>
		<![endif]-->
	</head>

	<body class="tundra" onload="initMenu();">
		<div id="pagecontainer">
			<div id="maincontainer">
				<div id="innerwrapper">
					<div id="banner">
						<a name="topofpage" id="topofpage"></a>
						<p class="accessibility">[<a href="#maincontent" accesskey="S">Skip to main content</a>]</p>
						<img src="<?php echo $currentSite->bannerimgurl; ?>" />
					</div>
					<?php
					include(LIB_PATH. "inc_language.php"); ?>
					<div id="menucolumn">
						<img src="<?php echo $currentSite->clubimgurl; ?>" /><br />
						<?php
						include(LIB_PATH. "inc_menu.php"); ?>
					</div>
					<div id="contentcolumn">
					<a name="maincontent" id="maincontent"></a>
						<?php
						$incJS = '<script type="text/javascript">function loadStuff(){';
						foreach ($contents as $content) {
							// Show normal content
							include(LIB_PATH. "inc_content.php");
						}
						$incJS .= "}</script>";
						echo $incJS;
						echo "<br /><br />"; ?>
					</div>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>