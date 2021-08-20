<?php
	$savedracedate = '';
	// Only get block content for raceentry, news and contact blocks
	if ($content->type == 'raceentry' ||
		$content->type == 'news' ||
		$content->type == 'contact' ||
		$content->type == 'contactemail' ||
		$content->type == 'login' ||
		$content->type == 'loginprocess') {

		$contents1 = Content::findByURL($content->type, $cookieLanguageCode);
		$content1 = array_shift($contents1);
	}
	//if ($content->type == 'contact' ||
	//	$content->type == 'login' ||
	//	$content->type == 'loginprocess') {
	//}

	// Text Block
	if ($content->type == 'text') {
		if (empty($content->languagecode)) {
			echo $content->text;
		} else {
			echo $content->languagetext;
		}

		// Race Entry Block
	} elseif ($content->type == 'raceentry') {

		$gotRace = false;
		$gotOpenRace = false;
		$gotFullRace = false;
		$gotTransfersEnabled = false;
		$gotWaitingListEnabled = false;
		$savedRaceDate = '';

		$racesx = Race::findAllFuture($currentSite->id, $cookieLanguageCode);

		$p_noraces = $content1->text;
		$content1 = array_shift($contents1);
		$p_entriesopen = $content1->text;
		$content1 = array_shift($contents1);
		$p_entriesclosed = $content1->text;
		$content1 = array_shift($contents1);
		$p_placesleft = $content1->text;
		$content1 = array_shift($contents1);
		$p_placesavail = $content1->text;
		$content1 = array_shift($contents1);
		$p_racefull = $content1->text;
		$content1 = array_shift($contents1);
		$p_regonwl = $content1->text;
		$content1 = array_shift($contents1);
		$p_enterbutton = $content1->text;
		$content1 = array_shift($contents1);
		$p_enteralt = $content1->text;
		$content1 = array_shift($contents1);
		$p_transferbutton = $content1->text;
		$content1 = array_shift($contents1);
		$p_transferalt = $content1->text;
		$content1 = array_shift($contents1);
		$p_waitaddbutton = $content1->text;
		$content1 = array_shift($contents1);
		$p_waitaddalt = $content1->text;
		$content1 = array_shift($contents1);
		$p_waitofferbutton = $content1->text;
		$content1 = array_shift($contents1);
		$p_waitofferalt = $content1->text;
		$content1 = array_shift($contents1);
		$p_waitviewbutton = $content1->text;
		$content1 = array_shift($contents1);
		$p_waitviewalt = $content1->text;
		$content1 = array_shift($contents1);
		$p_formbutton = $content1->text;
		$content1 = array_shift($contents1);
		$p_formalt = $content1->text;
		$content1 = array_shift($contents1);
		$p_viewenterbutton = $content1->text;
		$content1 = array_shift($contents1);
		$p_viewenteralt = $content1->text;
		$content1 = array_shift($contents1);
		$p_feedbackbutton = $content1->text;
		$content1 = array_shift($contents1);
		$p_feedbackalt = $content1->text;
		$content1 = array_shift($contents1);
		$p_viewfeedbackbutton = $content1->text;
		$content1 = array_shift($contents1);
		$p_viewfeedbackalt = $content1->text;
		?>
		<table width="100%" border="1" cellpadding="10" cellspacing="0" bordercolor="#000000" bgcolor="#E9E9EB">
			<tr>
				<td align="center">
					<h1><?php echo $currentSite->title; ?></h1>
					<?php
					if (empty($racesx)) {
						echo $p_noraces;
					} else {
						$gotRace = true ?>
						<table width="100%" cellpadding="2" cellspacing="1" style="border:1px solid #000000;">
							<?php
							$rowColour = 'darkgrey';
							foreach ($racesx as $racex) {
								$openrace = false;
								$fullrace = false;
								if ($racex->datenumber() != $savedracedate) {
									$savedracedate = $racex->datenumber();
									echo '<tr class="darkestgreybg"><td align="left" colspan="2"><strong id="race'. $racex->id. '">';
									$incJS .= "var d=new Date(". $racex->year(). ",". ($racex->month() - 1). ",". $racex->day(). ");document.getElementById('race". $racex->id. "').innerHTML=dojo.date.locale.format(d,{selector:'date',formatLength:'long',locale:'". $cookieLanguageCode. "'});";
									echo "</strong></td></tr>";
								}
								echo '<tr class="'. $rowColour. 'bg"><td align="left" valign="top">'. $racex->name. '</td>';
								$noofentries = Entry::countAllPaid($racex->id);
								$noofpreallocations = Preallocation::countAllUnused($racex->id);
								$numberleft = $racex->limit - $noofentries - $noofpreallocations;
								echo '<td align="left">';
								if ($racex->areentriesopen()) {
									$opendate = '<span id="raceopen'. $racex->id. '"></span>';
									echo str_replace('*OPENDATE', $opendate, $p_entriesopen);
									$incJS .= "var d1=new Date(". $racex->openyear(). ",". ($racex->openmonth() - 1). ",". $racex->openday(). ");document.getElementById('raceopen". $racex->id. "').innerHTML=dojo.date.locale.format(d1,{selector:'date',formatLength:'long',locale:'". $cookieLanguageCode. "'});";
								} else {
									if ($racex->areentriesclosed()) {
										// Only show 'entries closed' if the race is in the future (or today)
										if ($racex->isinfuture()) {
											echo $p_entriesclosed;
										}
									} else {
										if (!$racex->allocatenumbers || $numberleft > 0) {
											if ($racex->allocatenumbers || !empty($racex->limit)) {
												echo str_replace('*PLACESLEFT', $numberleft, $p_placesleft);
											} else {
												echo $p_placesavail;
											}
											$openrace = true;
										} else {
											echo $p_racefull;
											if ($racex->waitinglistenabled) {
												echo '<br />'. $p_regonwl;
											}
											$fullrace = true;
										}
									}
								}
								echo '<div class="buttonblock">';
								if ($openrace) {
									echo '<div class="smallbutton"><a href="javascript:openWindow(\'enter1.php?raceid='. $racex->id. '\',\'\',\'scrollbars=yes,resizable=yes,width=790,height=800\');" title="'. $p_enteralt. '">'. $p_enterbutton. '</a></div>';
									if ($racex->entryformurl != "*NONE") {
										echo '<div class="smallbutton"><a href="javascript:openWindow(\''. $racex->entryformurl. '?raceid='. $racex->id. '\',\'\',\'scrollbars=yes,resizable=yes,width=790,height=800\');" title="'. $p_formalt. '">'. $p_formbutton. '</a></div>';
									}
								}
								if ($racex->showentrylist && ($openrace || $fullrace || ($racex->isinfuture() && $racex->areentriesclosed()))) {
									echo '<div class="smallbutton"><a href="javascript:openWindow(\'entrylist.php?clearfilter=1&raceid='. $racex->id. '\',\'\',\'scrollbars=yes,resizable=yes,width=790,height=800\');" title="'. $p_viewenteralt. '">'. $p_viewenterbutton. '</a></div>';
								}
								if ($racex->transfersenabled()) {
									echo '<div class="smallbutton"><a href="javascript:openWindow(\'transfer1.php?raceid='. $racex->id. '\',\'\',\'scrollbars=yes,resizable=yes,width=790,height=800\');" title="'. $p_transferalt. '">'. $p_transferbutton. '</a></div>';
								}
								if ($racex->waitinglistenabled && $fullrace) {
									echo '<div class="clear"></div>';
									echo '<div class="smallbutton"><a href="javascript:openWindow(\'waitlist.php?clearfilter=1&raceid='. $racex->id. '\',\'\',\'scrollbars=yes,resizable=yes,width=790,height=800\');" title="'. $p_waitviewalt. '">'. $p_waitviewbutton. '</a></div>';
									if ($racex->waitinglistadditionsopen && $fullrace) {
										echo '<div class="smallbutton"><a href="javascript:openWindow(\'addwaitlist1.php?raceid='. $racex->id. '\',\'\',\'scrollbars=yes,resizable=yes,width=790,height=800\');" title="'. $p_waitaddalt. '">'. $p_waitaddbutton. '</a></div>';
									}
									if ($racex->waitinglistoffersopen && $fullrace) {
										echo '<div class="smallbutton"><a href="javascript:openWindow(\'offer1.php?raceid='. $racex->id. '\',\'\',\'scrollbars=yes,resizable=yes,width=790,height=800\');" title="'. $p_waitofferalt. '">'. $p_waitofferbutton. '</a></div>';
									}
								}
								if ($racex->feedbackopen) {
									echo '<div class="clear"></div>';
									echo '<div class="smallbutton"><a href="javascript:openWindow(\'comments1.php?raceid='. $racex->id. '\',\'\',\'scrollbars=yes,resizable=yes,width=790,height=800\');" title="'. $p_feedbackalt. '">'. $p_feedbackbutton. '</a></div>';
									echo '<div class="smallbutton"><a href="javascript:openWindow(\'showcomments.php?clearfilter=1&raceid='. $racex->id. '\',\'\',\'scrollbars=yes,resizable=yes,width=790,height=800\');" title="'. $p_viewfeedbackalt. '">'. $p_viewfeedbackbutton. '</a></div>';
								}
								echo '</div>';
								echo '</td></tr>';
								if ($rowColour == 'darkgrey') {
									$rowColour = 'darkergrey';
								} else {
									$rowColour = 'darkgrey';
								}
							} ?>
						</table><br /><br />
					<?php
					} ?>
				</td>
			</tr>
		</table>
		<?php

	// News Block
	} elseif ($content->type == 'news') {

		$newsitems = NewsItem::findLatest(1, $cookieLanguageCode);

		$p_readfull = $content1->text;
		$content1 = array_shift($contents1);
		$p_seearchive = $content1->text; ?>

		<br />
		<img src="<?php echo $currentSite->newsimgurl; ?>" alt="" /><br />
		<?php
		$x = 0;
		foreach ($newsitems as $newsitem) { ?>
			<div class="floatleft"><h3><?php echo $newsitem->headline; ?></h3></div>
			<div class="floatright" id="display_date<?php echo ++$x; ?>"></div>
			<form name="dummyformx<?php echo $x; ?>" action="index.php">
				<input type="hidden" name="news_year" value="<?php echo $newsitem->year(); ?>" />
				<input type="hidden" name="news_month" value="<?php echo $newsitem->month(); ?>" />
				<input type="hidden" name="news_day" value="<?php echo $newsitem->day(); ?>" />
			</form>
			<div class="clear"></div><br />
			<?php
			echo $newsitem->summary; ?>
			<div class="clear"></div>
			<div class="floatright"><a href="#" onclick="openWindow('news_detail.php?id=<?php echo $newsitem->id; ?>','','scrollbars=yes,resizable=yes,width=790,height=800')"><?php echo $p_readfull; ?></a></div>
			<div class="clear"></div>
			<img src="images/black.gif" width="100%" height="1" alt="" />
			<div class="clear"></div>
			<a href="news.php"><?php echo $p_seearchive; ?></a><br /><br />
			<?php
			$incJS .= "var pyear=document.dummyformx". $x. ".news_year.value;var pmonth=document.dummyformx". $x. ".news_month.value-1;var pday=document.dummyformx". $x. ".news_day.value;var d=new Date(pyear,pmonth,pday);MM_setTextOfLayer('display_date". $x. "','',dojo.date.locale.format(d,{selector:'date',formatLength:'long',locale:'". $cookieLanguageCode. "'}));";
		}

	// Contact Block
	} elseif ($content->type == 'contact') {
		$emailerror = $content1->text;
		$content1 = array_shift($contents1);
		$secerror = $content1->text; ?>

		<script type="text/javascript">
			var ajaxCaptchaRequest = false;
			var resOK;
			try {
				// Opera 8.0+, Firefox, Safari
				ajaxCaptchaRequest = new XMLHttpRequest();
			} catch (e) {
				// IE
				try {
					ajaxCaptchaRequest = new ActiveXObject('Msxml2.XMLHTTP');
				} catch (e) {
					try {
						ajaxCaptchaRequest = new ActiveXObject('Microsoft.XMLHTTP');
					} catch (e) {
						alert('Error in Ajax initialisation');
					}
				}
			}

			function verifyCaptcha(code) {
				resOK = false;
				ajaxCaptchaRequest.open('GET', 'formsecurity.php?v=1&code=' + code, false);
				ajaxCaptchaRequest.onreadystatechange = function() {
					if (ajaxCaptchaRequest.readyState == 4) {
						if (ajaxCaptchaRequest.status == 200) {
							var res = ajaxCaptchaRequest.responseText;
							if (res == "OK") {
								resOK = true;
							}
						} else {
							//alert('There was a problem with the request.');
						}
					}
				}
				ajaxCaptchaRequest.send(null); 
			}

			function checkform() {
				missinginfo = "";
				// Check security code
				verifyCaptcha(document.form1.securitycode.value);
				if (!resOK) {
					missinginfo += "\n<?php echo $secerror; ?>";
				}
				if (!((document.form1.email.value.indexOf(".") > 0) && (document.form1.email.value.indexOf("@") > 0))) {
					missinginfo += "\n<?php echo $emailerror; ?>";
				}
				if (missinginfo != "") {
					alert(missinginfo);
				} else {
					document.form1.submit();
				}
			}
		</script>
		<?php
		// Retrieve url of the processing page (a page with a contactemail block)
		$content2 = Content::findByType('contactemail');

		if (!empty($content2)) {

			$content1 = array_shift($contents1);
			echo $content1->text;
			$content1 = array_shift($contents1);
			$p_name = $content1->text;
			$content1 = array_shift($contents1);
			$p_email = $content1->text;
			$content1 = array_shift($contents1);
			$p_enquiry = $content1->text;
			$content1 = array_shift($contents1);
			$p_whofor = $content1->text;
			$content1 = array_shift($contents1);
			$p_nocontacts = $content1->text;
			$content1 = array_shift($contents1);
			$p_submit = $content1->text;
			$content1 = array_shift($contents1);
			$p_seccode = $content1->text;
			$content1 = array_shift($contents1);
			$p_secinstr = $content1->text; ?>

			<form name="form1" method="post" action="<?php echo $content2->url; ?>">
				<div class="subcontainer">
					<div class="formcaption"><?php echo $p_name; ?></div>
					<div class="formfield"><input name="name" type="text" id="name" style="width: 200px;" dojoType="dijit.form.TextBox" propercase="true" /></div>
					<div class="clear"></div>
					<div class="formcaption"><?php echo $p_email; ?></div>
					<div class="formfield"><input name="email" type="text" id="email" style="width: 200px;" dojoType="dijit.form.TextBox" lowercase="true" /></div>
					<div class="clear"></div>
					<div class="formcaption"><?php echo $p_enquiry; ?></div>
					<div class="formfield"><textarea name="enquiry" id="enquiry" dojoType="dijit.form.Textarea" style="width: 300px;"></textarea></div>
					<div class="clear"></div><br />
					<div class="formcaption"><?php echo $p_whofor; ?></div>
					<div class="formfield">
						<select name="contactid" id="contactid" dojoType="dijit.form.FilteringSelect" style="width: 300px;">
							<?php
							$contacts1 = Contact::findAllForOpenRaces($currentSite->id);
							if (empty($contacts1)) { ?>
								<option value="0"><?php echo $p_nocontacts; ?></option>
							<?php
							} else {
								foreach ($contacts1 as $contact1) { ?>
									<option value="<?php echo $contact1->id; ?>"><?php echo $contact1->name. ' '. $contact1->role; ?></option>
								<?php
								}
							} ?>
						</select>
					</div>
					<div class="clear"></div><br />
					<div class="formcaption"><?php echo $p_seccode; ?></div>
					<div class="formfield"><img src="formsecurity.php" title="Security code" width="50" height="24" align="bottom" /></div>
					<div class="clear"></div>
					<div class="buttonblock"><?php echo $p_secinstr; ?>&nbsp;<input type="text" name="securitycode" id="securitycode" style="width: 50px;" dojoType="dijit.form.TextBox" size="4" title="<?php echo $p_secinstr; ?>"></div>
					<div class="clear"></div><br />
					<div class="buttonblock">
						<div class="button"><a href="javascript:checkform();"><?php echo $p_submit; ?></a></div>
					</div>
					<div class="clear"></div><br />
				</div>
			</form>
		<?php
		}

	// Contact Send Email Block
	} elseif ($content->type == 'contactemail') {

		// Don't allow links - but don't tell the spammers that we've ignored their message
		if (!stripos($_REQUEST['enquiry'], '<a') && $_REQUEST['email'] != 'mark357177@hotmail.com') {

			$contact2 = Contact::findById($_REQUEST['contactid']);

			if (!empty($contact2->raceid)) {
				$racex = Race::findById($contact2->raceid);
			}

			$p_body = 'Name: <strong>'. htmlspecialchars($_REQUEST['name'], ENT_QUOTES). '</strong><br />
				Email: <strong>'. htmlspecialchars($_REQUEST['email'], ENT_QUOTES). '</strong><br /><br />
				Enquiry: <strong>'. htmlspecialchars($_REQUEST['enquiry'], ENT_QUOTES). '</strong>';
			if (empty($contact2->raceid)) {
				$ret = sendEMail($currentSite->emailfrom, $contact2->email, '*NONE', 'Enquiry regarding '. $currentSite->title, $p_body);
			} else {
				$ret = sendEMail($racex->emailfrom, $contact2->email, '*NONE', 'Enquiry regarding '. $racex->name, $p_body);
			}
		}

	// Public login block
	} elseif ($content->type == 'login') {

		// Retrieve url of the processing page (a page with a loginprocess block)
		$content2 = Content::findByType('loginprocess');

		if (!empty($content2)) { ?>

			<div class="subcontainer">
				<?php
				$p_usernameerror = $content1->text;
				$content1 = array_shift($contents1);
				$p_passworderror = $content1->text;
				$content1 = array_shift($contents1);
				echo $content1->text;
				$content1 = array_shift($contents1);
				$p_username = $content1->text;
				$content1 = array_shift($contents1);
				$p_password = $content1->text;
				$content1 = array_shift($contents1);
				$p_forgotpassword = $content1->text;
				$content1 = array_shift($contents1);
				$p_rememberme = $content1->text;
				$content1 = array_shift($contents1);
				$p_remprompt = $content1->text;
				$content1 = array_shift($contents1);
				$p_howtoregister = $content1->text;
				$content1 = array_shift($contents1);
				$p_login = $content1->text; ?>

				<form method="post" name="loginform" id="loginform" action="<?php echo $content2->url; ?>">
					<div class="formcaption"><?php echo $p_username; ?></div>
					<div class="formfield">
						<input type="text" name="username" id="username" style="width: 200px;" value="<?php if (isset($_REQUEST['username'])) echo htmlspecialchars($_REQUEST['username'], ENT_QUOTES); ?>" dojotype="dijit.form.ValidationTextBox" required="true" invalidmessage="<?php echo $p_usernameerror; ?>" />
					</div>
					<div class="clear"></div>
					<div class="formcaption"><?php echo $p_password; ?></div>
					<div class="formfield">
						<input type="password" name="password" id="password" style="width: 200px;" dojotype="dijit.form.ValidationTextBox" required="true" invalidmessage="<?php echo $p_passworderror; ?>" />&nbsp;<a href="#" onclick="openWindow('requestpassword1.php', '', 'scrollbars=no, resizable=no, width=790, height=220')"><?php echo $p_forgotpassword; ?></a>

					</div>
					<div class="clear"></div><br />
					<div class="formcaption">&nbsp;</div>
					<div class="formfield">
						<input type="checkbox" name="rememberme" id="rememberme" dojotype="dijit.form.CheckBox" promptmessage="<?php echo $p_remprompt; ?>">&nbsp;<label for="rememberme"><?php echo $p_rememberme; ?></label>
					</div>
					<div class="clear"></div><br />
					<?php echo $p_howtoregister; ?><br />
					<div class="buttonblock">
						<div class="button"><a href="javascript: checkform();"><?php echo $p_login; ?></a></div>
					<div class="clear"></div>
					</div>
				</form>
			</div><br /><br /><br />
			<script type="text/javascript">function checkform(){missinginfo = "";if(dijit.byId('username').value==''){missinginfo+="\n<?php echo $p_usernameerror; ?>";}if(dijit.byId('password').value==''){missinginfo+="\n<?php echo $p_passworderror; ?>";}if(missinginfo!=""){alert(missinginfo);}else{document.loginform.submit();}}
			</script>

		<?php
		}

	// Login process Block
	} elseif ($content->type == 'loginprocess') {

		if (!empty($p_loginerror)) {
			echo $p_loginerror;
		} else {
			if (!empty($_SESSION['entrantid'])) {
	
				$content1 = array_shift($contents1);
				echo $content1->text;
				$content1 = array_shift($contents1);
				$p_name = $content1->text;
				$content1 = array_shift($contents1);
				$p_actions = $content1->text;
	
				$entrants1 = Entrant::findByRegisteredEntrant($_SESSION['entrantid']); ?>
	
				<table width="100%" cellspacing="0" cellpadding="2">
					<tr class="darkgreybg">
						<td width="200" align="left"><strong><?php echo $p_name; ?></strong></td>
						<td width="100" align="center"><strong><?php echo $p_actions; ?></strong></td>
					</tr>
					<?php
					$rowColour = 'white';
					foreach ($entrants1 as $entrant1) { ?>
						<tr class="<?php echo $rowColour; ?>bg">
							<td class="smalltext" align="left"><a href="javascript:openWindow('enter1.php?entrantid=<?php echo $entrant1->id; ?>','','scrollbars=yes,resizable=yes,width=790,height=800');"><?php echo strtoupper($entrant1->surname) ?>, <?php echo $entrant1->firstname; ?></a></td>
							<td class="smalltext" align="center">&nbsp;</td>
						</tr>
						<?php 
						if ($rowColour == 'white') {
							$rowColour = 'grey';
						} else {
							$rowColour = 'white';
						}
					} ?>
				</table><br />
				<?php
				$content1 = array_shift($contents1);
				echo $content1->text;
			}
		}
	} ?>