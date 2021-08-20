<?php
/* ************************************************************************
' admin_getpages.php -- Offline admin system - Return List of public pages
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

	$pages = Page::findAllMenuItems(1, 'en-gb');
	foreach ($pages as $page) {
		$subpages = Page::findAllSubMenuItems($page->id, $cookieLanguageCode);
		echo '	<page>';
		echo '		<id>'. $page->id. '</id>';
		echo '		<name>'. XMLEncode($page->name). '</name>';
		echo '		<sequence>'. XMLEncode($page->sequence). '</sequence>';
		echo '		<subpageof>0</subpageof>';
		echo '	</page>';
		if (!empty($subpages)) {
			foreach ($subpages as $subpage) {
				echo '	<page>';
				echo '		<id>'. $subpage->id. '</id>';
				echo '		<name>'. XMLEncode('&nbsp;&nbsp;'. $subpage->name). '</name>';
				echo '		<sequence>'. XMLEncode($subpage->sequence). '</sequence>';
				echo '		<subpageof>'. XMLEncode($page->id). '</subpageof>';
				echo '	</page>';
			}
		}
	}
	echo '</root>';
}
?>