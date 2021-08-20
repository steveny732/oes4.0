<?php
/* ************************************************************************
' inc_matchwaitlist.php -- Match Waiting List Entries Against Confirmed Offers
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
'		betap 2008-06-29 21:22:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
require_once("includes/initialise.php");
require_once(LIB_PATH. "race.php");

// List all races
$races1 = Race::findAllFutureWithWaitList($currentSite->id, $cookieLanguageCode);

foreach ($races1 as $race1) {

	if (!$race1->transfersclosed()) {
		$waitinglistentries = Entry::findAllOnWaitingList($race1->id);
		$offers = Transfer::findAllOffers($race1->id);

		if ($waitinglistentries && $offers) {
			foreach ($waitinglistentries as $waitinglistentry) {
				foreach ($offers as $offer) {

					// Form an UPDATE statement populating the 'transfer_new' columns of the offer with the
					// entries values of the waiting list entry
					$offer->offertoentryid = $waitinglistentry->id;
					$offer->offereddate = date('c');

					$columns = DatabaseMetadata::findMetadata('transfers');
					foreach ($columns as $column) {
						$colName = $column->Field;
						if (substr($colName, 0, 3) == 'new') {
							$dbName = substr($colName, 3);
							$p_data = $waitinglistentry->$dbName;
							if ($column->Type == 'tinyint(1)') {
								$p_data = $p_data ? 1 : 0;
							}
							$offer->$colName = $p_data;
						}
					}
					$offer->save();

					$waitinglistentry->waiting = 0;
					$waitinglistentry->save();

					// Get place available email content
					$contents2a = Content::findByURL('emailentryavailable', $cookieLanguageCode);
					$content2a = array_shift($contents2a);

					// Send an email
					$p_from = $race1->emailfrom;
					$p_to = $waitinglistentry->email;
					if (strtoupper($race1->emailbcc) != '*NONE') {
						$p_bcc = $race1->emailbcc;
					} else {
						$p_bcc = $currentSite->entrybcc;
					}
					$p_subject = $content2a->text;
					$p_subject = str_replace('*RACENAME', $race1->name, $p_subject);
					$content2a = array_shift($contents2a);
					$p_body = $content2a->text;
					$p_body = str_replace('*FIRSTNAME', $waitinglistentry->firstname, $p_body);
					$p_body = str_replace('*RACENAME', $race1->name, $p_body);
					$p_body = str_replace('*SITEURL', $currentSite->url, $p_body);
					$p_body = str_replace('*ENTRYID', $waitinglistentry->id, $p_body);
					$p_body = str_replace('*TRANSFERID', $offer->id, $p_body);
					$p_body = str_replace('*ORDERNUMBER', $waitinglistentry->ordernumber, $p_body);
					$res = sendEMail($p_from, $p_to, $p_bcc, $p_subject, $p_body);
					break;
				}
			}
		}
	}
}
?>