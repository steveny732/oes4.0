<?php
/* ************************************************************************
' enter3.php -- Public Entry Process #3 - Add Entry
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
'		betap 2008-07-16 18:32:55
'		Pass discount to add_entry routine and correct online flag on multiple race entries.
'		betap 2008-06-29 21:22:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once('includes/initialise.php');

$p_waitingListEntry = false;
$p_adminEntry = false;
$p_processasp = "";

// Race list is open for selection, so only process the races selected
$p_raceSelected = false;

include(LIB_PATH. 'inc_addentry.php');

$_SESSION['ordernumber'] = htmlspecialchars($_REQUEST['ordernumber'], ENT_QUOTES);
redirect_to('enter4.php?displaydob='. $p_params['displaydob']);
?>