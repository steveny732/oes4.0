<?php
/* ************************************************************************
' admin_getraces.php -- Return List of Races for offline entry system
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

if (!empty($_POST['username'])) {
	if ($user = User::authenticate(trim($_POST['username']), trim($_POST['password']))) {
		$session->login($user);
	}
}
if ($session->isAuthorised()) {
	header('Content-type: text/xml');
	echo "<?xml version='1.0' encoding='UTF-8'?>";
	echo '<root>';
	if ($_REQUEST['full'] == 1) {
		$races = Race::findAllBySeries($_REQUEST['raceid'], $_REQUEST['formid'], $_REQUEST['processurl']);
	} else {
		$races = Race::findAllExtended();
	}

	$racelist = "";
	$sep = "";
	foreach ($races as $race) {
		echo '	<race>';
		echo '		<id>'. $race->id. '</id>';
		echo '		<name>'. XMLEncode($race->name). '</name>';
		echo '		<date>'. $race->formatdate. '</date>';
		echo '		<distance>'. $race->distance. '</distance>';
		echo '		<limit>'. $race->limit. '</limit>';
		echo '		<teamsenabled>'. $race->teamsenabled. '</teamsenabled>';
		echo '		<clubsenabled>'. $race->clubsenabled. '</clubsenabled>';
		echo '		<cumulsplits>'. $race->cumulsplits. '</cumulsplits>';
		echo '		<chiptimes>'. $race->chiptimes. '</chiptimes>';
		echo '		<timetext>'. XMLEncode($race->timetext). '</timetext>';
		echo '		<chiptimetext>'. XMLEncode($race->chiptimetext). '</chiptimetext>';
		echo '		<chiptime2text>'. XMLEncode($race->chiptime2text). '</chiptime2text>';
		echo '		<chiptime3text>'. XMLEncode($race->chiptime3text). '</chiptime3text>';
		echo '		<chiptime4text>'. XMLEncode($race->chiptime4text). '</chiptime4text>';
		echo '		<chiptime5text>'. XMLEncode($race->chiptime5text). '</chiptime5text>';
		echo '		<chiptime6text>'. XMLEncode($race->chiptime6text). '</chiptime6text>';
		echo '		<agecategorybasis>'. $race->basis. '</agecategorybasis>';
		echo '		<agecategorytypeid>'. $race->agecategorytypeid. '</agecategorytypeid>';
		echo '		<formid>'. $race->formid. '</formid>';
		echo '		<tshirtsenabled>'. $race->tshirtsenabled. '</tshirtsenabled>';
		echo '		<option1enabled>'. $race->option1enabled. '</option1enabled>';
		echo '		<option2enabled>'. $race->option2enabled. '</option2enabled>';
		echo '		<charityfee>'. $race->charityfee. '</charityfee>';
		echo '		<clubtypeid>'. $race->clubtypeid. '</clubtypeid>';
		if ($_REQUEST['full'] == 1) {
			echo '		<raceyear>'. $race->year(). '</raceyear>';
			echo '		<racemonth>'. $race->month(). '</racemonth>';
			echo '		<raceday>'. $race->day(). '</raceday>';
			echo '		<minimumage>'. $race->minimumage. '</minimumage>';
			echo '		<seriesid>'. $race->seriesid. '</seriesid>';
			echo '		<discnoraces>'. $race->discnoraces. '</discnoraces>';
			echo '		<discount>'. $race->discount. '</discount>';
			echo '		<transportenabled>'. $race->transportenabled. '</transportenabled>';
			echo '		<ownchipallowed>'. $race->ownchipallowed. '</ownchipallowed>';
			echo '		<option1caption>'. $race->option1caption. '</option1caption>';
			echo '		<option2caption>'. $race->option2caption. '</option2caption>';
			echo '		<onlinefee>'. $race->raceonlinefee. '</onlinefee>';
			echo '		<chiprentalfee>'. $race->chiprentalfee. '</chiprentalfee>';
			echo '		<fee>'. $race->racefee. '</fee>';
			echo '		<affilfee>'. $race->affilfee. '</affilfee>';
			echo '		<transportfee>'. $race->transportfee. '</transportfee>';
			echo '		<charityname>'. $race->charityname. '</charityname>';
		}
		echo '	</race>';
		$racelist .=  $sep. $race->id;
		if (empty($sep)) {
			$sep = ',';
		}
	}

	if ($_REQUEST['full'] == 1) {
		$racesalesitems = RaceSalesItem::findAllForRaces($racelist, $cookieLanguageCode);
		if (count($racesalesitems) > 0) {
			foreach ($racesalesitems as $racesalesitem) {
				echo '	<salesitem>';
				echo '		<id>'. $racesalesitem->id. '</id>';
				echo '		<name>'. XMLEncode($racesalesitem->name). '</name>';
				echo '		<price>'. $racesalesitem->price. '</price>';
				echo '		<allowquantity>'. $racesalesitem->allowquantity. '</allowquantity>';
				echo '		<raceid>'. $racesalesitem->raceid. '</raceid>';
				echo '	</salesitem>';
			}
		}
	}
	echo '</root>';
}
?>