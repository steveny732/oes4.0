<?php
/* ************************************************************************
' inc_adminentryform.php -- Administration version of entry form
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
'		betap 2008-07-01 09:14:00
'		Apply GPL and general preparation for release.
'
' ************************************************************************ */
$currentColumn = '*NONE';
$currentSection = 0;
$tabCount = 0;
$currentControl = '';
$lastControl = '';
$lastType = '';
$openformsectiondiv = false;
$openformsectioncontentdiv = false;
$openformfielddiv = false;
$checkMeJS = 'function checkform(){missinginfo="";raceselected=false;for(i=0;i<document.form1.elements.length;i++){if(document.form1.elements[i].type==\'checkbox\'){fieldid=document.form1.elements[i].id;if(fieldid.substring(0,7)==\'raceid_\'){if(document.form1.elements[i].checked){raceselected=true;break;}}}}if(!raceselected){missinginfo+="\nNo races selected";}';
$functionNames = '';
$functionsJS = '';
?>
<form action="<?php echo $p_processasp; ?>" method="post" name="form1">
	<input type="hidden" name="ordernumber" id="ordernumber" value="<?php if (!empty($p_params['ordernumber'])) { echo $p_params['ordernumber']; } else { echo date('YmdHis'). createRandomCode(6); } ?>" />
	<input type="hidden" name="id" id="id" value="<?php echo $p_params['id']; ?>" />
	<input type="hidden" name="currency" id="currency" value="<?php echo $currentSite->currencycode; ?>" />
	<input type="hidden" name="memberaffil" id="memberaffil" value="<?php echo $p_params['memberaffil']; ?>" />
	<input type="hidden" name="formid" id="formid" value="<?php echo $p_formid; ?>" />
	<input type="hidden" name="clubtypeid" id="clubtypeid" value="<?php echo $p_clubtypeid; ?>" />
	<input type="hidden" name="raceid" value="<?php echo $p_raceid; ?>" />
	<?php
	$formfields = FormField::findAllForFormDisplay($p_formid, $cookieLanguageCode);

	foreach ($formfields as $formfield) {
		$fieldName = $formfield->dbname;
		$fieldType = $formfield->type;
		$fieldControl = $formfield->control;
		if ($fieldType == 'section') {
			$fieldColumn = '';
		} else {
			$fieldColumn = $formfield->column;
		}
		$fieldCaption = $formfield->caption;
		$fieldWidth = $formfield->width;
		$fieldFormat = $formfield->format;
		$fieldShow = $formfield->show;
		$fieldLength = $formfield->length;
		if ($fieldType == 'races') {
			$fieldColumn = '0';
		}

		// Close any open formfield divs
		if ($fieldControl != '*NOCLEAR' && $openformfielddiv) {
			echo '</div>';
			echo '<div class="clear"></div>';
			$openformfielddiv = false;
		}
		// Close any previous hide control divs
		if (!empty($currentControl) && $currentControl != '*NOCLEAR' && $currentControl != '*ONADMIN' &&
			(empty($fieldControl) ||
			 !empty($fieldControl) && $fieldControl != $currentControl) &&
			($currentControl != '*ONCLUB' ||
			 $fieldControl != '*ONTRANSFERAFFIL' &&
			 $fieldControl != '*ONCHANGEAFFIL' &&
			 $fieldControl != '*ONTRANSFERNONAFFIL' &&
			 $fieldControl != '*ONCHANGENONAFFIL') &&
			$p_OK) {
			echo '</div>'. CRLF;

			// *ONCLUBNOTAFFIL is nested within *ONCLUB, so close the nest
			if ($currentControl == '*ONCLUBNOTAFFIL') {
				echo '</div>'. CRLF;
			}
		}
		// Close any previous windowcontentcolumn divs
		if (empty($fieldColumn) && $fieldColumn != '0' && $currentColumn != '' && $currentColumn != '*NONE' ||
			(!empty($fieldColumn) || $fieldColumn == '0') && $fieldColumn != $currentColumn) {
			if ($currentColumn != "" && $currentColumn != '*NONE') {
				echo '</div>'. CRLF;
			}
		}
		// Close any previous formsectioncontent and formsection divs
		if ($fieldType == 'section' && $fieldShow) {
			if ($openformsectioncontentdiv) {
				echo '</div>'. CRLF;
				$openformsectioncontentdiv = false;
			}
			if ($openformsectiondiv) {
				echo '</div>'. CRLF;
				echo '<div class="clear"></div>'. CRLF;
			}
		}

		// Open a new windowcontentcolumn div
		if ((!empty($fieldColumn) || $fieldColumn == '0') && $fieldColumn != $currentColumn) {
			$currentColumn = $fieldColumn;
			echo '<div class="windowcontentcolumn'. $currentColumn. '">'. CRLF;
		} elseif (empty($fieldColumn) && $fieldColumn != '0') {
			$currentColumn = '';
		}
		if (empty($fieldControl) && !empty($currentControl) ||
			!empty($fieldControl) && $fieldControl != $currentControl) {
			$p_OK = false;
			if (empty($fieldControl) || $fieldControl == '*NOCLEAR' || $fieldControl == '*ONADMIN') {
				$p_OK = true;
			} elseif ($fieldControl == '*ONOPTION1') {
				$p_OK = true;
				echo '<div id="hideoption1" style="display: none;">'. CRLF;
			} elseif ($fieldControl == '*ONOPTION2') {
				$p_OK = true;
				echo '<div id="hideoption2" style="display: none;">'. CRLF;
			} elseif ($fieldControl == '*ONTSHIRT') {
				$p_OK = true;
				echo '<div id="hidetshirt" style="display: none;">'. CRLF;
			} elseif ($fieldControl == '*ONOWNCHIP') {
				$p_OK = true;
				echo '<div id="hideownchip" style="display: none;">'. CRLF;
			} elseif ($fieldControl == '*ONTEAM') {
				$p_OK = true;
				echo '<div id="hideteam" style="display: none;">'. CRLF;
			} elseif ($fieldControl == '*ONCLUB') {
				$p_OK = true;
				echo '<div id="hideclub" style="display: none;">'. CRLF;
			} elseif ($fieldControl == '*ONTRANSPORT') {
				$p_OK = true;
				echo '<div id="hidetransport" style="display: none;">'. CRLF;
			} elseif ($fieldControl == '*ONTRANSPORTREQ') {
				$p_OK = true;
				echo '<div id="hidetransporttype" style="display: none;">'. CRLF;
			} elseif ($fieldControl == '*ONTRANSPORTFEE') {
				$p_OK = true;
				echo '<div id="hidetransportfee" style="display: none;">'. CRLF;
			} elseif ($fieldControl == '*ONTRANSFERAFFIL' &&
				$p_processasp == 'transfer4.php' &&
				$p_params['affil'] &&
				$_REQUEST['change'] == '0') {
				$p_OK = true;
			} elseif ($fieldControl == '*ONCHANGEAFFIL' &&
				$p_processasp == 'transfer4.php' &&
				$p_params['affil'] &&
				$_REQUEST['change'] != '0') {
				$p_OK = true;
			} elseif ($fieldControl == '*ONTRANSFERNONAFFIL' &&
				$p_processasp == 'transfer4.php' &&
				!$p_params['affil'] &&
				$_REQUEST['change'] == '0') {
				$p_OK = true;
			} elseif ($fieldControl == '*ONCHANGENONAFFIL' &&
				$p_processasp == 'transfer4.php' &&
				!$p_params['affil'] &&
				$_REQUEST['change'] != '0') {
				$p_OK = true;
			} elseif ($fieldControl == '*ONCLUBNOTAFFIL') {
				echo '<div id="hidemembtext" style="display: none;">';
				$p_OK = true;
			} elseif ($fieldControl == '*ONGIFTAID') {
				if ($p_processasp != 'transfer4.php') {
					echo '<div id="hidecharity1" style="display: none;">'. CRLF;
					$p_OK = true;
				} else {
					echo '<div id="hidecharity1" style="display: none;"></div>'. CRLF;
				}
			} elseif ($fieldControl == '*ONCHARITY') {
				$p_OK = true;
				echo '<div id="hidecharity2" style="display: none;">'. CRLF;
			} elseif ($fieldControl == '*ONCHIPFEE') {
				$p_OK = true;
				echo '<div id="hidechiprentalfee" style="display: none;">'. CRLF;
			} elseif ($fieldControl == '*ONDISCOUNT') {
				$p_OK = true;
				echo '<div id="hidediscount" style="display: none;">'. CRLF;
			}
			if (empty($fieldControl)) {
				$currentControl = '';
			} else {
				if ($fieldControl == '*ONTRANSFERAFFIL' ||
					$fieldControl == '*ONCHANGEAFFIL' ||
					$fieldControl == '*ONTRANSFERNONAFFIL' ||
					$fieldControl == '*ONCHANGENONAFFIL') {
					$currentControl = '*ONCLUB';
				} else {
					$currentControl = $fieldControl;
				}
			}
		}

		if ($p_OK) {
			//##################################################
			if ($fieldType == 'section') {

				if ($fieldShow) {
					$currentSection++;
					echo '<div class="formsection">'. CRLF;
					$openformsectiondiv = true;
					echo '<div class="formsectionimage"><img src="images/'. $currentSection. '.gif" /></div>'. CRLF;
					echo '<div class="formsectioncaption">'. $fieldCaption. '</div>'. CRLF;
					if ($fieldControl != '*NOCLEAR') {
						echo '<div class="clear"></div>'. CRLF;
						echo '<div class="formsectioncontent">'. CRLF;
						$openformsectioncontentdiv = true;
					}
				}
			//##################################################
			} elseif ($fieldType == 'salesitems') {

				if ($fieldShow) {
					$racesalesitems = RaceSalesItem::findAllForRaces($racelist, $cookieLanguageCode);
					$savedraceid = "";
					// Show all the sales items relevant to all the selected races
					foreach ($racesalesitems as $racesalesitem) {
						$tabCount++;
						echo '<div id="hidesalesitem_'. $racesalesitem->raceid. '_'. $racesalesitem->id. '" style="display: none;">'. CRLF;
						$extraHTML = ' onclick="toggleSalesItem('. $racesalesitem->raceid. ', '. $racesalesitem->id. ', '. $racesalesitem->allowquantity. ', false);"';
						if ($racesalesitem->raceid != $savedraceid) {
							echo '<div class="formcaption">&nbsp;</div>'. CRLF;
							$caption = str_replace('*RACENAME', $racesalesitem->racename, $fieldCaption);
							echo '<div class="formfield"><u>'. $caption. '</u></div><br />';
							$savedraceid = $racesalesitem->raceid;
						}
						if ($p_params['si_'. $racesalesitem->raceid. '_'. $racesalesitem->id]) {
							$extraHTML .= ' checked="checked"';
						}
						echo '<div class="formcaption">'. $racesalesitem->name. '</div>'. CRLF;
						echo '<div class="formfield">'. CRLF;
						echo '<input type="checkbox" name="si_'. $racesalesitem->raceid. '_'. $racesalesitem->id. '" id="si_'. $racesalesitem->raceid. '_'. $racesalesitem->id. '" tabindex="'. $tabCount. '"'. $extraHTML. ' dojotype="dijit.form.CheckBox" />';
						echo '<input type="hidden" id="sipriceval_'. $racesalesitem->raceid. '_'. $racesalesitem->id. '" value="'. $racesalesitem->price. '" />';
						if (!empty($racesalesitem->price)) {
							echo '&nbsp;&nbsp;<label for="si_'. $racesalesitem->raceid. '_'. $racesalesitem->id. '" id="siprice_'. $racesalesitem->raceid. '_'. $racesalesitem->id. '"></label>';
						}
						echo '</div>';
						if ($racesalesitem->allowquantity) {
							$tabCount++;
							echo '<div class="clear"></div>';
							echo '<div id="hidesalesitemqty_'. $racesalesitem->raceid. '_'. $racesalesitem->id. '" style="display: none;">'. CRLF;
							echo '<div class="formcaption">Quantity:</div>';
							echo '<div class="formfield">';
							echo '<input type="hidden" id="oldsiqty_'. $racesalesitem->raceid. '_'. $racesalesitem->id. '" value="0" />';
							echo '<input type="text" name="siqty_'. $racesalesitem->raceid. '_'. $racesalesitem->id. '" id="siqty_'. $racesalesitem->raceid. '_'. $racesalesitem->id. '" tabindex="'. $tabCount. '" value="'. $p_params['siqty_'. $racesalesitem->raceid. '_'. $racesalesitem->id]. '" dojoType="dijit.form.NumberTextBox" onkeyup="toggleSalesItemQty('. $racesalesitem->raceid. ',  '. $racesalesitem->id. ');" constraints="{min:1,places:0}" style="width: 50px;" /></div>';
							$checkMeJS .= 'if(dijit.byId(\'si_'. $racesalesitem->raceid. '_'. $racesalesitem->id. '\').checked && !dijit.byId(\'siqty_'. $racesalesitem->raceid. '_'. $racesalesitem->id. '\').isValid()){missinginfo+="\nYou must enter a valid quantity";}';

							echo '</div>';
						}
						echo '<div class="clear"></div>';
						echo '</div>';
					}
				}
			//##################################################
			} elseif ($fieldType == 'text') {

				if ($fieldName != 'email2') {
					$tabCount++;
					$extraHTML = '';
					if ($fieldName == 'searchclub') {
						echo '<div id="hidesearchclub" style="display: none;">'. CRLF;
						echo '<div id="hidesearchclub1">'. CRLF;
						$extraHTML .= ' onkeyup="getClubs(1);"';
					} elseif ($fieldName == 'clubname') {
						echo '<div id="hidesearchclub2" style="display: none;">'. CRLF;
						$extraHTML .= ' onkeyup="toggleClub();document.form1.clubname.value=\'\';document.form1.searchclub.focus();document.form1.searchclub.select();" onclick="toggleClub();document.form1.clubname.value=\'\';document.form1.searchclub.focus();document.form1.searchclub.select();"';
					} elseif ($fieldName == 'regno' ||
						$fieldName == 'discount' ||
						$fieldName == 'charval' ||
						$fieldName == 'ownchipno') {
						$extraHTML .= ' onkeyup="iehack();"';
					} elseif ($fieldName == 'couponcode') {
						$extraHTML .= ' onkeyup="document.form1.applycoupon.style.display = \'inline\';" onchange="globCouponOK=checkCoupon();"';
						$checkMeJS .= 'if(!globCouponOK){missinginfo+="\nYou must enter a valid '. strtolower(str_replace(':', '', empty($fieldCaption) ? $fieldName : $fieldCaption)). '";}';
					}
					if ($fieldShow ||
						$fieldName == 'discount' ||
						$fieldName == 'overridecategory' ||
						($fieldName == 'offercount' && isset($p_params['waitdate']) && $p_params['waitdate'] != '0000-00-00')) {
						$p_formatting = '';
						$p_validation = '';
						if ($fieldFormat == 1) {
							$p_formatting = ' propercase="true"';
						} elseif ($fieldFormat == 2) {
							$p_formatting = ' uppercase="true"';
						} elseif ($fieldFormat == 3) {
							$p_formatting = ' lowercase="true"';
						}
						if ($formfield->dbtype == '6') {
							$p_dojoType = 'dijit.form.CurrencyTextBox';
							$checkMeJS .= 'if(!dijit.byId(\''. $fieldName. '\').isValid()){missinginfo+="\nYou must enter a valid '. strtolower(str_replace(':', '', empty($fieldCaption) ? $fieldName : $fieldCaption)). '";}';
							if ($formfield->minval != $formfield->maxval) {
								$p_formatting .= ' constraints="{min:'. $formfield->minval. ',max:'. $formfield->maxval. '}"';
							}
						} elseif ($formfield->dbtype == '300') {
							$p_dojoType = 'dijit.form.Textarea';
						} else {
							$p_dojoType = 'dijit.form.TextBox';
						}
						if (!empty($fieldLength)) {
							$extraHTML .= ' maxlength="'. $fieldLength. '"';
						}
						if ($fieldControl != '*NOCLEAR') {
							echo '<div class="formcaption">'. $fieldCaption. '</div>';
							echo '<div class="formfield">';
							$openformfielddiv = true;
						} else {
							if (!empty($fieldCaption)) {
								echo '<span style="width: 80px; display: inline-block;">'. $fieldCaption. '</span>';
							}
						}
						echo '<input type="text" name="'. $fieldName. '" id="'. $fieldName. '" tabindex="'. $tabCount. '" value="'. (isset($p_params[$fieldName]) ? $p_params[$fieldName] : '').  '"'. $extraHTML. ' style="width: '. $fieldWidth. 'px;" dojoType="'. $p_dojoType. '"'. $p_formatting. ' />';
						if (!empty($formfield->memo)) {
							if ($fieldName == 'regno') {
								echo '&nbsp;&nbsp;'. $formfield->memo;
							} else {
								echo '&nbsp;&nbsp;'. strip_tags($formfield->memo);
							}
						} elseif ($fieldName == 'email') {
							echo '&nbsp;<a href="#" onclick="noemail();">None</a>';
						}
						if ($fieldName == 'searchclub') {
							echo '<div class="alignleft" id="clubsearch" style="display: none; padding: 10px; margin-left: 10px; border: thin solid">'. CRLF;
							echo '<table width="500" id="clubstable">'. CRLF;
							echo '</table>'. CRLF;
							echo '</div>'. CRLF;
							echo '</div>'. CRLF;
							echo '</div>'. CRLF;
						}
						if ($formfield->mandatory && $fieldName != 'email') {
							$checkMeJS .= 'if(document.form1.'. $fieldName. '.value==""){missinginfo+="\nYou must enter a valid '. strtolower(str_replace(':', '', empty($fieldCaption) ? $fieldName : $fieldCaption)). '";}';
						}
						if ($fieldName == 'email') {
							$checkMeJS .= 'if((document.form1.email.value.toUpperCase()!=\'*NONE\'&&(document.form1.email.value.indexOf(".")<=0||document.form1.email.value.indexOf("@")<=0))||document.form1.email.value==""){missinginfo+="\nYou must enter a valid email address or *NONE";}';
						} elseif ($fieldName == 'clubname') {
							$checkMeJS .= 'var clubsenabled=dict["clubsenabled_"+globRaceID];if(clubsenabled=="1"&&(document.form1.affil.checked)&&(document.form1.clubname.value=="")){missinginfo+="\nYou must select an affiliated club";}';
						} elseif ($fieldName == 'couponcode') {
							echo '&nbsp;<input type="button" id="applycoupon" name="applycoupon" class="smalltext" value="Apply Coupon" style="display: none;" />';
						}
					} else {
						// Output as a hidden field if set not to show
						echo '<input type="hidden" name="'. $fieldName. '" id="'. $fieldName. '" value="'. (isset($p_params[$fieldName]) ? $p_params[$fieldName] : ''). '" />';
					}
				}
			//##################################################
			} elseif ($fieldType == 'memo' && true) { // Disabled for admin

				if ($fieldShow) {
					$p_memo = $formfield->memo;
					$p_memo = str_replace('*RACEAFFILFEE', '<span id="raceaffilfee"></span>', $p_memo);
					$p_memo = str_replace('*CHARITYFEE', '<span id="charityfee"></span>', $p_memo);
					$p_memo = str_replace('*CHARITYNAME', '<span id="charityname"></span>', $p_memo);

					if ($currentColumn == 0) {
						if ($fieldControl != '*NOCLEAR') {
							echo '<div class="formcaption">'. $fieldCaption. '</div>'. CRLF;
							echo '<div class="formfield">'. CRLF;
							$openformfielddiv = true;
						}
					}
					echo $p_memo. CRLF;
				}
			//##################################################
			} elseif ($fieldType == 'radio') {

				$extraHTML = '';
				if ($fieldShow) {
					if ($fieldControl != '*NOCLEAR') {
						echo '<div class="formcaption">'. $fieldCaption. '</div>'. CRLF;
						echo '<div class="formfield">'. CRLF;
						$openformfielddiv = true;
					} else {
						if (!empty($fieldCaption)) {
							echo '<span style="width: 50px; display: inline-block;">'. $fieldCaption. '</span>';
						}
					}
					$p_values = explode(';', $formfield->values);

					if ($formfield->mandatory) {
						if ($fieldName == 'tshirt') {
							$checkMeJS .= "var tshirtsenabled=document.getElementById('hidetshirt').style.display=='none'?false:true;if(tshirtsenabled&&";
						} elseif ($fieldName == 'transportreq') {
							$checkMeJS .= "if(document.getElementById('transportreq').checked&&";
						} else {
							$checkMeJS .= 'if(';
						}
						$logic = '';
						$p_count = 0;
					}
					if ($fieldName == 'couponcode') {
						$extraHTML .= ' onchange="globCouponOK=checkCoupon();"';
					}
					foreach ($p_values as $p_value) {
						$p_pair = explode(":", $p_value);
						if (isset($p_params[$fieldName]) && $p_params[$fieldName] == $p_pair[0]) {
							$p_check = ' checked="checked"';
						} else {
							$p_check = '';
						}
						echo '<input type="radio" name="'. $fieldName. '" value="'. $p_pair[0]. '" tabindex="'. $tabCount. '"'. $p_check. $extraHTML. ' dojotype="dijit.form.RadioButton" />&nbsp;'. $p_pair[1]. '&nbsp;&nbsp;';

						if ($formfield->mandatory) {
							$checkMeJS .= $logic. '(document.form1.'. $fieldName. '['. $p_count. '].checked==false)';
							$logic = '&&';
							$p_count++;
						}
					}

					if ($formfield->mandatory) {
						$checkMeJS .= '){missinginfo+="\nYou must select a '. strtolower(str_replace(':', '', empty($fieldCaption) ? $fieldName : $fieldCaption)). '";}';
					}
				} else {
					// Output as a hidden field if set not to show
					echo '<input type="hidden" name="'. $fieldName. '" id="'. $fieldName. '" value="'. $p_params[$fieldName]. '" />';
				}
			//##################################################
			} elseif ($fieldType == 'checkbox') {

				if ($fieldShow ||
					$fieldName == 'online' ||
					$fieldName == 'paid' ||
					$fieldName == 'sendemail' ||
					$fieldName == 'createentrant' ||
					($fieldName == 'waiting' && isset($p_params['waitdate']) && $p_params['waitdate'] != '0000-00-00')) {

					$extraHTML = '';
					if ($fieldName == 'affil') {
						$extraHTML .= ' onclick="toggleAffil(true);"';
						if ($p_params[$fieldName]) {
							$extraHTML .= ' checked="checked"';
						}
					} elseif ($fieldName == 'transportreq') {
						$extraHTML .= ' onclick="toggleTransportReq();"';
					} else {
						if ($fieldName == 'online') {
							$extraHTML .= ' onclick="iehack();"';
						}
						if (isset($p_params[$fieldName]) && $p_params[$fieldName]) {
							$extraHTML .= ' checked="checked"';
						}
					}
					if ($fieldControl != '*NOCLEAR') {
						echo '<div class="formcaption">'. $fieldCaption. '</div>'. CRLF;
						echo '<div class="formfield">'. CRLF;
						$openformfielddiv = true;
					} else {
						if (!empty($fieldCaption)) {
							echo '<span style="width: 50px; display: inline-block;">'. $fieldCaption. '</span>';
						}
					}
					echo '<input type="checkbox" name="'. $fieldName. '" id="'. $fieldName. '" tabindex="'. $tabCount. '"'. $extraHTML. ' dojotype="dijit.form.CheckBox" />';
					if (!empty($formfield->memo)) {
						echo '&nbsp;&nbsp;<label for="'. $fieldName. '">';
						echo $formfield->memo;
						echo '</label>';
					}
				} else {
					// Output as a hidden field if set not to show
					echo '<input type="checkbox" name="'. $fieldName. '" id="'. $fieldName. '"'. $extraHTML. ' style="display: none;" />';
				}
			//##################################################
			} elseif ($fieldType == 'sum') {

				if ($fieldShow) {

					if ($fieldName == 'fee') {
						echo '<br /><br />';
					}
					echo '<div class="sumcaption">'. $fieldCaption. '</div>'. CRLF;
					echo '<div class="sumfield" id="'. $fieldName. '"></div>'. CRLF;
					echo '<div class="clear"></div>'. CRLF;
				} else {
					echo '<div class="sumfield" id="'. $fieldName. '" style="display: none;"></div>'. CRLF;
				}
			//##################################################
			} elseif ($fieldType == 'races') {

				// if this is admin entry update, we'll have already chosen the race on a previous page, so list only that race
				// otherwise list all the valid races.
				$racelist = "";
				$sep = "";
				$races = Race::findAllBySeries($race->id, $race->formid, $p_processasp);
				$rowColour = "white";

				foreach ($races as $race) {

					// if no race already selected, pre-select the first race displayed
					if (empty($p_params['raceid'])) {
						$p_params['raceid'] = $race->id;
					}
					$racelist .= $sep. $race->id;
					if (empty($sep)) {
						$sep = ",";
					} ?>

					<div class="<?php echo $rowColour; ?>bg">
						<div class="floatleft">
							<input type="checkbox" name="raceid_<?php echo $race->id; ?>" id="raceid_<?php echo $race->id; ?>" value="<?php echo $race->id; ?>"<?php if ($race->id == $p_params['raceid']) { echo ' checked="checked"'; } ?> tabindex="1"<?php if ($p_processasp != 'admin_new2.php') { echo ' disabled="disabled"'; } ?> onclick="toggleRaceAjax(<?php echo $race->id; ?>, false, 0, <?php echo $race->formid; ?>, '<?php echo $p_processasp; ?>');togglePreAllocsNew();" dojotype="dijit.form.CheckBox" />&nbsp;&nbsp;<label for="raceid_<?php echo $race->id; ?>"><?php echo $race->formatdate. ' '. $race->name; ?></label>
						</div>
						<?php
						if (!empty($race->limit)) { ?>
							<div class="floatright" id="hidepreallnos_<?php echo $race->id; ?>">Race number: 
								<select name="preallnos_<?php echo $race->id; ?>" id="preallnos_<?php echo $race->id; ?>" tabindex="<?php echo $tabCount; ?>" dojoType="dijit.form.FilteringSelect" required="false" style="width: 70px;">
									<option value="0"<?php if (empty($p_params['racenumber'])) { echo ' selected="selected"'; } ?>>None</option>
									<?php
									$tabCount++;
									$checkMeJS .= 'if(dijit.byId(\'raceid_'. $race->id. '\').checked && !dijit.byId(\'preallnos_'. $race->id. '\').isValid()){missinginfo += "\nYou must enter a valid race number";}';
									if (!empty($p_params['racenumber'])) { ?>
										<option value="<?php echo $p_params['racenumber']; ?>" selected="selected"><?php echo $p_params['racenumber']; ?></option>
									<?php
									}
									$racenos = RaceNo::findAllUnused($race->id);
									foreach ($racenos as $raceno) { ?>
										<option value="<?php echo $raceno->racenumber; ?>"><?php echo $raceno->preall. $raceno->racenumber; ?></option>
									<?php
									} ?>
								</select>
							</div>
						<?php
						} ?>
						<div class="clear"></div>
					</div>
					<?php
					if ($rowColour == "white") {
						$rowColour = "grey";
					} else {
						$rowColour = "white";
					}
				}

			//##################################################
			} elseif ($fieldType == 'date') {

				if ($fieldShow) {
					if ($fieldControl != '*NOCLEAR') {
						echo '<div class="formcaption">'. $fieldCaption. '</div>'. CRLF;
						echo '<div class="formfield">'. CRLF;
						$openformfielddiv = true;
					}
					if (!isset($p_params[$fieldName]) || $p_params[$fieldName] == 0) {
						$p_params[$fieldName] = '';
					}
					if ($formfield->mandatory) {
						$required = 'true';
					} else {
						$required = 'false';
					}
					echo '<input type="text" name="'. $fieldName. '" id="'. $fieldName. '" value="'. $p_params[$fieldName]. '" tabindex="'. $tabCount. '" dojoType="dijit.form.DateTextBox" required="'. $required. '" promptmessage="dd/mm/yyyy" onchange="format'. $fieldName. '();" style="width: 100px;">';
					// Output validation javascript
					$checkMeJS .= 'if(!dijit.byId(\''. $fieldName. '\').isValid()){missinginfo+="\nYou must enter a valid date of birth";}'. CRLF. '
						if (dojo.byId(\''. $fieldName. '\').value) {'. CRLF. '
							var dob=new Date(dojo.date.locale.parse(dojo.byId(\''. $fieldName. '\').value,{selector: \'date\'}));'. CRLF. '
							var comp=new Date();'. CRLF. '
							var raceYear;'. CRLF. '
							var raceMonth;'. CRLF. '
							var raceDay;'. CRLF. '
							var minimumAge;'. CRLF. '
							for(i=0;i<document.form1.elements.length;i++){'. CRLF. '
								fieldid=document.form1.elements[i].id;'. CRLF. '
								if(fieldid=="firstname"){break;}else{'. CRLF. '
									if(fieldid.substring(0,7)=="raceid_"){'. CRLF. '
										if(document.form1.elements[i].checked){'. CRLF. '
											raceid=fieldid.substring(7,fieldid.length);'. CRLF. '
											raceYear=dict["raceyear_"+raceid];'. CRLF. '
											raceMonth=dict["racemonth_"+raceid];'. CRLF. '
											raceDay=dict["raceday_"+raceid];'. CRLF. '
											minimumAge=dict["minimumage_"+raceid];'. CRLF. '
											comp.setFullYear(raceYear-minimumAge,raceMonth-1,raceDay);'. CRLF. '
											if(dob>comp){missinginfo+="\nYou are too young to enter one of the races you have selected";}}}}}}';
					// Output a hidden field for the locale-formatted date
					echo '<input type="hidden" name="display'. $fieldName. '" id="display'. $fieldName. '" value="">';
					// Output date formatting javascript
					$functionNames .= 'format'. $fieldName. '();';
					$functionsJS .= 'function format'. $fieldName. '() {
						if (dijit.byId(\''. $fieldName. '\').value) {
							var year = dijit.byId(\''. $fieldName. '\').value.getFullYear();
							var month = dijit.byId(\''. $fieldName. '\').value.getMonth();
							var date = dijit.byId(\''. $fieldName. '\').value.getDate();
							var d = new Date(year, month, date);
							document.form1.display'. $fieldName. '.value = dojo.date.locale.format(d, {selector: \'date\', formatLength: \'long\', locale: \''. $cookieLanguageCode. '\'});}}';
				} else {
					// Output as a hidden field if set not to show
					echo '<input type="hidden" name="'. $fieldName. '" id="'. $fieldName. '" value="'. $p_params[$fieldName]. '" />';
				}
			//##################################################
			} elseif ($fieldType == 'datetime') {

				if ($fieldShow ||
					$fieldName == 'waitdate') {
					if (isset($p_params['waitdate']) && $p_params['waitdate'] != '0000-00-00') {
						echo '<div class="formcaption">'. $fieldCaption. '</div>'. CRLF;
						echo '<div class="formfield">'. CRLF;
						$openformfielddiv = true;
						echo '<input type="text" name="waitdate" id="waitdate" tabindex="'. $tabCount. '" value="'. (isset($p_params['waitdate']) ? $p_params['waitdate'] : ''). '" style="width: 80px;" dojoType="dijit.form.DateTextBox" />';
						$tabCount++;
						echo '<input type="text" name="waittime" id="waittime" tabindex="'. $tabCount. '" value="'. (isset($p_params['waittime']) ? $p_params['waittime'] : ''). '" style="width: 80px;" dojoType="dijit.form.TimeTextBox" constraints="{timePattern:\'HH:mm:ss\'}" />';
						$tabCount++;
					}
				} else {
					// Output as a hidden field if set not to show
					echo '<input type="hidden" name="'. $fieldName. '" id="'. $fieldName. '" value="'. $p_params[$fieldName]. '" />';
				}
			}
		}
		if ($fieldControl == '*ONTRANSFERAFFIL' ||
			$fieldControl == '*ONCHANGEAFFIL' ||
			$fieldControl == '*ONTRANSFERNONAFFIL' ||
			$fieldControl == '*ONCHANGENONAFFIL') {
			$p_OK = true;
		}
		if ($fieldShow) {
			$lastType = $fieldType;
		}
		if (empty($fieldControl)) {
			$lastControl = '';
		} else {
			$lastControl = $fieldControl;
		}
	}

	if ($openformfielddiv) {
		echo '</div>';	// Close previous formfield
	}
	if ($openformsectioncontentdiv) {
		echo '</div>';	// Close previous formsectioncontent
	}
	if ($openformsectiondiv) {
		echo '</div>';	// Close previous formsection
	}

	echo '<br /><br /><div class="buttonblock"><div class="button"><a href="javascript:history.go(-1);">Cancel</a></div><div class="button"><a href="javascript:checkform();">Continue to step 3</a></div></div>';
	echo '<div class="clear"></div>';

	echo '</div>';	// Close previous windowcontentcolumn
	if (!empty($currentControl)) {
		echo '</div>'; // Close previous control div
	} ?>
</form>
<?php
echo '<script type="text/javascript">'. CRLF. $checkMeJS. 'if (missinginfo != ""){'. CRLF. 'alert(missinginfo);}else{'. CRLF. $functionNames. CRLF. 'document.form1.submit();}}'. CRLF. $functionsJS. CRLF. '</script>'; ?>