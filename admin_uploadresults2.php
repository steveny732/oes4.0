<?php
/* ************************************************************************
' admin_uploadresults2.php -- Administration - Upload Results #2
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
require_once(LIB_PATH. 'oldresult.php');
require_once(LIB_PATH. 'result.php');
if (!empty($_POST['username'])) {
	if ($user = User::authenticate(trim($_POST['username']), trim($_POST['password']))) {
		$session->login($user);
	}
	if (!$session->isAuthorised()) {
		echo 'Failure';
		die();
	}
} else {
	if (!$session->isAuthorised()) {
		redirect_to("notauthorised.php");
	}
}

$savedraceid = '';
$recordNo = 0;
$badFile = false;
$raceidCol = '';
$entryidCol = '';
$racenumberCol = '';
$agecategorycodeCol = '';
$forenameCol = '';
$surnameCol = '';
$timeCol = '';
$chiptimeCol = '';
$chiptime2Col = '';
$chiptime3Col = '';
$chiptime4Col = '';
$chiptime5Col = '';
$chiptime6Col = '';
$posCol = '';
$clubnameCol = '';
$teamCol = '';
$agegradeCol = '';
$raceid = '';
$headings = '';

if (!empty($_POST['data'])) {
	$records = explode('|', $_POST['data']);
	if (empty($records)) {
		echo 'Empty';
	} else {
		foreach ($records as $record) {
			processRecord($record);
			if ($badFile) {
				break;
			}
		}
	}
	exit;
}

function processRecord($record) {

	global $savedraceid;
	global $badFile;
	global $recordNo;
	global $raceidCol;
	global $entryidCol;
	global $racenumberCol;
	global $agecategorycodeCol;
	global $forenameCol;
	global $surnameCol;
	global $timeCol;
	global $chiptimeCol;
	global $chiptime2Col;
	global $chiptime3Col;
	global $chiptime4Col;
	global $chiptime5Col;
	global $chiptime6Col;
	global $posCol;
	global $clubnameCol;
	global $teamCol;
	global $agegradeCol;
	global $raceid;
	global $headings;

	$fields = explode(',', $record);
	for ($x = 0; $x < count($fields); $x++) {
		$fields[$x] = trim($fields[$x]);
	}
	$recordNo++;
	// On record 1, determine the field column numbers from the headings
	if ($recordNo == 1) {
		$headings = $fields;
	} elseif ($recordNo == 2) {
		$entryid = '';
		$racenumber = '';
		$agecategorycode = '';
		$forename = '';
		$surname = '';
		$time = '';
		$chiptime = '';
		$clubname = '';
		$team = '';
		$agegrade = '';
		for ($x = 0; $x < count($headings); $x++) {
			switch (strtolower($headings[$x])) {
				case 'race id':
					$raceidCol = $x;
					$raceid = $fields[$raceidCol];
					$race = Race::findById($raceid);
					break;
				case 'entry id':
					$entryidCol = $x;
					break;
				case 'entry no.':
					$racenumberCol = $x;
					break;
				case 'age category':
					$agecategorycodeCol = $x;
					break;
				case 'first name':
					$forenameCol = $x;
					break;
				case 'surname':
					$surnameCol = $x;
					break;
				case strtolower($race->timetext):
					$timeCol = $x;
					break;
				case strtolower($race->chiptimetext):
					$chiptimeCol = $x;
					break;
				case strtolower($race->chiptime2text):
					$chiptime2Col = $x;
					break;
				case strtolower($race->chiptime3text):
					$chiptime3Col = $x;
					break;
				case strtolower($race->chiptime4text):
					$chiptime4Col = $x;
					break;
				case strtolower($race->chiptime5text):
					$chiptime5Col = $x;
					break;
				case strtolower($race->chiptime6text):
					$chiptime6Col = $x;
					break;
				case 'position':
					$posCol = $x;
					break;
				case 'club':
					$clubnameCol = $x;
					break;
				case 'team':
					$teamCol = $x;
					break;
				case 'age grade':
					$agegradeCol = $x;
					break;
			}
		}
		if ($raceidCol === '' || $posCol === '' || $timeCol === '') {
			if (empty($_POST['data'])) {
				echo "Invalid File Format";
			} else {
				echo "Error";
			}
			$badFile = true;
		}
	}

	if ($recordNo > 1) {
		if (!$badFile) {
			if ($entryidCol !== '') {
				$entryid = $fields[$entryidCol];
			}
			if ($racenumberCol !== '') {
				$racenumber = $fields[$racenumberCol];
			}
			if ($agecategorycodeCol !== '') {
				$agecategorycode = $fields[$agecategorycodeCol];
			}
			
			if ($forenameCol !== '') {
				$forename = $fields[$forenameCol];
			}
			if ($surnameCol !== '') {
				$surname = $fields[$surnameCol];
			}
			$time = $fields[$timeCol];
			if ($time === '') {
				$time = 'DNF';
			}
			if ($chiptimeCol !== '') {
				$chiptime = $fields[$chiptimeCol];
				if ($chiptime === '') {
					$chiptime = '';
				}
			}
			if ($chiptime2Col !== '') {
				$chiptime2 = $fields[$chiptime2Col];
				if ($chiptime2 === '') {
					$chiptime2 = '';
				}
			}
			if ($chiptime3Col !== '') {
				$chiptime3 = $fields[$chiptime3Col];
				if ($chiptime3 === '') {
					$chiptime3 = '';
				}
			}
			if ($chiptime4Col !== '') {
				$chiptime4 = $fields[$chiptime4Col];
				if ($chiptime4 === '') {
					$chiptime4 = '';
				}
			}
			if ($chiptime5Col !== '') {
				$chiptime5 = $fields[$chiptime5Col];
				if ($chiptime5 === '') {
					$chiptime5 = '';
				}
			}
			if ($chiptime6Col !== '') {
				$chiptime6 = $fields[$chiptime6Col];
				if ($chiptime6 === '') {
					$chiptime6 = '';
				}
			}
			$pos = $fields[$posCol];
			if ($clubnameCol !== '') {
				$clubname = $fields[$clubnameCol];
			}
			if ($teamCol !== '') {
				$team = $fields[$teamCol];
			}
			if ($agegradeCol !== '') {
				$agegrade = $fields[$agegradeCol];
				if ($agegrade == '#VALUE!') {
					$agegrade = '0.0';
				} else {
					$agegrade = str_replace('%', '', $agegrade);
				}
			}
			if ($raceid != '#N/A' && $raceid != '') {
				if ($raceid !== $savedraceid) {
					if (empty($_POST['data'])) {
						echo 'Created/updated results for race '. $raceid. '. Link is <a href="';
					}
					if ($entryidCol == '') {
						echo 'results.php?raceid=O'. $raceid;
						if (empty($_POST['data'])) {
							echo '">results.php?raceid=O'. $raceid. '</a><br />';
						}
					} else {
						echo 'results.php?raceid=N'. $raceid;
						if (empty($_POST['data'])) {
							echo '">results.php?raceid=N'. $raceid. '</a><br />';
						}
					}
					$savedraceid = $raceid;
					if ($_POST['clear'] == 'on') {
						if ($entryidCol === '') {
							OldResult::clearRace($raceid);
						} else {
							Result::clearRace($raceid);
						}
					}
				}
				// if we've got an entry ID, then we can use the existing entries table for most of the data
				// otherwise, we'll use the oldresults table which is de-normalised
				if ($entryidCol === '') {
					$oldresult = OldResult::findByPosition($raceid, $pos);
					if (!$oldresult) {
						$oldresult = new OldResult();
						$oldresult->raceid = $raceid;
						$oldresult->pos = $pos;
					}
					$oldresult->time = $time;
					$oldresult->chiptime = $chiptime;
					$oldresult->chiptime2 = $chiptime2;
					$oldresult->chiptime3 = $chiptime3;
					$oldresult->chiptime4 = $chiptime4;
					$oldresult->chiptime5 = $chiptime5;
					$oldresult->chiptime6 = $chiptime6;
					$oldresult->name = strtoupper($surname). ', '. capitalize($forename);
					$oldresult->clubname = $clubname;
					$oldresult->agecategorycode = $agecategorycode;
					$oldresult->racenumber = $racenumber;
					$oldresult->team = $team;
					$oldresult->agegrade = $agegrade;
					$oldresult->save();
				} else {
					$result = Result::findByPosition($raceid, $pos);
					if (!$result) {
						$result = new Result();
						$result->raceid = $raceid;
						$result->pos = $pos;
					}
					$result->entryid = $entryid;
					$result->time = $time;
					$result->chiptime = $chiptime;
					$result->chiptime2 = $chiptime2;
					$result->chiptime3 = $chiptime3;
					$result->chiptime4 = $chiptime4;
					$result->chiptime5 = $chiptime5;
					$result->chiptime6 = $chiptime6;
					$result->agegrade = $agegrade;
					$result->save();
				}
			}
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Uploaded Result Status</title>
		<link href="css/styles.css" rel="stylesheet" type="text/css">
		<?php
		include(LIB_PATH. "style_overrides.php"); ?>
	</head>

	<body>
		<div id="pagecontainer">
			<div id="maincontainer">
				<div class="windowcontentcolumn0">
					<h2>Results Upload Status Report</h2>
					<?php
					$faname = $_FILES['filename']['tmp_name'];
					if ($handle = fopen($faname, 'r')) {
						while ($record = fgets($handle)) {
							processRecord($record);
							if ($badFile) {
								break;
							}
						}
						fclose($handle);
					} else {
						echo 'File could not be read. Please try again.';
					} ?>
				</div>
				<?php
				include(LIB_PATH. "inc_footer.php"); ?>
			</div>
		</div>
	</body>
</html>