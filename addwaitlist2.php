<?php
/* ************************************************************************
' admin_addwaitlist2.php -- Add a waiting list entry #2
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
'		betap 2008-06-29 15:40:00
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

$p_params = array();
$p_params['raceid'] = htmlspecialchars($_REQUEST['raceid'], ENT_QUOTES);
$p_formid = htmlspecialchars($_REQUEST['formid'], ENT_QUOTES);
$p_clubtypeid = htmlspecialchars($_REQUEST['clubtypeid'], ENT_QUOTES);
$p_params['online'] = true;
$p_params['racenumber'] = 0;
$p_params['total'] = 0;
$p_params['discount'] = 0;
$p_params['charval'] = 0;
$p_params['languagecode'] = $cookieLanguageCode;
$p_processasp = 'addwaitlist3.php';
$p_params['entrantid'] = '';
$p_params['affil'] = false;
$p_params['memberaffil'] = false;
$p_params['waiting'] = true;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $currentSite->title; ?></title>
		<?php
		$dojoParse = "true";
		include(LIB_PATH. 'dojo.php'); ?>
		<script type="text/javascript" src="scripts/ajax.js"></script>
		<script type="text/javascript" src="scripts/entries.js"></script>
		<script type="text/javascript" src="scripts/general.js"></script>
		<script type="text/javascript">
			dojo.require("dojo.date.locale");
			dojo.require("dojo.currency");
			dojo.require("dojo.parser");
			dojo.require("dijit.form.CheckBox");
			dojo.require("dijit.form.TextBox");
			dojo.require("dijit.form.Textarea");
			dojo.require("dijit.form.CurrencyTextBox");
			dojo.require("dijit.form.DateTextBox");
			dojo.require("dijit.form.NumberTextBox");
			dojo.addOnLoad(function() {
				loadRaces();
				clearTotals();
				toggleRaceAjax(<?php echo $p_params['raceid']; ?>, false, 0, <?php echo $p_formid; ?>, '<?php echo $p_processasp; ?>');
				toggleAffil(false);
				<?php if (!empty($p_params['couponcode'])) { ?>
					globCouponOK=checkCoupon();
				<?php } else { ?>
					globCouponOK=true;
				<?php } ?>
				dijit.byId('firstname').focus();
			});
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

	<body class="tundra">
		<div id="pagecontainer">
			<div id="windowcontainer">
				<div id="innerwrapper">
					<div class="windowcontentcolumn0">
						<?php
						$p_noraceserror = $content->text;
						$content = array_shift($contents);
						$p_genericerror = $content->text;
						$content = array_shift($contents);
						$p_invalidemailerror = $content->text;
						$content = array_shift($contents);
						$p_emailconferror = $content->text;
						$content = array_shift($contents);
						$p_affilcluberror = $content->text;
						$content = array_shift($contents);
						$p_genericselecterror = $content->text;
						$content = array_shift($contents);
						$p_dateformat = $content->text;
						$content = array_shift($contents);
						$p_tooyoungerror = $content->text;
						$content = array_shift($contents);
						echo $content->text;
						$content = array_shift($contents);
						$p_cancel = $content->text;
						$content = array_shift($contents);
						$p_continue = $content->text;
						$content = array_shift($contents);

						include(LIB_PATH. 'inc_publicentryform.php'); ?>
					</div>
				</div>
				<?php
				include(LIB_PATH. "inc_windowfooter.php"); ?>
			</div>
		</div>
	</body>
</html>