<?php
/* ************************************************************************
' admin_genracenos.php -- Administration - Update an entry #2
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
'		betap 2008-06-29 16:02:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once("includes/initialise.php");
require_once(LIB_PATH. 'raceno.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

for ($x = $_REQUEST['start']; $x < $_REQUEST['start'] + $_REQUEST['limit']; $x++) {

	$chkraceno = RaceNo::findByNumber($_REQUEST['raceid'], $x);
	if (empty($chkraceno)) {
		$raceno = new RaceNo();
		$raceno->raceid = $_REQUEST['raceid'];
		$raceno->racenumber = $x;
		$raceno->save();
	}
}

redirect_to("admin_racelist.php")
?>