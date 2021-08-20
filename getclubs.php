<?php
/* ************************************************************************
' admin_getclubs.php -- Administration - Return Matching Clubs via Ajax
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
require_once(LIB_PATH. 'paging.php');
require_once(LIB_PATH. 'entrant.php');

$p_clubtypeid = str_replace("'", "''", htmlspecialchars($_REQUEST['clubtypeid'], ENT_QUOTES));
$p_name = str_replace("'", "''", htmlspecialchars($_REQUEST['key'], ENT_QUOTES));
$p_newpage = str_replace("'", "''", htmlspecialchars($_REQUEST['newpage'], ENT_QUOTES));

$itemcount = Club::countAll($p_clubtypeid, $p_name);
$paging = new Paging($p_newpage, 5, $itemcount);

header('Content-type: text/xml');
echo "<?xml version='1.0' encoding='UTF-8'?>";
echo '<root>';
echo '	<data>';
echo '		<row>';

if (empty($p_name)) {
	echo '		<cell>Key some characters to begin search</cell>';
	echo '		<cell> </cell>';
	echo '		<cell> </cell>';
	echo '	</row>';
	echo '	<row>';
} else {

	$clubs = Club::findAllByPage($p_clubtypeid, $paging, $p_name);

	if ($itemcount == 0) {
		echo '		<cell>No clubs found</cell>';
		echo '		<cell> </cell>';
		echo '		<cell> </cell>';
		echo '	</row>';
	} else {
		echo '		<cell>Club Name</cell>';
		echo '		<cell> </cell>';
		echo '		<cell> </cell>';
		echo '	</row>';
	
		foreach ($clubs as $club) {
			echo '	<row>';
			echo '		<cell>'. $club->id. '</cell>';
			echo '		<cell>'. XMLEncode($club->name). '</cell>';
			echo '		<cell>'. $club->affiliated. '</cell>';
			echo '	</row>';
		}
		
		if (!$paging->isLastPage()) {
			echo '	<row>';
			echo '		<cell>More...</cell>';
			echo '		<cell> </cell>';
			echo '		<cell> </cell>';
			echo '	</row>';
		}
	}
}
echo '	</data>';
echo '</root>'; ?>