<?php
// Define special global for returning the allocated race number for display after registration
$globAllocRaceNumber = "";

// Define I18N variables
$cookieLanguageCode = "";
if (isset($_COOKIE['languageCode'])) {
	$cookieLanguageCode = $_COOKIE['languageCode'];
}

// Define entrant (if remembered)
if (empty($_SESSION['entrantid']) && !empty($_COOKIE['entrantid'])) {
	$_SESSION['entrantid'] == $_COOKIE['entrantid'];
	$_SESSION['registered'] == true;
}

$currentSite = Site::findTheSite();
Language::findCurrent($currentSite->languagecode);

spl_autoload_register(function($class) {
	$class = strtolower($class);
    include $class . '.php';
});


function redirect_to($location = NULL) {
	if ($location != NULL) {
		header("Location: {$location}");
		exit;
	}
}

function createRandomCode($length) {

	$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	srand((double)microtime()*1000000);
	$i = 0;
	$code = '';
	for ($i = 0; $i < $length; $i++) {
		$code .= substr($chars, rand() % 35, 1);
	
	}
	return $code;
}

function XMLEncode($str) {
	return str_replace('"', '&quot;', str_replace("'", '&apos;', str_replace('&', '&amp;', str_replace('>', '&gt;', str_replace('<', '&lt;', $str)))));
}

function allocateRaceNumber($seriesid, $seriesracenumbers, $raceid, $allocatenumbers, $clubid, $paid, $p_racenumber) {

	$racenumber = 'NULL';
	if ($paid) {
		if ($allocatenumbers) {
			if (empty($p_racenumber)) {
				// Allocate race number
				$racenumber = RaceNo::getNextFreeNumber($raceid, $seriesid, $seriesracenumbers, $clubid);
				
				if (empty($racenumber)) {
					$racenumber = $p_racenumber;
				}
			} else {
				$racenumber = $p_racenumber;
			}
		} else {
			// If we're not automatically allocating numbers and this is not part of a series, any race
			// number we have has come from the manually-entered drop-down on the admin page
			if (!$seriesracenumbers && !empty($p_racenumber)) {
				$racenumber = $p_racenumber;
			}
		}
	}
	if ($racenumber != 'NULL') {
		if (!empty($_SESSION['racenumber'])) {
			$_SESSION['racenumber'] .= ', '. $racenumber;
		} else {
			$_SESSION['racenumber'] = $racenumber;
		}
	}
	return $racenumber;
}

function sendEMail($from, $to, $bcc, $subject, $body) {
	
	global $currentSite;

	if (strtoupper($from) == '*NONE' || strtoupper($currentSite->smtpserver) == '*NONE') {
		$res = false;
	} else {
		if (strtoupper($to) == '*NONE') {
			$to = $from;
		}
		// Format email HTML body
		$body = '<style type="text/css">p {margin:0; padding:0;}</style><body style="margin: 10px;"><div style="width: 400px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">'. $body. '</div></body>';

		// Wordwrap the body for old servers
		$body = wordwrap($body, 70);
		require_once(LIB_PATH. 'class.phpmailer.php');
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPDebug = 1;
		$mail->SMTPAuth = true;
		if ($currentSite->smtpserver == 'smtp.gmail.com') {
			$mail->SMTPSecure = "tls";
		}
		$mail->Host = $currentSite->smtpserver;
		$mail->Port = 25;
		$mail->Username = $currentSite->smtpusername;
		$mail->Password = $currentSite->smtppassword;
		$mail->SetFrom($from);
		$mail->AddReplyTo($from);
		$mail->Subject = $subject;
		$mail->MsgHTML($body);
		$mail->CharSet = 'UTF-8';
		$mail->AltBody = trim(html_entity_decode(strip_tags($body)));
		$mail->AddAddress($to);
		if (!empty($bcc) && strtoupper($bcc) != "*NONE") {
			$bccs = explode(';', $bcc);
			foreach ($bccs as $abcc) {
				$mail->AddBCC(trim($abcc));
			}
		}
		$res = $mail->Send();
	}

	return $res;
}

function sendEntryEmail($contenttype, $entry, $race, $languagecode) {

	global $currentSite;
	// Get entry email content
	$contents = Content::findByURL($contenttype, $languagecode);
	$content = array_shift($contents);

	$p_from = $race->emailfrom;
	$p_to = $entry->email;
	$p_bcc = "";
	if (strtoupper($race->emailbcc) != '*NONE') {
		$p_bcc = $race->emailbcc;
	//} else {
	//	$p_bcc = $currentSite->entrybcc;
	}
	$p_subject = $content->text;
	$p_subject = str_replace('*RACENAME', $race->name, $p_subject);
	$content = array_shift($contents);
	$p_body = $content->text;
	$p_body = str_replace('*FIRSTNAME', $entry->firstname, $p_body);
	$p_body = str_replace('*RACENAME', $race->name, $p_body);
	$p_body = str_replace('*SURNAME', $entry->surname, $p_body);
	if ($entry->dob == '0000-00-00') {
		$p_body = str_replace('*DOBDATE', 'n/a', $p_body);
	} else {
		$p_body = str_replace('*DOBDATE', date('d M Y', strtotime($entry->dob)), $p_body);
	}
	$content = array_shift($contents);
	if (empty($entry->agecategorycode)) {
		$p_agecategorycode = '';
	} else {
		$p_agecategorycode = $entry->agecategorycode;
	}
	$p_agecatdets = $content->text;
	$p_agecatdets = str_replace('*AGECATEGORY', $p_agecategorycode, $p_agecatdets);
	$p_body = str_replace('*AGECATDETS', $p_agecatdets, $p_body);
	$content = array_shift($contents);
	$p_clubdets = $content->text;
	$content = array_shift($contents);
	$p_unaffiliated = $content->text;
	if ($race->clubsenabled) {
		$p_clubname = $entry->clubname;
		if (empty($entry->clubname)) {
			$p_clubname = '';
		}
		if (empty($p_clubname)) {
			$p_clubname = $p_unaffiliated;
		}
		$p_clubdets = str_replace('*CLUBNAME', $p_clubname, $p_clubdets);
	} else {
		$p_clubdets = '';
	}
	$p_body = str_replace('*CLUBDETS', $p_clubdets, $p_body);
	$content = array_shift($contents);
	$p_teamdets = $content->text;
	if ($race->teamsenabled) {
		$p_team = $entry->team;
		if (empty($entry->team)) {
			$p_team = '';
		}
		$p_teamdets = str_replace('*TEAMNAME', $p_team, $p_teamdets);
	} else {
		$p_teamdets = '';
	}
	$p_body = str_replace('*TEAMDETS', $p_teamdets, $p_body);
	$p_body = str_replace('*ADDRESS1', $entry->address1, $p_body);
	$p_address2 = $entry->address2;
	if (empty($p_address2)) {
		$p_address2 = '';
	}
	$p_body = str_replace('*ADDRESS2', $p_address2, $p_body);
	$p_town = $entry->town;
	if (empty($p_town)) {
		$p_town = '';
	}
	$p_body = str_replace('*TOWN', $p_town, $p_body);
	$p_county = $entry->county;
	if (empty($p_county)) {
		$p_county = '';
	}
	$p_body = str_replace('*COUNTY', $p_county, $p_body);
	$p_postcode = $entry->postcode;
	if (empty($p_postcode)) {
		$p_postcode = '';
	}
	$p_body = str_replace('*POSTCODE', $p_postcode, $p_body);
	$p_email = $entry->email;
	if ($p_email == '*none') {
		$p_email = strtoupper($p_email);
	}
	$p_body = str_replace('*EMAIL', $p_email, $p_body);
	$content = array_shift($contents);
	$p_tshirtdets = $content->text;
	$p_tshirt = $entry->tshirt;
	if ($race->tshirtsenabled) {
		$p_tshirtdets = str_replace("*TSHIRT", $p_tshirt, $p_tshirtdets);
	} else {
		$p_tshirtdets = '';
	}
	$p_body = str_replace('*TSHIRTDETS', $p_tshirtdets, $p_body);
	$content = array_shift($contents);
	$p_option1dets = $content->text;
	$content = array_shift($contents);
	$p_option1true = $content->text;
	$content = array_shift($contents);
	$p_option1false = $content->text;
	$content = array_shift($contents);
	if ($race->option1enabled) {
		$p_option1 = $entry->option1;
		if ($p_option1 == '1') {
			$p_option1 = $p_option1true;
		} else {
			$p_option1 = $p_option1false;
		}
		$p_option1dets = str_replace('*OPTION1', $p_option1, $p_option1dets);
	} else {
		$p_option1dets = '';
	}
	$p_body = str_replace('*OPTION1DETS', $p_option1dets, $p_body);
	$p_option2dets = $content->text;
	$content = array_shift($contents);
	$p_option2true = $content->text;
	$content = array_shift($contents);
	$p_option2false = $content->text;
	$content = array_shift($contents);
	if ($race->option2enabled) {
		$p_option2 = $entry->option2;
		if ($p_option2 == '1') {
			$p_option2 = $p_option2true;
		} else {
			$p_option2 = $p_option2false;
		}
		$p_option2dets = str_replace('*OPTION2', $p_option2, $p_option2dets);
	} else {
		$p_option2dets = '';
	}
	$p_body = str_replace('*OPTION2DETS', $p_option2dets, $p_body);
	$p_body = str_replace('*SITEURL', $currentSite->url, $p_body);
	$p_racenodets = '';
	if (!empty($entry->racenumber)) {
		$p_racenodets = $content->text;
		$p_racenodets = str_replace('*RACENUMBER', $entry->racenumber, $p_racenodets);
	}
	$p_body = str_replace('*RACENODETS', $p_racenodets, $p_body);
	$p_body = str_replace("*TRANSFERCLOSEDATE", $race->displaytransferclosedate(), $p_body);
	$sitextlist = "";
	if (!empty($entry->salesitems)) {
		$content = array_shift($contents);
		$sitextlist = $content->text;
		$content = array_shift($contents);
		// Load up selected sales items
		$salesitems = preg_split(',', $entry->salesitems);
		foreach ($salesitems as $salesitem) {
			$sitext = $content->text;
			$sifields = preg_split(';', $salesitem);
			$si = SalesItem::findByIdTranslated($sifields[0], $languagecode);
			$sitext = str_replace("*SINAME", $si->name, $sitext);
			$sitext = str_replace("*SIQTY", $sifields[2], $sitext);
			$fmt = new NumberFormatter( 'en_US', NumberFormatter::CURRENCY );
			$sitext = str_replace("*SIAMOUNT", $fmt->formatCurrency($sifields[2] * $si->price, 'USD'), $sitext);
			$sitextlist .= $sitext. CRLF;
		}
	}
	$p_body = str_replace("*SI", $sitextlist, $p_body);

	return sendEMail($p_from, $p_to, $p_bcc, $p_subject, $p_body);
}

function capitalize($input) {

	$nextcap = false;
	$output = '';
	
	// Only change if the string is purely upper or lowercase
	if ($input == strtolower($input) || $input == strtoupper($input)) {
		$input = strtolower($input);
		for ($x = 0; $x < strlen($input); $x++) {
			if ($x == 1) {
				$output .= strtoupper(substr($input, $x, 1));
			} else {
				if (substr($input, $x, 1) == ' ' || substr($input, $x, 1) == '-') {
					$nextcap = true;
					$output .= substr($input, $x, 1);
				} else {
					if ($nextcap) {
						$nextcap = false;
						$output .= strtoupper(substr($input, $x, 1));
					} else {
						$output .= substr($input, $x, 1);
					}
				}
			}
		}
	} else {
		$output = $input;
	}
	return $output;
}

function getOrdinal($number) {
	
	if ((($number % 100) / 10) != 1) {
		if (($number % 10) == 1) {
			$ordinal = 'st';
		} elseif (($number % 10) == 2) {
			$ordinal = 'nd';
		} elseif (($number % 10) == 3) {
			$ordinal = 'rd';
		} else
			$ordinal = 'th';
	} else {
		$ordinal = 'th';
	}
	return $ordinal;
}
?>