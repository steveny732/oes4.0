<?php
/* ************************************************************************
' admin_uploadchanges.php -- Offline admin system - Upload changes
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
'		betap 2008-07-18 13:47:05
'		New script.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'change.php');
require_once(LIB_PATH. 'entry.php');

if ($user = User::authenticate(trim($_POST['username']), trim($_POST['password']))) {
	$session->login($user);
}
if (!$session->isAuthorised()) {
	echo 'Authorisation failure';
	die();
}

$xml = simplexml_load_string(stripslashes($_POST['xml']));

$race = Race::findById($xml->change->newraceid);
foreach ($xml->change as $change) {
	$p_params = array();
	foreach($change as $key=>$value) {
		if ($key == 'type') {
			$mode = $value;
			if ($mode == 'add') {
				$entry = new Entry();
				$p_params['ordernumber'] = date('YmdHis'). createRandomCode(6);
				$p_params['paid'] = '1';
			} else {
				$entry = Entry::findByIdForUpdate($change->entryid);
			}
		}
		if ($mode == 'add') {
			if (substr($key, 0, 3) == 'new') {
				$dbName = substr($key, 3);
				if ($dbName == 'team') {
					if (empty($value)) {
						$p_params['clubid'] = 0;
						$p_params['team'] = '';
					} else {
						$club = Club::findByName($race->formid, ''. $value);
						if ($club) {
							$p_params['clubid'] = $club->id;
						} else {
							$p_params['clubid'] = 0;
							$p_params['team'] = ''. $value;
						}
					}
				} else {
					$p_params[$dbName] = ''. $value;
				}
			} elseif ($key == 'entryid') {
					$oldentryid = $value;
			}
		} else {
			if ($key == 'createentrant') {
				$createentrant = $value;
			} elseif (substr($key, 0, 3) == 'old') {
				$dbName = substr($key, 3);
				$newkey = 'new'. $dbName;
				if (!empty($value) || !empty($change->$newkey)) {
					if ($dbName == 'team') {
						if (empty($value)) {
							$oldclubid = 0;
							$oldteam = '';
						} else {
							$club = Club::findByName($race->formid, ''. $change->$newkey);
							if ($club) {
								$oldclubid = $club->id;
							} else {
								$oldclubid = 0;
								$oldteam = ''. $value;
							}
						}
						if (empty($change->$newkey)) {
							$newclubid = 0;
							$newteam = '';
						} else {
							$club = Club::findByName($race->formid, ''. $change->$newkey);
							if ($club) {
								$newclubid = $club->id;
							} else {
								$newclubid = 0;
								$newteam = ''. $change->$newkey;
							}
						}
						if ($oldclubid != $newclubid && $entry->clubid != $newclubid) {
							$retxml .= '<error><entryid>'. $entry->id. '</entryid><name>'. $entry->firstname. ' '. $entry->surname. '</name><column>clubid</column><oldvalue>'. $oldclubid. '</oldvalue><expectedvalue>'. $entry->clubid. '</expectedvalue></error>';
						}
						if ($oldteam != ''. $newteam && $entry->team != $newteam) {
							$retxml .= '<error><entryid>'. $entry->id. '</entryid><name>'. $entry->firstname. ' '. $entry->surname. '</name><column>team</column><oldvalue>'. $oldteam. '</oldvalue><expectedvalue>'. $entry->team. '</expectedvalue></error>';
							$goterror = true;
						}
					} else {
						$oldvalue = $entry->$dbName;
						if ($oldvalue != "". $value && $oldvalue != "". $change->$newkey) {
							$retxml .= '<error><entryid>'. $entry->id. '</entryid><name>'. $entry->firstname. ' '. $entry->surname. '</name><column>'. $dbName. '</column><oldvalue>'. $value. '</oldvalue><expectedvalue>'. $oldvalue. '</expectedvalue></error>';
							$goterror = true;
						}
					}
					if (!$goterror) {
						if ($dbName == 'team') {
								$p_params['clubid'] = $newclubid;
								$p_params['team'] = $newteam;
						} else {
							$p_params[$dbName] = ''. $change->$newkey;
						}
					}
				}
			}
		}
	}

	if ($mode == 'add' || $createentrant == '1') {
		$entrant = Entrant::findByDetails($p_params['surname'], $p_params['firstname'], $p_params['postcode'], $p_params['dob']);
		if (!$entrant) {
			$entrant = new Entrant();
		}
	} else {
		$entrant = Entrant::findById($entry->entrantid);
	}
	$entrantcols = DatabaseMetadata::findMetadata('entrants');
	foreach ($entrantcols as $column) {
		$entrantcol[$column->Field] = 1;
	}

	$entrycols = DatabaseMetadata::findMetadata('entries');
	foreach ($p_params as $name=>$value) {
		if ($name != 'entryid') {
			$p_data = $value;
			$column = $entrycols[$name];
			if ($column->Type == 'tinyint(1)') {
				$p_data = $p_data ? 1 : 0;
			}
			if ($entry->$name != $p_data || $createentrant == "1") {
				if ($entry->$name != $p_data) {
					if ($mode == 'change') {
						$change = new Change();
						$change->entryid = $entry->id;
						$change->source = 'Offline admin '. $mode;
						$change->column = $name;
						$change->oldvalue = $entry->$name;
						$change->newvalue = $p_data;
						$change->userid = $session->id;
						$change->save();
					}
					$entry->$name = $p_data;
				}
				// Fill in all entrant fields if adding a new entrant
				if (empty($entrant->id) && array_key_exists($name, $entrantcol)) {
					$entrant->$name = $p_data;
				}
			}
		}
	}

	$entrant->save();
	$entry->entrantid = $entrant->id;
	$entry->save();

	if ($mode == 'add') {
		$change = new Change();
		$change->entryid = $entry->id;
		$change->source = 'Offline admin '. $mode;
		$change->userid = $session->id;
		$change->save();

		$retxml .= '<add><entryid>'. $oldentryid. '</entryid><newentryid>'. $entry->id. '</newentryid><ordernumber>'. $entry->ordernumber. '</ordernumber></add>';
	}
}

if (!empty($retxml)) {
	header('Content-type: text/xml');
	echo "<?xml version='1.0' encoding='UTF-8'?>";
	echo '<root>'. $retxml. '</root>';
} ?>