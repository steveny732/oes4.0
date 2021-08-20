<?php
/* ************************************************************************
' admin_getformfields.php -- Offline admin system - Return List of non-standard form fields
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

	$formfields = FormField::findAllForFormDisplay($_POST['formid'], 'en-gb');
	foreach ($formfields as $formfield) {
		if (!empty($formfield->dbname)) {
			$dbcolumn = DbColumn::findByName($formfield->dbname);
		}
		echo '	<formfield>';
		echo '		<column>'. $formfield->column. '</column>';
		echo '		<type>'. XMLEncode($formfield->type). '</type>';
		echo '		<show>'. $formfield->show. '</show>';
		echo '		<caption>'. XMLEncode($formfield->caption). '</caption>';
		echo '		<memo>'. XMLEncode($formfield->memo). '</memo>';
		echo '		<control>'. XMLEncode($formfield->control). '</control>';
		echo '		<values>'. XMLEncode($formfield->values). '</values>';
		echo '		<width>'. $formfield->width. '</width>';
		echo '		<mandatory>'. $formfield->mandatory. '</mandatory>';
		echo '		<format>'. $formfield->format. '</format>';
		echo '		<minval>'. $formfield->minval. '</minval>';
		echo '		<maxval>'. $formfield->maxval. '</maxval>';
		if (!empty($formfield->dbname)) {
			echo '		<dbname>'. $formfield->dbname. '</dbname>';
			echo '		<dbtext>'. $dbcolumn->description. '</dbtext>';
			echo '		<dbtype>'. $dbcolumn->type. '</dbtype>';
		}
		echo '	</formfield>';
	}
	echo '</root>';
}
?>