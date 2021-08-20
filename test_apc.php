<?php
/* ************************************************************************
' test_apc.php - Test Version - Payment application callback
'
'	Version: 1.0
'	Last Modified By:
'		betap 2008-07-01 09:02:00
'		General preparation for release.
'
' ************************************************************************ */
require_once("includes/initialise.php");
require_once(LIB_PATH. "entry.php");

$PaymentResponse = 'AUTHORISED';

if (!empty($_REQUEST['order_id'])) {						// Nochex form field containing order number
	$p_ordernumber = $_REQUEST['order_id'];
}
if (!empty($_REQUEST['item_number'])) {
	$p_ordernumber = $_REQUEST['item_number'];		// Paypal form field containing order number
}

$entries = Entry::findByOrderNumberNotPaid($p_ordernumber);

$saved_seriesid = 0;
$saved_seriesracenumbers = false;
$saved_raceid = 0;
$saved_surname = '';
$saved_firstname = '';
$saved_dob = '';
$saved_postcode = '';

foreach ($entries as $entry) {
	$p_needNewNumber = false;
	$p_clubid = $entry->clubid;

	// if this is an entry for a race not in a series or within a series without series-wide race numbers, or
	// part of a different series or for a different person to the previous entry processed, we can allocate a new race number
	if (empty($entry->seriesid) ||
		!$entry->seriesracenumbers ||
		$entry->seriesid != $saved_seriesid ||
		$entry->surname != $saved_surname ||
		$entry->firstname != $saved_firstname ||
		$entry->dob != $saved_dob ||
		$entry->postcode != $saved_postcode) {
		if (empty($entry->seriesid)) {
			$saved_seriesid = 0;
		} else {
			$saved_seriesid = $entry->seriesid;
		}
		$saved_seriesracenumbers = $entry->seriesracenumbers;
		$saved_surname = $entry->surname;
		$saved_firstname = $entry->firstname;
		$saved_dob = $entry->dob;
		$saved_postcode = $entry->postcode;
		$p_needNewNumber = true;
	}
	// if this is a different race to the previous entry processed, we'll use the previous
	// race number (in the case of a series entry for the same entrant) or we'll assume that
	// the first race number passed from the allocation routine will be OK
	if ($entry->raceid != $saved_raceid) {
		$saved_raceid = $entry->raceid;
		if (empty($entry->seriesid)) {
			$saved_racenumber = 0;
			$p_racenumber = 0;
		}
		$race = Race::findById($saved_raceid);
	}
	// See if this is a waiting list confirmation
	$waitlistentry = Transfer::findWaitingListEntry($p_ordernumber);

	// Complete any waiting list transfer etc. (we're going to re-use the existing racenumber
	// so we don't need to allocate a new one)
	if ($waitlistentry) {
		$p_racenumber = $waitlistentry->racenumber;
		$p_originalentryid = $waitlistentry->entryid;
		
		$oldentry = Entry::findByIdForUpdate($p_originalentryid);
		$oldentry->racenumber = 0;
		$oldentry->paid = 0;
		$oldentry->deleted = 1;
		$oldentry->save();

		$transfer = Transfer::findByIdForUpdate($waitlistentry->id);
		$transfer->complete = 1;
		$transfer->offertoentryid = 0;
		$transfer->offereddate = 0;
		$transfer->save();
	} else {
		
		// Allocate race number
		if ($p_needNewNumber) {
			$p_racenumber = allocateRaceNumber($saved_seriesid, $saved_seriesracenumbers, $saved_raceid, $race->allocatenumbers, $p_clubid, true, '0');
		}
		// if we're not allocating race numbers for this race, don't bother with the looping next time through
		if ($p_racenumber != 'NULL') {
			$saved_racenumber = $p_racenumber;
		}
	}

	// Mark as paid and allocate a race number (any number not already allocated or pre-allocated)
	$entryupdate = Entry::findByIdForUpdate($entry->id);
	$change = new Change();
	$change->entryid = $entryupdate->id;
	$change->source = 'TEST payment auth';
	$change->column = 'racenumber';
	$change->oldvalue = $entryupdate->racenumber;
	$change->newvalue = $p_racenumber;
	$change->userid = 'TEST';
	$change->save();
	$entryupdate->racenumber = $p_racenumber;
	$change = new Change();
	$change->entryid = $entryupdate->id;
	$change->source = 'TEST payment auth';
	$change->column = 'paid';
	$change->oldvalue = $entryupdate->paid;
	$change->newvalue = '1';
	$change->userid = 'TEST';
	$change->save();
	$entryupdate->paid = 1;
	$entryupdate->save();

	// This is only so the email routine 'sees' the race number
	$entry->racenumber = $p_racenumber;

	$res = sendEntryEmail('emailentry', $entry, $race, $entry->languagecode);
}

redirect_to('payment_confirmed.php?raceid='. $saved_raceid); ?>