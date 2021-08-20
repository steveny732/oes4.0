var ColourPicker = Class.create();
ColourPicker.prototype = {
	colourArray: new Array(),
	element: null,
	trigger: null,
	example: null,
	type: null,
	tableShown: false,
    
	initialize: function(element, trigger, example, type) {
			this.colourArray = new Array();
			this.element = $(element);
			this.trigger = $(trigger);
			this.example = $(example);
			this.type = type;
        
			this.trigger.onclick = this.toggleTable.bindAsEventListener(this);
			// Initialise the color array
			this.initColourArray();
			this.buildTable();
        
	},
	initColourArray: function() {
		var colourMap = new Array('00', '33', '66', '99', 'AA', 'CC', 'EE', 'FF');
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[i] + colourMap[i] + colourMap[i]);
		}
			
		// Blue
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[0] + colourMap[i] + colourMap[7]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[i] + colourMap[0] + colourMap[7]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[0] + colourMap[i] + colourMap[5]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[i] + colourMap[0] + colourMap[5]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[0] + colourMap[i] + colourMap[3]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[i] + colourMap[0] + colourMap[3]);
		}

			
		// Green
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[0] + colourMap[7] + colourMap[i]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[i] + colourMap[7] + colourMap[0]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[0] + colourMap[5] + colourMap[i]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[i] + colourMap[5] + colourMap[0]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[0] + colourMap[3] + colourMap[i]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[i] + colourMap[3] + colourMap[0]);
		}
			
		// Red
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[7] + colourMap[i] + colourMap[0]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[7] + colourMap[0] + colourMap[i]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[5] + colourMap[i] + colourMap[0]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[5] + colourMap[0] + colourMap[i]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[3] + colourMap[i] + colourMap[0]);
		}
		for (i = 0; i < colourMap.length; i++) {
			this.colourArray.push(colourMap[3] + colourMap[0] + colourMap[i]);
		}
			
	},
	buildTable: function() {
		if (!this.tableShown) {
			html = "<table cellpadding=\"0\" cellspacing=\"0\" id=\"" + this.trigger.id + "ColourPicker\" style=\"display: none\" class=\"colorPicker\">"
			for (i = 0; i < this.colourArray.length; i++) {
				if (i % 8 == 0) {
					html += "<tr>";
				}
				html += "<td style=\"background-color: #" + this.colourArray[i] + ";\" title=\"#" + this.colourArray[i] +  "\" onClick=\"$('" + this.element.id + "').value = '#" + this.colourArray[i] + "'; $('" + this.example.id + "').style." + this.type + " = '#" + this.colourArray[i] + "'; $('" + this.trigger.id + "ColourPicker').style.display = 'none';\">";
				if (i % 8 == 7) {
					html += "</tr>";
				}
			}
			html += "</table>";
			new Insertion.After(this.trigger, html);
		}
	},
	toggleTable: function(sender) {
		var obj = $(Event.element(sender).id + 'ColourPicker');
		obj.style.display = (obj.style.display == 'block' ? 'none' : 'block');
	}
}
