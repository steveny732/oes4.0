var ajaxPotentialDupsRequest = false;

try {
	// Opera 8.0+, Firefox, Safari
	ajaxPotentialDupsRequest = new XMLHttpRequest();
} catch (e) {
	// IE
	try {
		ajaxPotentialDupsRequest = new ActiveXObject('Msxml2.XMLHTTP');
	} catch (e) {
		try {
			ajaxPotentialDupsRequest = new ActiveXObject('Microsoft.XMLHTTP');
		} catch (e) {
			alert('Error in Ajax initialisation');
		}
	}
}

function getPotentialDups(page) {
	
	var tbl = document.getElementById('duplicatestable');
	while (tbl.rows.length > 0) {
		tbl.deleteRow(tbl.rows.length - 1);
	}

	if (document.form1.surname.value.length != 0) {
		ajaxPotentialDupsRequest.open('GET', 'admin_getpotentialdups.php?key=' + document.form1.surname.value + '&newpage=' + page, true);
		ajaxPotentialDupsRequest.onreadystatechange = function() {
			if (ajaxPotentialDupsRequest.readyState == 4) {
				if (ajaxPotentialDupsRequest.status == 200) {
					var xmldoc = ajaxPotentialDupsRequest.responseXML;
					var root = xmldoc.getElementsByTagName('root').item(0);
					
					for (var iNode = 0; iNode < root.childNodes.length; iNode++) {
						var node = root.childNodes.item(iNode);
						var row = 0;
						for (i = 0; i < node.childNodes.length; i++) {
							var sibl = node.childNodes.item(i);
							var len = parseInt(sibl.childNodes.length / 2);
							var arr = new Array(len);
							var cnt = 0;
							for (x = 0; x < sibl.childNodes.length; x++) {
								var sibl2 = sibl.childNodes.item(x);
								var sibl3;
								if (sibl2.childNodes.length > 0) {
									sibl3 = sibl2.childNodes.item(0);
									arr[cnt] = sibl3.data;   
									cnt++;
								}
							}
							if (cnt > 0) {
								addrow(page, 'duplicatestable', arr, row++);
							}
						}
					}
				} else {
					//alert('There was a problem with the request.');
				}
			}
		}
		ajaxPotentialDupsRequest.send(null); 
	}
}