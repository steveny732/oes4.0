var globRaceFee, globAffilFee, globTransportFee, globCharityFee, globOnlineFee, globChipRentalFee, globDiscount, globRaceID, globCouponName, globSalesItems, globTotalCouponDiscount, globGotCoupon, globCouponOK;
var races;
var salesitems;
var dict = {};

var ajaxRaceRequest = false;

try {
	// Opera 8.0+, Firefox, Safari
	ajaxRaceRequest = new XMLHttpRequest();
} catch (e) {
	// IE
	try {
		ajaxRaceRequest = new ActiveXObject('Msxml2.XMLHTTP');
	} catch (e) {
		try {
			ajaxRaceRequest = new ActiveXObject('Microsoft.XMLHTTP');
		} catch (e) {
			alert('Error in Ajax initialisation');
		}
	}
}

function getRaces(raceid, formid, processurl) {

	ajaxRaceRequest.open('GET', 'admin_getraces.php?full=1&raceid=' + raceid + '&formid=' + formid + '&processurl=' + processurl, false);
	ajaxRaceRequest.onreadystatechange = function() {
		if (ajaxRaceRequest.readyState == 4) {
			if (ajaxRaceRequest.status == 200) {
				var xmldoc = ajaxRaceRequest.responseXML;
				races = xmldoc.getElementsByTagName('race');
				salesitems = xmldoc.getElementsByTagName('salesitem');
			} else {
				//alert('There was a problem with the request.');
			}
		}
	}
	ajaxRaceRequest.send(null); 
}

function noemail() {
	document.form1.email.value = '*NONE';
}

function toggleRaceAjax(raceid, applyDiscount, thisDiscount, formid, processurl) {

	// We enable the options if any race has them enabled
	gotTeamsEnabledRace = false;
	gotClubsEnabledRace = false;
	gotTransportEnabledRace = false;
	gotTShirtsEnabledRace = false;
	gotOwnChipAllowedRace = false;
	gotOption1EnabledRace = false;
	gotOption2EnabledRace = false;
	gotOnlineFeeRace = false;
	gotChipRentalFeeRace = false;
	gotCharityEnabledRace = false;
	option1Caption = "";
	option2Caption = "";
	globRaceFee = 0;
	globAffilFee = 0;
	globTransportFee = 0;
	globCharityFee = 0;
	var charityFee = 0;
	var charityName = "";
	var siteCurrency = document.form1.currency.value;

	// If no race passed, set it to the raceid of the last checked race
	// Also, determine any multi-race discounts
	globDiscount = 0;
	raceSelected = false;
	savedSeriesID = "";
	raceCount = 0;
	applyDiscount = false;
	// Get race details via Ajax
	getRaces(raceid, formid, processurl);
	for (var raceIndex = 0; raceIndex < races.length; raceIndex++) {
		var raceid = races.item(raceIndex).getElementsByTagName('id').item(0).childNodes.item(0).nodeValue;
		var raceCheckbox = document.getElementById("raceid_" + raceid);
		if (raceCheckbox != null) {
			// Store stuff
			dict["limit_" + raceid] = races.item(raceIndex).getElementsByTagName("limit").item(0).childNodes.item(0).nodeValue;
			dict["clubsenabled_" + raceid] = races.item(raceIndex).getElementsByTagName("clubsenabled").item(0).childNodes.item(0).nodeValue;
			dict["raceyear_" + raceid] = races.item(raceIndex).getElementsByTagName("raceyear").item(0).childNodes.item(0).nodeValue;
			dict["racemonth_" + raceid] = races.item(raceIndex).getElementsByTagName("racemonth").item(0).childNodes.item(0).nodeValue;
			dict["raceday_" + raceid] = races.item(raceIndex).getElementsByTagName("raceday").item(0).childNodes.item(0).nodeValue;
			dict["minimumage_" + raceid] = races.item(raceIndex).getElementsByTagName("minimumage").item(0).childNodes.item(0).nodeValue;
			raceSelected = raceCheckbox.checked;
			if (races.item(raceIndex).getElementsByTagName("seriesid").item(0).childNodes.length > 0) {
				seriesid = races.item(raceIndex).getElementsByTagName("seriesid").item(0).childNodes.item(0).nodeValue;
				if (seriesid != '') {
					if (seriesid != savedSeriesID) {
						savedSeriesID = seriesid;
						if (raceCount > 0) {
							raceCount = 0;
							applyDiscount = false;
							globDiscount = 0;
						}
					}
				}
			}
			if (raceSelected) {
				raceCount++;
				var discnoraces = 0;
				if (races.item(raceIndex).getElementsByTagName("discnoraces").item(0).childNodes.length > 0) {
					discnoraces = races.item(raceIndex).getElementsByTagName("discnoraces").item(0).childNodes.item(0).nodeValue;
					if (discnoraces != '') {
						if (raceCount >= discnoraces) {
							applyDiscount = true;
						}
					}
				}
				var discount = 0;
				if (races.item(raceIndex).getElementsByTagName("discount").item(0).childNodes.length > 0) {
					discount = races.item(raceIndex).getElementsByTagName("discount").item(0).childNodes.item(0).nodeValue;
					if (applyDiscount && discount != '') {
						globDiscount = globDiscount + parseFloat(discount);
					}
				}
				var teamsenabled = races.item(raceIndex).getElementsByTagName("teamsenabled").item(0).childNodes.item(0).nodeValue;
				if (teamsenabled == '1' && raceSelected) {
					gotTeamsEnabledRace = true;
				}
				var clubsenabled = races.item(raceIndex).getElementsByTagName("clubsenabled").item(0).childNodes.item(0).nodeValue;
				if (clubsenabled == '1' && raceSelected) {
					gotClubsEnabledRace = true;
				}
				var transportenabled = races.item(raceIndex).getElementsByTagName("transportenabled").item(0).childNodes.item(0).nodeValue;
				if (transportenabled == '1' && raceSelected) {
					gotTransportEnabledRace = true;
				}
				var tshirtsenabled = races.item(raceIndex).getElementsByTagName("tshirtsenabled").item(0).childNodes.item(0).nodeValue;
				if (tshirtsenabled == '1' && raceSelected) {
					gotTShirtsEnabledRace = true;
				}
				var ownchipallowed = races.item(raceIndex).getElementsByTagName("ownchipallowed").item(0).childNodes.item(0).nodeValue;
				if (ownchipallowed == '1' && raceSelected) {
					gotOwnChipAllowedRace = true;
				}
				var option1enabled = races.item(raceIndex).getElementsByTagName("option1enabled").item(0).childNodes.item(0).nodeValue;
				if (option1enabled == '1' && raceSelected) {
					gotOption1EnabledRace = true;
				}
				var optioncaption = "";
				if (races.item(raceIndex).getElementsByTagName("option1caption").item(0).childNodes.length > 0) {
					optioncaption = races.item(raceIndex).getElementsByTagName('option1caption').item(0).childNodes.item(0).nodeValue;
					if (gotOption1EnabledRace && option1caption == '') {
						option1Caption = optioncaption;
					}
				}
				var option2enabled = races.item(raceIndex).getElementsByTagName("option2enabled").item(0).childNodes.item(0).nodeValue;
				if (option2enabled == '1' && raceSelected) {
					gotOption2EnabledRace = true;
				}
				if (races.item(raceIndex).getElementsByTagName("option2caption").item(0).childNodes.length > 0) {
					optioncaption = races.item(raceIndex).getElementsByTagName("option2caption").item(0).childNodes.item(0).nodeValue;
					if (gotOption2EnabledRace && option2Caption == '') {
						option2Caption = optioncaption;
					}
				}
				var onlineFee = races.item(raceIndex).getElementsByTagName("onlinefee").item(0).childNodes.item(0).nodeValue;
				if (onlineFee != 0 && raceSelected) {
					gotOnlineFeeRace = true;
				}
				var chipRentalFee = races.item(raceIndex).getElementsByTagName("chiprentalfee").item(0).childNodes.item(0).nodeValue;
				if (chipRentalFee != 0 && raceSelected) {
					gotChipRentalFeeRace = true;
				}
				if (charityFee == 0) {
					if (races.item(raceIndex).getElementsByTagName("charityfee").item(0).childNodes.length > 0) {
						charityFee = races.item(raceIndex).getElementsByTagName("charityfee").item(0).childNodes.item(0).nodeValue;
					}
				}
				if (charityFee != 0 && raceSelected) {
					gotCharityEnabledRace = true;
				}
				var raceFee = races.item(raceIndex).getElementsByTagName("fee").item(0).childNodes.item(0).nodeValue;
				var affilFee = races.item(raceIndex).getElementsByTagName("affilfee").item(0).childNodes.item(0).nodeValue;
				var transportFee = races.item(raceIndex).getElementsByTagName("transportfee").item(0).childNodes.item(0).nodeValue;
				if (charityName == "") {
					if (races.item(raceIndex).getElementsByTagName("charityname").item(0).childNodes.length > 0) {
						charityName = races.item(raceIndex).getElementsByTagName("charityname").item(0).childNodes.item(0).nodeValue;
					}
				}
				globRaceID = raceid;
				globRaceFee += parseFloat(raceFee);
				globAffilFee += parseFloat(affilFee);
				globTransportFee += parseFloat(transportFee);
				globCharityFee += parseFloat(charityFee);
				if (gotOnlineFeeRace) {
					if (globOnlineFee == 0) {
						globOnlineFee += parseFloat(onlineFee);
					}
				} else {
					globOnlineFee = 0;
				}
				if (gotChipRentalFeeRace) {
					if (globChipRentalFee == 0) {
						globChipRentalFee += parseFloat(chipRentalFee);
					}
				} else {
					globChipRentalFee = 0;
				}
			}
		}
	}
	for (var salesitemIndex = 0; salesitemIndex < salesitems.length; salesitemIndex++) {
		var salesitemid = salesitems.item(salesitemIndex).getElementsByTagName("id").item(0).childNodes.item(0).nodeValue;
		var salesitemprice = salesitems.item(salesitemIndex).getElementsByTagName("price").item(0).childNodes.item(0).nodeValue;
		var salesitemallowquantity = salesitems.item(salesitemIndex).getElementsByTagName("allowquantity").item(0).childNodes.item(0).nodeValue;
		var salesitemraceid = salesitems.item(salesitemIndex).getElementsByTagName("raceid").item(0).childNodes.item(0).nodeValue;
		var raceCheckbox = document.getElementById("raceid_" + salesitemraceid);
		MM_setTextOfLayer('siprice_' + salesitemraceid + '_' + salesitemid, '', dojo.currency.format(salesitemprice, {currency: siteCurrency}));
		if (raceCheckbox != null) {
			if (raceCheckbox.checked) {
				document.getElementById("hidesalesitem_" + salesitemraceid + '_' + salesitemid).style.display = '';
			} else {
				document.getElementById("hidesalesitem_" + salesitemraceid + '_' + salesitemid).style.display = 'none';
			}
		}
	}

	// If teams enabled, hide all the affiliation/club stuff
	if (gotTeamsEnabledRace) {
		document.getElementById("hideteam").style.display = '';
	} else {
		// Clear team information if it is just about to be hidden
		document.form1.team.value = "";
		document.getElementById("hideteam").style.display = 'none';
	}
	// If clubs enabled, show the relevant section
	if (gotClubsEnabledRace) {
		document.getElementById("hideclub").style.display = '';
	} else {
		// Clear club affiliation information if it is just about to be hidden
		document.getElementById("hideclub").style.display = 'none';
		try {
			dijit.byId('affil').setValue(false);
		} catch(e) {
		}
		document.form1.clubname.value = "";
		document.form1.clubid.value = "";
		document.form1.searchclub.value = "";
		document.form1.regno.value = "";
		document.getElementById("hidesearchclub").style.display = 'none';
		toggleClub();
		getClubs(1);
	}
	// If transport enabled, show the relevant section
	if (gotTransportEnabledRace) {
		document.getElementById("hidetransport").style.display = '';
		document.getElementById("hidetransporttype").style.display = '';
	} else {
		document.getElementById("hidetransport").style.display = 'none';
		document.getElementById("hidetransporttype").style.display = 'none';
	}
	toggleTransportReq();
	// If t-shirts enabled, show the relevant section
	if (gotTShirtsEnabledRace) {
		document.getElementById("hidetshirt").style.display = '';
	} else {
		document.getElementById("hidetshirt").style.display = 'none';
	}
	// If own chip allowed, show the relevant section
	if (gotOwnChipAllowedRace) {
		document.getElementById("hideownchip").style.display = '';
	} else {
		document.getElementById("hideownchip").style.display = 'none';
		document.form1.ownchipno.value = "";
	}
	// If options enabled, show the relevant section
	if (gotOption1EnabledRace) {
		document.getElementById("hideoption1").style.display = '';
		MM_setTextOfLayer('option1caption', '', option1Caption);
	} else {
		document.getElementById("hideoption1").style.display = 'none';
	}
	if (gotOption2EnabledRace) {
		document.getElementById("hideoption2").style.display = '';
		MM_setTextOfLayer('option2caption', '', option2Caption);
	} else {
		document.getElementById("hideoption2").style.display = 'none';
	}
	// If charity fee set, show the relevant section
	if (gotCharityEnabledRace) {
		document.getElementById("hidecharity1").style.display = '';
		document.getElementById("hidecharity2").style.display = '';
	} else {
		document.getElementById("hidecharity1").style.display = 'none';
		document.getElementById("hidecharity2").style.display = 'none';
	}

	MM_setTextOfLayer('charityfee', '', dojo.currency.format(charityFee, {currency: siteCurrency}));
	MM_setTextOfLayer('charityname', '', charityName);

	recalc();
}

function toggleRace(raceid, applyDiscount, thisDiscount) {

	// We enable the options if any race has them enabled
	gotTeamsEnabledRace = false;
	gotClubsEnabledRace = false;
	gotTransportEnabledRace = false;
	gotTShirtsEnabledRace = false;
	gotOwnChipAllowedRace = false;
	gotOption1EnabledRace = false;
	gotOption2EnabledRace = false;
	gotOnlineFeeRace = false;
	gotChipRentalFeeRace = false;
	gotCharityEnabledRace = false;
	option1Caption = "";
	option2Caption = "";

	// If no race passed, set it to the raceid of the last checked race
	// Also, determine any multi-race discounts
	globDiscount = 0;
	raceSelected = false;
	savedSeriesID = "";
	raceCount = 0;
	applyDiscount = false;
	for (var i = 0; i < document.form1.elements.length; i++) {
		var fieldid = document.form1.elements[i].id;
		if (document.form1.elements[i].type == 'checkbox') {
			// Drop out of the loop when we've reached the end of the race stuff
			if (fieldid == 'firstname') {
				break;
			} else if (fieldid.substring(0, 7) == 'raceid_') {
				raceSelected = document.form1.elements[i].checked;
				if (raceSelected) {
					if (raceid == '') {
						raceid = fieldid.substring(7, fieldid.length);
					}
				}
			}
		} else {
			if (fieldid.substring(0, 9) == 'seriesid_') {
				seriesid = document.form1.elements[i].value;
				if (seriesid != savedSeriesID) {
					savedSeriesID = seriesid;
					if (raceCount > 0) {
						raceCount = 0;
						applyDiscount = false;
						globDiscount = 0;
					}
				}
				if (raceSelected) {
					raceCount++;
				}
			} else if (fieldid.substring(0, 12) == 'discnoraces_') {
				if (document.form1.elements[i].value != '') {
					if (raceCount >= document.form1.elements[i].value) {
						applyDiscount = true;
					}
				}
			} else if (fieldid.substring(0, 9) == 'discount_') {
				if (applyDiscount && document.form1.elements[i].value != '') {
					globDiscount = globDiscount + parseFloat(document.form1.elements[i].value);
				}
			} else if (fieldid.substring(0, 13) == 'teamsenabled_') {
				if (document.form1.elements[i].value == '1' && raceSelected) {
					gotTeamsEnabledRace = true;
				}
			} else if (fieldid.substring(0, 13) == 'clubsenabled_') {
				if (document.form1.elements[i].value == '1' && raceSelected) {
					gotClubsEnabledRace = true;
				}
			} else if (fieldid.substring(0, 17) == 'transportenabled_') {
				if (document.form1.elements[i].value == '1' && raceSelected) {
					gotTransportEnabledRace = true;
				}
			} else if (fieldid.substring(0, 15) == 'tshirtsenabled_') {
				if (document.form1.elements[i].value == '1' && raceSelected) {
					gotTShirtsEnabledRace = true;
				}
			} else if (fieldid.substring(0, 15) == 'ownchipallowed_') {
				if (document.form1.elements[i].value == '1' && raceSelected) {
					gotOwnChipAllowedRace = true;
				}
			} else if (fieldid.substring(0, 15) == 'option1enabled_') {
				if (document.form1.elements[i].value == '1' && raceSelected) {
					gotOption1EnabledRace = true;
				}
			} else if (fieldid.substring(0, 15) == 'option1caption_') {
				if (gotOption1EnabledRace && option1Caption == '') {
					option1Caption = document.form1.elements[i].value;
				}
			} else if (fieldid.substring(0, 15) == 'option2enabled_') {
				if (document.form1.elements[i].value == '1' && raceSelected) {
					gotOption2EnabledRace = true;
				}
			} else if (fieldid.substring(0, 15) == 'option2caption_') {
				if (gotOption2EnabledRace && option2Caption == '') {
					option2Caption = document.form1.elements[i].value;
				}
			} else if (fieldid.substring(0, 10) == 'onlinefee_') {
				if (document.form1.elements[i].value != 0 && raceSelected) {
					gotOnlineFeeRace = true;
				}
			} else if (fieldid.substring(0, 14) == 'chiprentalfee_') {
				if (document.form1.elements[i].value != 0 && raceSelected) {
					gotChipRentalFeeRace = true;
				}
			} else if (fieldid.substring(0, 11) == 'charityfee_') {
				if (document.form1.elements[i].value != 0 && raceSelected) {
					gotCharityEnabledRace = true;
				}
			}
		}
	}

	// If teams enabled, hide all the affiliation/club stuff
	if (gotTeamsEnabledRace) {
		document.getElementById("hideteam").style.display = '';
	} else {
		// Clear team information if it is just about to be hidden
		document.form1.team.value = "";
		document.getElementById("hideteam").style.display = 'none';
	}
	// If clubs enabled, show the relevant section
	if (gotClubsEnabledRace) {
		document.getElementById("hideclub").style.display = '';
	} else {
		// Clear club affiliation information if it is just about to be hidden
		document.getElementById("hideclub").style.display = 'none';
		try {
			dijit.byId('affil').setValue(false);
		} catch(e) {
		}
		document.form1.clubname.value = "";
		document.form1.clubid.value = "";
		document.form1.searchclub.value = "";
		document.form1.regno.value = "";
		document.getElementById("hidesearchclub").style.display = 'none';
		toggleClub();
		getClubs(1);
	}
	// If transport enabled, show the relevant section
	if (gotTransportEnabledRace) {
		document.getElementById("hidetransport").style.display = '';
		document.getElementById("hidetransporttype").style.display = '';
	} else {
		document.getElementById("hidetransport").style.display = 'none';
		document.getElementById("hidetransporttype").style.display = 'none';
	}
	toggleTransportReq();
	// If t-shirts enabled, show the relevant section
	if (gotTShirtsEnabledRace) {
		document.getElementById("hidetshirt").style.display = '';
	} else {
		document.getElementById("hidetshirt").style.display = 'none';
	}
	// If own chip allowed, show the relevant section
	if (gotOwnChipAllowedRace) {
		document.getElementById("hideownchip").style.display = '';
	} else {
		document.getElementById("hideownchip").style.display = 'none';
		document.form1.ownchipno.value = "";
	}
	// If options enabled, show the relevant section
	if (gotOption1EnabledRace) {
		document.getElementById("hideoption1").style.display = '';
		MM_setTextOfLayer('option1caption', '', option1Caption);
	} else {
		document.getElementById("hideoption1").style.display = 'none';
	}
	if (gotOption2EnabledRace) {
		document.getElementById("hideoption2").style.display = '';
		MM_setTextOfLayer('option2caption', '', option2Caption);
	} else {
		document.getElementById("hideoption2").style.display = 'none';
	}
	// If charity fee set, show the relevant section
	if (gotCharityEnabledRace) {
		document.getElementById("hidecharity1").style.display = '';
		document.getElementById("hidecharity2").style.display = '';
	} else {
		document.getElementById("hidecharity1").style.display = 'none';
		document.getElementById("hidecharity2").style.display = 'none';
	}

	var raceID = raceid;
	var raceFee = document.getElementById("fee_" + raceID).value;
	var affilFee = document.getElementById("affilfee_" + raceID).value;
	var transportFee = document.getElementById("transportfee_" + raceID).value;
	var onlineFee = document.getElementById("onlinefee_" + raceID).value;
	var chipRentalFee = document.getElementById("chiprentalfee_" + raceID).value;
	var charityFee = document.getElementById("charityfee_" + raceID).value;
	var charityName = document.getElementById("charityname_" + raceID).value;

	var siteCurrency = document.form1.currency.value;
	MM_setTextOfLayer('charityfee', '', dojo.currency.format(charityFee, {currency: siteCurrency}));
	MM_setTextOfLayer('charityname', '', charityName);

	// Add or subtract the race values depending on whether the race just toggled is selected or not
	if (document.getElementById("raceid_" + raceid).checked) {
		globRaceID = raceid;
		globRaceFee += parseFloat(raceFee);
		globAffilFee += parseFloat(affilFee);
		globTransportFee += parseFloat(transportFee);
		globCharityFee += parseFloat(charityFee);
	} else {
		if (globRaceFee != 0) {
			globRaceFee -= parseFloat(raceFee);
			globAffilFee -= parseFloat(affilFee);
			globTransportFee -= parseFloat(transportFee);
			globCharityFee -= parseFloat(charityFee);
		}
	}
	
	// Add/subtract the online fees and chip rental fees only once
	if (gotOnlineFeeRace) {
		if (globOnlineFee == 0) {
			globOnlineFee += parseFloat(onlineFee);
		}
	} else {
		globOnlineFee = 0;
	}
	if (gotChipRentalFeeRace) {
		if (globChipRentalFee == 0) {
			globChipRentalFee += parseFloat(chipRentalFee);
		}
	} else {
		globChipRentalFee = 0;
	}

	recalc();
}

function toggleAffil(setfocus) {
	if (document.form1.affil.checked) {
		document.getElementById("hidesearchclub").style.display = '';
		if (setfocus) {
			toggleClub();
			document.form1.searchclub.focus();
		} else {
			toggleSearchClub();
		}
	} else {
		document.form1.clubname.value = "";
		document.form1.clubid.value = "";
		document.form1.regno.value = "";
		document.getElementById("hidesearchclub").style.display = 'none';
	}
	recalc();
}

function togglePreAllocsNew() {
	for (var i = 0; i < document.form1.elements.length; i++) {
		if (document.form1.elements[i].type == 'checkbox') {
			fieldid = document.form1.elements[i].id;
			// Drop out of the loop when we've reached the end of the race stuff
			if (fieldid.substring(0, 4) == 'paid') {
				break;
			}
			if (fieldid.substring(0, 7) == 'raceid_') {
				raceid = fieldid.substring(7, fieldid.length);
				if (dict["limit_" + raceid] != '0') {
					if (document.form1.elements[i].checked) {
						document.getElementById("hidepreallnos_" + raceid).style.display = '';
					} else {
						document.getElementById("hidepreallnos_" + raceid).style.display = 'none';
					}
				}
			}
		}
	}
}

function togglePreAllocs() {
	for (var i = 0; i < document.form1.elements.length; i++) {
		if (document.form1.elements[i].type == 'checkbox') {
			fieldid = document.form1.elements[i].id;
			// Drop out of the loop when we've reached the end of the race stuff
			if (fieldid.substring(0, 4) == 'paid') {
				break;
			}
			if (fieldid.substring(0, 7) == 'raceid_') {
				raceid = fieldid.substring(7, fieldid.length);
				if (document.getElementById("limit_" + raceid).value != '0') {
					if (document.form1.elements[i].checked) {
						document.getElementById("hidepreallnos_" + raceid).style.display = '';
					} else {
						document.getElementById("hidepreallnos_" + raceid).style.display = 'none';
					}
				}
			}
		}
	}
}

function toggleClub() {
	document.getElementById("hidesearchclub1").style.display = '';
	document.getElementById("hidesearchclub2").style.display = 'none';
	document.form1.searchclub.value = document.form1.clubname.value
}

function toggleSearchClub() {
	document.getElementById("hidesearchclub1").style.display = 'none';
	document.getElementById("hidesearchclub2").style.display = '';
	if (document.form1.memberaffil.value == '1') {
		document.getElementById("hidemembtext").style.display = 'none';
	} else {
		document.getElementById("hidemembtext").style.display = '';
	}
	recalc();
	document.form1.regno.focus();
}

function toggleTransportReq() {
	if (document.form1.transportreq.checked) {
		document.getElementById("hidetransporttype").style.display = '';
	} else {
		document.getElementById("hidetransporttype").style.display = 'none';
	}
	recalc();
}

function toggleSalesItem(raceid, salesitemid, allowquantity, keepQuantity) {

	var el = document.getElementById("hidesalesitemqty_" + raceid + '_' + salesitemid);
	var price = new Number(document.getElementById("sipriceval_" + raceid + '_' + salesitemid).value);
	var oldqty = document.getElementById("oldsiqty_" + raceid + '_' + salesitemid);
	if (document.getElementById('si_' + raceid + '_' + salesitemid).checked) {
		if (allowquantity == 1) {
			el.style.display = '';
			globSalesItems -= oldqty.value * price;
			oldqty.value = 0;
			if (!keepQuantity) {
				document.getElementById("siqty_" + raceid + '_' + salesitemid).value = 0;
			}
		} else {
			globSalesItems += price;
		}
	} else {
		if (allowquantity == 1) {
			el.style.display = 'none';
			document.getElementById("siqty_" + raceid + '_' + salesitemid).value = 0;
			globSalesItems -= oldqty.value * price;
			oldqty.value = 0;
		} else {
			globSalesItems -= price;
		}
	}
	recalc();
}

function toggleSalesItemQty(raceid, salesitemid) {
	var oldqty = document.getElementById("oldsiqty_" + raceid + '_' + salesitemid);
	var newqty = document.getElementById("siqty_" + raceid + '_' + salesitemid).value;
	if (!isNumber(newqty)) {
		newqty = 0;
	}
	var price = new Number(document.getElementById("sipriceval_" + raceid + '_' + salesitemid).value);
	globSalesItems += (newqty - oldqty.value) * price;
	oldqty.value = newqty;
	recalc();
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function clearTotals() {
	globRaceFee = 0;
	globAffilFee = 0;
	globTransportFee = 0;
	globChipRentalFee = 0;
	globCharityFee = 0;
	globOnlineFee = 0;
	globDiscount = 0;
	globSalesItems = 0;
	globCouponName = "";
}

// Special hack function as IE6 fails to call recalc directly for some inexplicable reason
function iehack() {
	recalc();
}

function recalc() {
	var siteCurrency = document.form1.currency.value;
	var totalFee;
	var totalCharity;
	var totalDiscount = new Number(dojo.currency.parse(dojo.byId('discount').value));
	// If charity value set, show the relevant section
	var totalCharVal = new Number(dojo.byId('charval').value);
	totalCharity = globCharityFee + totalCharVal;
	if (totalCharity != 0) {
		document.getElementById("hidecharity1").style.display = '';
		document.getElementById("hidecharity2").style.display = '';
		MM_setTextOfLayer('charityfee', '', dojo.currency.format(totalCharity, {currency: siteCurrency}));
	} else {
		document.getElementById("hidecharity1").style.display = 'none';
		document.getElementById("hidecharity2").style.display = 'none';
	}
	totalFee = 0;
	globTotalCouponDiscount = 0;
	if (globGotCoupon) {
		calcCouponDiscount();
	}
	if (document.getElementById('applycoupon') != null) {
		document.getElementById('applycoupon').style.display = 'none';
	}
	totalDiscount = totalDiscount + globDiscount + globTotalCouponDiscount;

	MM_setTextOfLayer('fee', '', dojo.currency.format(globRaceFee, {currency: siteCurrency}));
	if (document.form1.affil.checked && (document.form1.memberaffil.value == '1' || document.form1.regno.value != '')) {
		MM_setTextOfLayer('afee', '', 'N/A');
	} else {
		MM_setTextOfLayer('afee', '', dojo.currency.format(globAffilFee, {currency: siteCurrency}));
		totalFee = globAffilFee;
	}
	MM_setTextOfLayer('charfee', '', dojo.currency.format(totalCharity, {currency: siteCurrency}));
	if (document.form1.ownchipno.value == '') {
		MM_setTextOfLayer('chipfee', '', dojo.currency.format(globChipRentalFee, {currency: siteCurrency}));
		totalFee = totalFee + globChipRentalFee;
	}
	if (document.form1.transportreq.checked) {
		MM_setTextOfLayer('transfee', '', dojo.currency.format(globTransportFee, {currency: siteCurrency}));
		totalFee = totalFee + globTransportFee;
		document.getElementById("hidetransportfee").style.display = '';
	} else {
		MM_setTextOfLayer('transfee', '', dojo.currency.format(0.00, {currency: siteCurrency}));
		document.getElementById("hidetransportfee").style.display = 'none';
	}
	if (document.form1.online.checked) {
		MM_setTextOfLayer('cfee', '', dojo.currency.format(globOnlineFee, {currency: siteCurrency}));
		totalFee = totalFee + globOnlineFee;
	} else {
		MM_setTextOfLayer('cfee', '', dojo.currency.format(0.00, {currency: siteCurrency}));
	}
	if (globChipRentalFee == 0 || document.form1.ownchipno.value != '') {
		document.getElementById("hidechiprentalfee").style.display = 'none';
	} else {
		document.getElementById("hidechiprentalfee").style.display = '';
	}
	MM_setTextOfLayer('sifee', '', dojo.currency.format(globSalesItems, {currency: siteCurrency}));
	if (totalDiscount == 0) {
		document.getElementById("hidediscount").style.display = 'none';
	} else {
		document.getElementById("hidediscount").style.display = '';
	}
	var discText = dojo.currency.format(totalDiscount * -1, {currency: siteCurrency});
	if (globTotalCouponDiscount != 0 && globCouponName != '') {
		discText += ' (' + globCouponName + ')';
	}
	MM_setTextOfLayer('discfee', '', discText);
	totalFee = totalFee + globRaceFee + totalCharity + globSalesItems - totalDiscount;

	MM_setTextOfLayer('tfee', '', '<strong>' + dojo.currency.format(totalFee, {currency: siteCurrency}) + '</strong>');
	document.form1.total.value = totalFee;
}

var ajaxClubsRequest = false;

try {
	// Opera 8.0+, Firefox, Safari
	ajaxClubsRequest = new XMLHttpRequest();
} catch (e) {
	// IE
	try {
		ajaxClubsRequest = new ActiveXObject('Msxml2.XMLHTTP');
	} catch (e) {
		try {
			ajaxClubsRequest = new ActiveXObject('Microsoft.XMLHTTP');
		} catch (e) {
			alert('Error in Ajax initialisation');
		}
	}
}

function getClubs(page) {
	
	var tbl = document.getElementById('clubstable');
	while (tbl.rows.length > 0) {
		tbl.deleteRow(tbl.rows.length - 1);
	}

	if (document.form1.searchclub.value.length == 0) {
		document.getElementById("clubsearch").style.display = 'none';
	} else {
		document.getElementById("clubsearch").style.display = '';
		ajaxClubsRequest.open('GET', 'getclubs.php?clubtypeid=' + document.form1.clubtypeid.value + '&key=' + document.form1.searchclub.value + '&newpage=' + page, true);
		ajaxClubsRequest.onreadystatechange = function() {
			if (ajaxClubsRequest.readyState == 4) {
				if (ajaxClubsRequest.status == 200) {
					var xmldoc = ajaxClubsRequest.responseXML;
					var root = xmldoc.getElementsByTagName('root').item(0);
					
					for (var iNode = 0; iNode < root.childNodes.length; iNode++) {
						var node = root.childNodes.item(iNode);
						var row = 0;
						for (var i = 0; i < node.childNodes.length; i++) {
							var sibl = node.childNodes.item(i);
							var len = parseInt(sibl.childNodes.length / 2);
							var arr = new Array(len);
							var cnt = 0;
							for (var x = 0; x < sibl.childNodes.length; x++) {
								var sibl2 = sibl.childNodes.item(x);
								var sibl3;
								if (sibl2.childNodes.length > 0) {
									sibl3 = sibl2.childNodes.item(0);
									arr[cnt] = sibl3.data;
									cnt++;
								}
							}
							if (cnt > 0) {
								addrow(page, 'clubstable', arr, row++);
							}
						}
					}
				} else {
					//alert('There was a problem with the request.');
				}
			}
		}
		ajaxClubsRequest.send(null); 
	}
}

function setClub(clubid, clubname, clubaffil) {
	document.form1.clubid.value = clubid;
	document.form1.clubname.value = clubname;
	document.form1.memberaffil.value = clubaffil;
	toggleSearchClub();
}

var ajaxRaceCouponRequest = false;

try {
	// Opera 8.0+, Firefox, Safari
	ajaxRaceCouponRequest = new XMLHttpRequest();
} catch (e) {
	// IE
	try {
		ajaxRaceCouponRequest = new ActiveXObject('Msxml2.XMLHTTP');
	} catch (e) {
		try {
			ajaxRaceCouponRequest = new ActiveXObject('Microsoft.XMLHTTP');
		} catch (e) {
			alert('Error in Ajax initialisation');
		}
	}
}

function getRaceDiscount(raceid, couponcode) {

	ajaxRaceCouponRequest.open('GET', 'getcoupon.php?raceid=' + raceid + '&couponcode=' + couponcode, false);
	ajaxRaceCouponRequest.send(null);

	var raceDiscount = 0;
	var xmldoc = ajaxRaceCouponRequest.responseXML;
	var root = xmldoc.getElementsByTagName('coupon').item(0);
	for (var iNode = 0; iNode < root.childNodes.length; iNode++) {
		var node = root.childNodes.item(iNode);
		for (var i = 0; i < node.childNodes.length; i++) {
			var sibl = node.childNodes.item(i);
			if (node.nodeName == 'name') {
				if (sibl.nodeValue == 'No coupon found') {
					break;
				}
			} else if (node.nodeName == 'discount') {
				if (sibl.nodeValue != ' ') {
					raceDiscount = new Number(sibl.nodeValue);
					break;
				}
			}
		}
	}
	return raceDiscount;
}

function calcCouponDiscount() {

	var couponcode;
	if (typeof(document.form1.couponcode[0]) != 'undefined' && document.form1.couponcode[0].type == 'radio') {
		for (var i = 0; i < document.form1.couponcode.length; i++) {
			if (document.form1.couponcode[i].checked) {
				couponcode = document.form1.couponcode[i].value;
				if (couponcode == '#') {
					couponcode = '';
				}
			}
		}
	} else {
		couponcode = dijit.byId('couponcode').value;
	}
	if (couponcode.length == 0) {
		return;
	} else {

		for (var i = 0; i < document.form1.elements.length; i++) {
			fieldid = document.form1.elements[i].id;
			if (document.form1.elements[i].type == 'checkbox') {
				// Drop out of the loop when we've reached the end of the race stuff
				if (fieldid == 'firstname') {
					break;
				} else if (fieldid.substring(0, 7) == 'raceid_') {
					var raceSelected = document.form1.elements[i].checked;
					if (raceSelected) {
						var raceid = fieldid.substring(7, fieldid.length);
						globTotalCouponDiscount += getRaceDiscount(raceid, couponcode);
					}
				}
			}
		}
	}
}

var ajaxCouponRequest = false;

try {
	// Opera 8.0+, Firefox, Safari
	ajaxCouponRequest = new XMLHttpRequest();
} catch (e) {
	// IE
	try {
		ajaxCouponRequest = new ActiveXObject('Msxml2.XMLHTTP');
	} catch (e) {
		try {
			ajaxCouponRequest = new ActiveXObject('Microsoft.XMLHTTP');
		} catch (e) {
			alert('Error in Ajax initialisation');
		}
	}
}

function checkCoupon() {

	var couponcode;
	if (typeof(document.form1.couponcode[0]) != 'undefined' && document.form1.couponcode[0].type == 'radio') {
		for (var i = 0; i < document.form1.couponcode.length; i++) {
			if (document.form1.couponcode[i].checked) {
				couponcode = document.form1.couponcode[i].value;
				if (couponcode == '#') {
					couponcode = '';
				}
			}
    }
	} else {
		couponcode = dijit.byId('couponcode').value;
	}
	// A blank code is always valid
	if (couponcode.length == 0) {
		globGotCoupon = false;
	} else {
		ajaxCouponRequest.open('GET', 'getcoupon.php?couponcode=' + couponcode, false);
		ajaxCouponRequest.send(null);
		globGotCoupon = false;
		var xmldoc = ajaxCouponRequest.responseXML;
		var root = xmldoc.getElementsByTagName('coupon').item(0);
		for (var iNode = 0; iNode < root.childNodes.length; iNode++) {
			var node = root.childNodes.item(iNode);
			for (var i = 0; i < node.childNodes.length; i++) {
				var sibl = node.childNodes.item(i);
				if (node.nodeName == 'name') {
					if (sibl.nodeValue == 'No coupon found') {
						globCouponName = "";
						break;
					} else {
						globCouponName = sibl.nodeValue;
					}
				} else if (node.nodeName == 'discount') {
					if (sibl.nodeValue != ' ') {
						globGotCoupon = true;
						break;
					}
				}
			}
		}
	}
	recalc();
	return globGotCoupon;
}