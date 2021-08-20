<?php
/* ************************************************************************
' admin_addpage.php -- Offline admin system - Add a page.
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
	if (empty($_POST['url'])) {
		$page = Page::findByURL($_POST['url']);
	}
	if (!$page) {
		$page = new Page();
		$page->name = $_POST['name'];
		$page->url = $_POST['url'];
		$page->levelid = '4';
		$page->sequence = $_POST['sequence'] + 1;
		$page->showonmenu = '1';
		$page->showonfooter = '0';
		$page->subpageof = $_POST['subpageof'];
		$page->alttext = $_POST['alttext'];
		$page->siteid = '0';
		$page->save();
		echo $page->id;
	} else {
		echo 'not added';
	}
}
?>