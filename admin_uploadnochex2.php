<?php
/* ************************************************************************
' admin_uploadnochex2.php -- Administration - Upload Nochex Statement #2
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
' Version: 1.0
' Last Modified By:
'  betap 2008-07-16 09:37:41
'		New script.
'
' ************************************************************************ */
require_once('includes/initialise.php');
require_once(LIB_PATH. 'payhistory.php');

if (!$session->isAuthorised()) {
	redirect_to("notauthorised.php");
}

$faname = $_FILES['filename']['tmp_name'];
if ($handle = fopen($faname, 'r')) {
	$badFile = false;
	while ($record = fgets($handle)) {
		$fields = explode(',', $record);
		for ($x = 0; $x < count($fields); $x++) {
			$fields[$x] = trim($fields[$x]);
		}
		$recordNo++;
		if ($recordNo == 1) {
			if ($fields[0] != 'Date' || $fields[1] != 'TransType') {
				echo "Invalid File Format";
				$badFile = true;
			}
		} else {
			if (!$badFile) {
				$date = $fields[0];
				$date = substr($date, 6, 4). substr($date, 2, 4). substr($date, 0, 2). substr($date, 10);
				$type = $fields[1];
				$email = $fields[2];
				$desc = $fields[3];
				$out = $fields[4];
				$in = $fields[5];
				$charge = $fields[6];
				$bal = $fields[7];
				$order = $fields[8];
				$order = substr($order, 0, 50);

				$payhistory = PayHistory:: findByDate($date);
				if (!$payhistory) {
					$payhistory = new PayHistory();
					$payhistory->provider = 'NOCHEX';
					$payhistory->date = $date;
					$payhistory->type = $type;
					$payhistory->email = $email;
					$payhistory->desc = $desc;
					$payhistory->out = $out;
					$payhistory->in = $in;
					$payhistory->charge = $charge;
					$payhistory->bal = $bal;
					$payhistory->ordernumber = $order;
					$payhistory->save();
				}
			}
		}
	}
}
fclose($handle);
if (!badFile) {
	redirect_to('admin_summary.php');
}
?>