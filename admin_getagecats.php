<?php
/* ************************************************************************
' admin_getagecats.php -- Return List of Age Categories for offline entry system
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

	$agecats = AgeCategory::findAllForType($_POST['agecategorytypeid']);

	foreach ($agecats as $agecat) {
		echo '	<agecat>';
		echo '		<code>'. XMLEncode($agecat->code). '</code>';
		echo '		<maleonly>'. $agecat->maleonly. '</maleonly>';
		echo '		<femaleonly>'. $agecat->femaleonly. '</femaleonly>';
		echo '		<lowervalue>'. $agecat->lowervalue. '</lowervalue>';
		echo '		<uppervalue>'. $agecat->uppervalue. '</uppervalue>';
		echo '	</agecat>';
	}
	echo '</root>';
}
?>