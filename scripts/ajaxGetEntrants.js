var ajaxEntrantRequest = false;

try {
	// Opera 8.0+, Firefox, Safari
	ajaxEntrantRequest = new XMLHttpRequest();
} catch (e) {
	// IE
	try {
		ajaxEntrantRequest = new ActiveXObject('Msxml2.XMLHTTP');
	} catch (e) {
		try {
			ajaxEntrantRequest = new ActiveXObject('Microsoft.XMLHTTP');
		} catch (e) {
			alert('Error in Ajax initialisation');
		}
	}
}

function getEntrants(page) {
	
	var tbl = document.getElementById('entranttable');
	while (tbl.rows.length > 0) {
		tbl.deleteRow(tbl.rows.length - 1);
	}

	if (document.form1.surname.value.length == 0) {
		document.getElementById("surnamesearch").style.display = 'none';
	} else {
		document.getElementById("surnamesearch").style.display = '';
		ajaxEntrantRequest.open('GET', 'admin_getentrants.php?key=' + document.form1.surname.value + '&newpage=' + page, true);
		ajaxEntrantRequest.onreadystatechange = function() {
			if (ajaxEntrantRequest.readyState == 4) {
				if (ajaxEntrantRequest.status == 200) {
					var xmldoc = ajaxEntrantRequest.responseXML;
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
								addrow(page, 'entranttable', arr, row++);
							}
						}
					}
				} else {
					//alert('There was a problem with the request.');
				}
			}
		}
		ajaxEntrantRequest.send(null); 
	}
}

function submitForm(entrantid) {
	document.form1.entrantid.value = entrantid;
	document.form1.submit();
}