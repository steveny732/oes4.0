<?php
/* ************************************************************************
' admin_download.php -- Administration - Download to Excel
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
require_once('includes/initialise.php');
require_once(LIB_PATH. 'entry.php');

// User information might be provided by the local Excel application so allow
// authentication to occur here
if (!empty($_POST['username'])) {
	if ($user = User::authenticate(trim($_POST['username']), trim($_POST['password']))) {
		$session->login($user);
	}
}
if (!$session->isAuthorised()) {
	redirect_to('notauthorised.php');
}

if (!empty($_REQUEST['seriesid'])) {
	$entries = Entry::findAllForSeries($_REQUEST['seriesid']);
} else {
	$entries = Entry::findAllForRace($_REQUEST['raceid'], $_REQUEST['type']);
}

if (!empty($entries)) {
	$entry = $entries[0];
	$extracols = FormField::findExtraCols($entry->formid);

	header('Content-type: application/vnd.ms-excel');
	header('Content-Disposition: attachment; filename="entries.xml"');
	
	echo '<?xml version="1.0"?>'. CRLF;
	echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"'. CRLF;
	echo 'xmlns:o="urn:schemas-microsoft-com:office:office" ';
	echo 'xmlns:x="urn:schemas-microsoft-com:office:excel" ';
	echo 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'. CRLF;
	echo '<Styles>'. CRLF;
	echo '<Style ss:ID="Default" ss:Name="Normal">'. CRLF;
	echo '<Alignment ss:Vertical="Bottom" />'. CRLF. '<Borders />'. CRLF;
	echo '<Font />'. CRLF. '<Interior />'. CRLF. '<NumberFormat />'. CRLF;
	echo '<Protection />'. CRLF. '</Style>'. CRLF;
	echo '<Style ss:ID="Bold">'. CRLF. '<Font x:Family="Swiss" ss:Bold="1" />'. CRLF. '</Style>'. CRLF;
	echo '<Style ss:ID="BoldCentred">'. CRLF. '<Font x:Family="Swiss" ss:Bold="1" />'. CRLF;
	echo '<Alignment ss:Horizontal="Center" />'. CRLF;
	echo '</Style>'. CRLF;
	echo '<Style ss:ID="StringLiteral">'. CRLF. '<NumberFormat ss:Format="@" />'. CRLF. '</Style>'. CRLF;
	echo '<Style ss:ID="Decimal">'. CRLF. '<NumberFormat ss:Format="0.0000" />'. CRLF. '</Style>'. CRLF;
	echo '<Style ss:ID="Money">'. CRLF. '<NumberFormat ss:Format="#,##0.00" />'. CRLF. '</Style>'. CRLF;
	echo '<Style ss:ID="Integer">'. CRLF. '<NumberFormat ss:Format="0" />'. CRLF. '</Style>'. CRLF;
	echo '<Style ss:ID="Centred">'. CRLF. '<NumberFormat ss:Format="0" />'. CRLF;
	echo '<Alignment ss:Horizontal="Center" />'. CRLF;
	echo '</Style>'. CRLF;
	echo '<Style ss:ID="DateLiteral">'. CRLF. '<NumberFormat ss:Format="Short Date" />'. CRLF. '</Style>'. CRLF;
	echo '</Styles>'. CRLF;
} else {
	echo 'No entries to download';
	die();
}

if ($_REQUEST['type'] == "paid") {

	if (!empty($_REQUEST['seriesid'])) {
		$race = Race::findFirstForSeries($_REQUEST['seriesid']);
		$worksheetname = substr($entry->seriesname, 0, 24). '_paid';
	} else {
		$race = Race::findById($_REQUEST['raceid']);
		$worksheetname = substr($race->name, 0, 24). '_paid';
	}

	if (!empty($entries)) {

		// Output a paid worksheet
		echo '<Worksheet ss:Name="'. $worksheetname. '">'. CRLF;
		echo '<Table>'. CRLF;

		// Output the column widths
		echo '<Column ss:Width="127" />'. CRLF;			// Order Number
		echo '<Column ss:Width="51.75" />'. CRLF;		// Entry No.
		echo '<Column ss:Width="132" />'. CRLF;			// Name
		echo '<Column ss:Width="33" />'. CRLF;			// Age Category
		echo '<Column ss:Width="30" />'. CRLF;			// Paid
		echo '<Column ss:Width="35.25" />'. CRLF;		// Value
		echo '<Column ss:Width="42.75" />'. CRLF;		// Paid By
		echo '<Column ss:Width="37.5" />'. CRLF;		// Tshirt
		echo '<Column ss:Width="52.5" />'. CRLF;		// Affiliated
		echo '<Column ss:Width="39" />'. CRLF;			// Giftaid
		echo '<Column ss:Width="33.75" />'. CRLF;		// Gender
		echo '<Column ss:Width="40.5" />'. CRLF;		// Local
		echo '<Column ss:Width="170.25" />'. CRLF;	// Club/Team
		echo '<Column ss:Width="53.25" />'. CRLF;		// Spec.Club
		echo '<Column ss:Width="27" />'. CRLF;			// Age
		echo '<Column ss:Width="57" />'. CRLF;			// DOB
		echo '<Column ss:Width="67.5" />'. CRLF;		// First Name
		echo '<Column ss:Width="102" />'. CRLF;			// Surname
		echo '<Column ss:Width="40" />'. CRLF;			// Race ID
		echo '<Column ss:Width="45" />'. CRLF;			// Entry ID
		echo '<Column ss:Width="100" />'. CRLF;			// Address 1
		echo '<Column ss:Width="100" />'. CRLF;			// Address 2
		echo '<Column ss:Width="100" />'. CRLF;			// Town
		echo '<Column ss:Width="100" />'. CRLF;			// County
		echo '<Column ss:Width="50" />'. CRLF;			// Postcode
		echo '<Column ss:Width="100" />'. CRLF;			// Tel.(Day)
		echo '<Column ss:Width="100" />'. CRLF;			// Tel.(Eve)
		echo '<Column ss:Width="150" />'. CRLF;			// Email
		if ($race->option1enabled) {
			echo '<Column ss:Width="102" />'. CRLF;		// Option 1
		}
		if ($race->option2enabled) {
			echo '<Column ss:Width="102" />'. CRLF;		// Option 2
		}
		if ($race->clubsenabled) {
			echo '<Column ss:Width="50" />'. CRLF;		// Reg.No.
		}
		echo '<Column ss:Width="150" />'. CRLF;			// Sales Items
		// Append extra columns
		foreach ($extracols as $extracol) {
			echo '<Column ss:Width="100" />'. CRLF;
		}
		echo '<Row>'. CRLF;

		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Order Number</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">Entry No.</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">Name</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Age Category</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">Paid</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Value</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Paid By</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">Tshirt</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">Affiliated</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">Giftaid</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">Gender</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">Local</Data></Cell>'. CRLF;
		if ($race->teamsenabled && $race->clubsenabled) {
			echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Club/Team</Data></Cell>'. CRLF;
		} elseif ($race->teamsenabled) {
			echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Team</Data></Cell>'. CRLF;
		} else {
			echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Club</Data></Cell>'. CRLF;
		}
		echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">Spec.Club</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">Age</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">DOB</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">First Name</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">Surname</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Race ID</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Entry ID</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Address 1</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Address 2</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Town</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">County</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Postcode</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Tel.(Day)</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Tel.(Eve)</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Email</Data></Cell>'. CRLF;
		if ($race->option1enabled) {
			echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">Option 1</Data></Cell>'. CRLF;
		}
		if ($race->option2enabled) {
			echo '<Cell ss:StyleID="BoldCentred"><Data ss:Type="String">Option 2</Data></Cell>'. CRLF;
		}
		if ($race->clubsenabled) {
			echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Reg.No.</Data></Cell>'. CRLF;
		}
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Sales Items</Data></Cell>'. CRLF;
		// Append extra columns
		foreach ($extracols as $extracol) {
			echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">'. XMLEncode($extracol->description). '</Data></Cell>'. CRLF;
		}
		echo '</Row>'. CRLF;

		foreach ($entries as $entry) {
			echo '<Row>'. CRLF;
			echo '<Cell ss:StyleID="Integer"><Data ss:Type="String">'. $entry->ordernumber. '</Data></Cell>'. CRLF;	// Order Number
			echo '<Cell ss:StyleID="Centred"><Data ss:Type="Number">'. $entry->racenumber. '</Data></Cell>'. CRLF;	// Entry No.
			echo '<Cell ss:StyleID="Default" ss:Formula="=CONCATENATE(RC[14], &quot; &quot;, RC[15])"><Data ss:Type="String"></Data></Cell>'. CRLF;	// Name
			echo '<Cell><Data ss:Type="String">'. $entry->agecategorycode. '</Data></Cell>'. CRLF;	// Age Category
			echo '<Cell ss:StyleID="Centred"><Data ss:Type="Number">';
				if ($entry->paid) {
					echo '1';
				} else {
					echo '0';
				}
			echo '</Data></Cell>'. CRLF;	// Paid
			echo '<Cell ss:StyleID="Money"><Data ss:Type="Number">'. $entry->total. '</Data></Cell>'. CRLF;	// Value
			echo '<Cell><Data ss:Type="String">';
				if (empty($_REQUEST['seriesid'])) {
					$payhistory = PayHistory::findByOrderNumber($entry->ordernumber);
					if ($payhistory) {
						echo 'Cheque';
					} else {
						echo $payhistory->provider;
					}
				}
			echo '</Data></Cell>'. CRLF;	// Paid By
			echo '<Cell ss:StyleID="Centred"><Data ss:Type="String">';
				if (empty($_REQUEST['seriesid'])) {
					echo $entry->tshirt;
				}
			echo '</Data></Cell>'. CRLF;	// TShirt
			echo '<Cell ss:StyleID="Centred"><Data ss:Type="Number">';
				if ($entry->clubaffiliated || (!$entry->clubaffiliated && !empty($entry->regno))) {
					echo '1';
				} else {
					echo '0';
				}
			echo '</Data></Cell>'. CRLF;	// Affiliated
			echo '<Cell ss:StyleID="Centred"><Data ss:Type="Number">';
				if ($entry->giftaid) {
					echo '1';
				} else {
					echo '0';
				}
			echo '</Data></Cell>'. CRLF;	// Giftaid
			echo '<Cell ss:StyleID="Centred"><Data ss:Type="String">'. strtoupper(substr($entry->gender, 0, 1)). '</Data></Cell>'. CRLF;	// Gender
			echo '<Cell ss:StyleID="Default" ss:Formula="=IF(ISREF(localcode), IF(localcode=LEFT(RC[13], IF(LEN(localcode)=0,99,LEN(localcode))), &quot;Local&quot;, &quot;&quot;), &quot;&quot;)"><Data ss:Type="String"></Data></Cell>'. CRLF;	// Local
			if ($race->clubsenabled && $race->teamsenabled) {
				if (!empty($entry->team)) {
					$teamname = $entry->team;
				} else {
					$teamname = $entry->clubname;
				}
				echo '<Cell><Data ss:Type="String">'. XMLEncode($teamname). '</Data></Cell>'. CRLF;	// Club/Team
			} elseif ($race->teamsenabled) {
				echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->team). '</Data></Cell>'. CRLF;	// Team
			} else {
				if (empty($entry->clubname)) {
					echo '<Cell><Data ss:Type="String">Unattached</Data></Cell>'. CRLF;
				} else {
					echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->clubname). '</Data></Cell>'. CRLF;	// Club
				}
			}
			echo '<Cell ss:StyleID="Centred"><Data ss:Type="Number">';
			echo $entry->frostbite ? '1' : '0';
			echo '</Data></Cell>';	// Spec.Club
			$p_dob = $entry->dob. 'T00:00:00.000';
			//$p_dob = substr($p_dob, 8, 2). substr($p_dob, 4, 4). substr($p_dob, 0, 4);

			echo '<Cell ss:StyleID="Centred" ss:Formula="=IF(RC[1]&lt;&gt;&quot;&quot;, DATEDIF(CONCATENATE(YEAR(RC[1]), &quot;/&quot;, MONTH(RC[1]), &quot;/&quot;, DAY(RC[1])), &quot;'. $race->year(). '/'. $race->month(). '/'. $race->day(). '&quot;, &quot;Y&quot;), &quot;&quot;)"><Data ss:Type="Number"></Data></Cell>'. CRLF;	// Age
			echo '<Cell ss:StyleID="DateLiteral"><Data ss:Type="DateTime">'. $p_dob. '</Data></Cell>'. CRLF;	// DOB
			echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->firstname). '</Data></Cell>'. CRLF;	// First Name
			echo '<Cell><Data ss:Type="String">'. XMLEncode(strtoupper($entry->surname)). '</Data></Cell>'. CRLF;	// Surname
			echo '<Cell ss:StyleID="Centred"><Data ss:Type="Number">';
				if (empty($_REQUEST['seriesid'])) {
					echo $entry->raceid;
				}
			echo '</Data></Cell>'. CRLF;	// Race ID
			echo '<Cell ss:StyleID="Centred"><Data ss:Type="Number">';
				if (empty($_REQUEST['seriesid'])) {
					echo $entry->id;
				}
			echo '</Data></Cell>'. CRLF;	// Entry ID
			echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->address1). '</Data></Cell>'. CRLF;	// Address 1
			echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->address2). '</Data></Cell>'. CRLF;	// Address 2
			echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->town). '</Data></Cell>'. CRLF;	// Town
			echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->county). '</Data></Cell>'. CRLF;	// County
			echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->postcode). '</Data></Cell>'. CRLF;	// Postcode
			echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->telday). '</Data></Cell>'. CRLF;	// Tel.(Day)
			echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->televe). '</Data></Cell>'. CRLF;	// Tel.(Eve)
			echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->email). '</Data></Cell>'. CRLF;	// Email
			if ($race->option1enabled) {
				echo '<Cell ss:StyleID="Centred"><Data ss:Type="Number">';
				echo $entry->option1 ? '1' : '0';
				echo '</Data></Cell>'. CRLF;	// Option 1
			}
			if ($race->option2enabled) {
				echo '<Cell ss:StyleID="Centred"><Data ss:Type="Number">';
				echo $entry->option2 ? '1' : '0';
				echo '</Data></Cell>'. CRLF;	// Option 2
			}
			if ($race->clubsenabled) {
				echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->regno). '</Data></Cell>'. CRLF;	// Reg.No.
			}
			if (empty($entry->salesitems)) {
				echo '<Cell><Data ss:Type="String"></Data></Cell>'. CRLF;	// Sales Items
			} else {
				$salesitems = preg_split(',', $entry->salesitems);
				$sitext = "";
				foreach ($salesitems as $salesitem) {
					$sifields = preg_split(';', $salesitem);
					$si = SalesItem::findByIdTranslated($sifields[0], $languagecode);
					if (!empty($sitext)) {
						$sitext .= ', ';
					}
					$sitext .= $si->name. ' x '. $sifields[2];
				}
				echo '<Cell><Data ss:Type="String">'. XMLEncode($sitext). '</Data></Cell>'. CRLF;	// Sales Items
			}
			// Append extra columns
			foreach ($extracols as $extracol) {
				$colname = $extracol->dbname;
				if ($extracol->type == 3) {						// Number
					$prefix = '<Cell ss:StyleID="Bold"><Data ss:Type="String">';
					$fieldcontents = $entry->$colname;
				} elseif ($extracol->type == 6) {				// Money
					$prefix = '<Cell ss:StyleID="Money"><Data ss:Type="Number">';
					$fieldcontents = $entry->$colname;
				} elseif ($extracol->type == 7) {				// Date
					$prefix = '<Cell><Data ss:Type="String">';
					$fieldcontents = XMLEncode($entry->$colname);
					if (strpos($fieldcontents, '-') != 0) {
						$fieldcontents = str_replace('-', '/', $fieldcontents);
						$fieldcontents = substr($fieldcontents, 8, 8). substr($fieldcontents, 4, 4). substr($fieldcontents, 0, 4);
					}
				} elseif ($extracol->type == 11) {		// Boolean
					$prefix = '<Cell ss:StyleID="Centred"><Data ss:Type="Number">';
					$fieldcontents = $entry->$colname ? '1' : '0';
				} else {
					$prefix = '<Cell><Data ss:Type="String">';
					$fieldcontents = XMLEncode($entry->$colname);
				}
				echo $prefix. $fieldcontents. '</Data></Cell>'. CRLF;
			}
			echo '</Row>'. CRLF;
		}
	}

} else {

	$race = Race::findById($_REQUEST['raceid']);
	$worksheetname = substr($race->name, 0, 22). '_unpaid';

	if (!empty($entries)) {

		// Output the unpaid worksheet
		echo '<Worksheet ss:Name="'. $worksheetname. '">'. CRLF;
		echo '<Table>'. CRLF;

		// Output the column widths
		echo '<Column ss:Width="230" />'. CRLF;	// Name
		echo '<Column ss:Width="44" />'. CRLF;	// Cat.
		echo '<Column ss:Width="219" />'. CRLF;	// Club
		echo '<Column ss:Width="100" />'. CRLF;	// Email
		echo '<Column ss:Width="50" />'. CRLF;	// Tel.(Day)
		echo '<Column ss:Width="50" />'. CRLF;	// Tel.(Eve)
		echo '<Column ss:Width="127" />'. CRLF;	// Order Number

		echo '<Row>'. CRLF;

		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Name</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Cat.</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">';
		echo $race->teamsenabled ? 'Team' : 'Club';
		echo '</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Email</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Tel.(Day)</Data></Cell>'. CRLF;;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Tel.(Eve)</Data></Cell>'. CRLF;
		echo '<Cell ss:StyleID="Bold"><Data ss:Type="String">Order Number</Data></Cell>'. CRLF;
		echo '</Row>'. CRLF;

		foreach ($entries as $entry) {
			echo '<Row>'. CRLF;
			echo '<Cell><Data ss:Type="String">'. XMLEncode(strtoupper($entry->surname). ', '. $entry->firstname). '</Data></Cell>'. CRLF;	// Name
			echo '<Cell><Data ss:Type="String">'. $entry->agecategorycode. '</Data></Cell>'. CRLF;	// Cat.
			if ($race->clubsenabled && $race->teamsenabled) {
				if (!empty($entry->team)) {
					$teamname = $entry->team;
				} else {
					$teamname = $entry->clubname;
				}
				echo '<Cell><Data ss:Type="String">'. $teamname. '<Cell><Data ss:Type="String">'. XMLEncode($teamname). '</Data></Cell>'. CRLF;	// Club/Team
			} elseif ($race->teamsenabled) {
				echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->team). '</Data></Cell>'. CRLF;	// Team
			} else {
				if (empty($entry->clubname)) {
					echo '<Cell><Data ss:Type="String">Unattached</Data></Cell>'. CRLF;
				} else {
					echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->clubname). '</Data></Cell>'. CRLF;	// Club
				}
			}
			echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->email). '</Data></Cell>'. CRLF;	// Email
			echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->telday). '</Data></Cell>'. CRLF;	// Tel.(Day)
			echo '<Cell><Data ss:Type="String">'. XMLEncode($entry->televe). '</Data></Cell>'. CRLF;	// Tel.(Eve)
			echo '<Cell ss:StyleID="Integer"><Data ss:Type="String">'. $entry->ordernumber. '</Data></Cell>'. CRLF;	// Order Number
			echo '</Row>'. CRLF;
		}
	}
}

if (!empty($entries)) {
	// Complete Worksheet
	echo '</Table>'. CRLF;
	echo '<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">'. CRLF;
	echo '<Zoom>80</Zoom>'. CRLF;
	echo '<Selected/>'. CRLF;
	echo '<ProtectObjects>False</ProtectObjects>'. CRLF;
	echo '<ProtectScenarios>False</ProtectScenarios>'. CRLF;
	echo '</WorksheetOptions>'. CRLF;
	echo '</Worksheet>'. CRLF;

	// Finally, complete the Workbook
	echo '</Workbook>'. CRLF;
}
?>