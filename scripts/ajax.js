function addrow(page, tablename, arr, rowNumber) {
	var tbl = document.getElementById(tablename);
	var lastRow = tbl.rows.length;
	var row = tbl.insertRow(lastRow);
	for (col = 0; col < arr.length; col++) {
		var cell = row.insertCell(col);
		var textNode = document.createTextNode('');
		var anchorEl = document.createElement('a');
		if (tablename == 'entranttable' && col == 0 && rowNumber > 0) {
			if (arr[col] == 'More...') {
				anchorEl.href = 'javascript: getEntrants(' + ++page + ');';
				textNode.appendData('More entrants available...');
			} else {
				anchorEl.href = 'javascript: submitForm(' + arr[col] + ');';
				textNode.appendData(arr[col + 1].replace('&amp;', '&'));
			}
			anchorEl.appendChild(textNode);
			cell.appendChild(anchorEl);
		} else if (tablename == 'duplicatestable' && col == 0 && arr[col] == 'More...' && rowNumber > 0) {
			anchorEl.href = 'javascript: getPotentialDups(' + ++page + ');';
			textNode.appendData('More potential duplicates available...');
			anchorEl.appendChild(textNode);
			cell.appendChild(anchorEl);
		} else if (tablename == 'clubstable' && col == 0 && rowNumber > 0) {
			if (arr[col] == 'More...') {
				anchorEl.href = 'javascript: getClubs(' + ++page + ');';
				textNode.appendData('More clubs available...');
			} else {
				anchorEl.href = 'javascript: setClub(' + arr[col] + ', \'' + arr[col + 1] + '\', \'' + arr[col + 2].toLowerCase() + '\');';
				textNode.appendData(arr[col + 1].replace('&amp;', '&'));
			}
			anchorEl.appendChild(textNode);
			cell.appendChild(anchorEl);
		} else if (tablename == 'clubstable' && col > 0) {
		} else if (tablename != 'entranttable' || col != 1) {
			textNode.appendData(arr[col].replace('&amp;', '&'));
			cell.appendChild(textNode);
		}
	}
}