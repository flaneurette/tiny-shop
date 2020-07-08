/*
 * Main.js Tiny Shop custom javascript. For external javascript, edit site.json and add the uri.
 */
 
var tinyshop = {

	// vars
	name: "tinyshop javascript library",
	version: "1.12",
	instanceid: 1000011,
	messagecode: 1e5,
	csp: ["Access-Control-Allow-Origin","*"],

	xhr: function() {

		var objxml = null;
		var ProgID = ["Msxml2.XMLHTTP.6.0", "Msxml2.XMLHTTP.3.0", "Microsoft.XMLHTTP"];

		try {
			objxml = new XMLHttpRequest();
		} catch (e) {
			for (var i = 0; i < ProgID.length; i++) {
				try {
					objxml = new ActiveXObject(ProgID[i]);
				} catch (e) {
					continue;
				}
			}
		}
		return objxml;
	},
	
	message: function(str) {
		this.message(escape(str) + '\n' + '-'.repeat(32) + '\n' + '#TS-MSGC-' + this.messagecode);
		if(this.messagecode < Number.MAX_SAFE_INTEGER) {
			this.messagecode++;
		}
	},

	redirect: function(uri) {
		if(!uri) {
			document.location = location.href;
			} else {
			document.location = escape(uri);
		}
	},
	
	math: function(method,e,mod=1) {
		
		let result = null;
		let i = 0;
		
		switch(method) {
			
			case 'int':
			if(mod > 1) {
				while(mod > i) {
					this.math('int',e,mod);
					this.result = parseInt(e);
					mod--;
				}
			} else {
			this.result = parseInt(e);
			}
			
			break;
			
			case 'float':
			this.result = parseFloat(e);
			break;	

			case 'fixed':
			this.result = e.toFixed(mod);
			break;	
			
			case 'rand':
			this.result = Math.random();
			break;
			
		}
		
		return result;
	},	
	
	rnd: function(method='rand',e=null,len=null,seed=null) {
		
		let r = null;
		switch(method) {
			case 'rand':
			this.r = Math.random();
			break;
			case 'bytes':
			this.r = Math.random();
			break;			
		}
		return this.r;
	},
	
	toggle: function(id, counter) {
		
		for (i = 0; i < counter; i++) {
			
			try {
				this.dom('toggle' + id,'display','none');
				this.dom('cat' + id,'fontWeight','100');
			} catch (e) {}
		}
		
		this.dom('toggle' + id,'display','block');
		this.dom('cat' + id,'fontWeight','bold');
	},
	
	dom: function(id,method,value='') {
		
		switch(method) {

			case 'get':
			return document.getElementById(escape(id)).value;
			break;	
			
			case 'set':
			document.getElementById(escape(id)).value = value;
			break;
			
			case 'html':
			document.getElementById(escape(id)).innerHTML = value;
			break;
			
			case 'gethtml':
			document.getElementById(escape(id)).innerHTML;
			break;	
			
			case 'display':
			document.getElementById(escape(id)).style.display = value;
			break;	
			
			case 'fontWeight':
			document.getElementById(escape(id)).style.fontWeight = value;
			break;	
			
			case 'className':
			document.getElementById(escape(id)).style.fontWeight = value;
			break;				
		}
		
		return true;
	},
	
	returner: function(data) {
		window.alert(data);
		return data;
	},

	json: function(uri) {
	 tinyshop.fetchJSON(uri,function(response) {
		var obj =  JSON.parse(response);
		return obj;
	 });
	},
	
	caller: function(uri,method) {
	
		var func = method;
		var req  = tinyshop.xhr();
		req.onreadystatechange = returncall;
		req.open("GET", uri, true); 
		req.send();
		
		function returncall() {

			if (req.readyState == 4) {	
				// add a switch case for each file we need to process.
				switch(func) {
					case 'inventory':
					tinyshop.getinventory(this.responseText);
					break;
					case 'settings':
					tinyshop.getsettings(this.responseText);
					break;
					case 'shipping':
					tinyshop.getshipping(this.responseText);
					break;
				}
				
			}
		};
	},
 
	fetchJSON: function(uri,callback) {

		var req = tinyshop.xhr();

		req.open("GET", uri, true);
		req.withCredentials = true;
		req.setRequestHeader('Access-Control-Allow-Origin', '*');
		
		req.onreadystatechange = function() {
			if (req.readyState == 4 && req.status == 200) {
				callback(req.responseText);
			}
		}
		req.send(null);
	},
	
	fetchHTML: function(uri, id, method) {

		var req = this.xhr();
		var res = '';
		
		req.open("GET", uri + '&rnd=' + this.rnd(), true);
		req.withCredentials = true;
		req.setRequestHeader('Access-Control-Allow-Origin', '*');

		req.onreadystatechange = function() {
			if (req.readyState == 4 && req.status == 200) {
				this.res = req.responseText;
				tinyshop.dom(id,'html',this.res);
			}
		}
		req.send(null);
	},

	//--> end of tinyshop javascript logic.


	/*
	* Site specific functions
	*/
	
	addtocart: function(productId) {
		this.id = this.math('int',productId,1);
		this.fetchHTML('/cart/' + this.rnd() + '/addtocart/'+this.id+'/', 'GET', 'result');
	},
	
	/*
	* PayPal functions.
	*/
	
	calculateTotalPayPal: function(amount) {

		var price = this.dom('item_price','get');
		var shipping = this.dom('shipping','get');
		var handling = this.dom('handling','get');
		
		var total_amount = this.dom('total_amount','get');
		
		var pre = this.math('int',this.math('int',shipping) + this.math('int',handling));
		var sub_total = this.math('int',price * amount);
		var total = this.math('int',this.math('int',pre) + this.math('int',sub_total));
		
		this.dom('total_amount','set',total);
		
		return true;
	},

	/*
	* Functions to retrieve JSON files. These are called by the caller function.
	* Example: tinyshop.caller('inventory/site.json','settings');
	* This retrieves the site.json file, and prints the object out in html. 
	*/

    getshipping: function(jsonData) {
	
        var arr = [];
	var col = [];
        arr = JSON.parse(jsonData); 
		
        for (var i = 0; i < arr.length; i++) {
            for (var key in arr[i]) {
                if (col.indexOf(key) === -1) {
                    col.push(key);
                }
            }
        }
	for (var i = 0; i < arr.length; i++) {
			
		for (var j = 0; j < col.length; j++) {
				if(arr[i][col[j]] == '' || arr[i][col[j]] == null) {
				} else {
				document.write(col[j] + ':');
				document.write(arr[i][col[j]]);
				document.write('<br>');
			}
		}
	}
	
    },
	
    getsettings: function(jsonData) {
	
        var arr = [];
	var col = [];
        arr = JSON.parse(jsonData); 
		
        for (var i = 0; i < arr.length; i++) {
            for (var key in arr[i]) {
                if (col.indexOf(key) === -1) {
                    col.push(key);
                }
            }
        }
	for (var i = 0; i < arr.length; i++) {
			
		for (var j = 0; j < col.length; j++) {
				if(arr[i][col[j]] == '' || arr[i][col[j]] == null) {
				} else {
				document.write(col[j] + ':');
				document.write(arr[i][col[j]]);
				document.write('<br>');
			}
		}
	}
	
    },
	
    getinventory: function(jsonData) {
	
        var arr = [];
	var col = [];
        arr = JSON.parse(jsonData); 
		
        for (var i = 0; i < arr.length; i++) {
            for (var key in arr[i]) {
                if (col.indexOf(key) === -1) {
                    col.push(key);
                }
            }
        }
	for (var i = 0; i < arr.length; i++) {
			
		for (var j = 0; j < col.length; j++) {
				if(arr[i][col[j]] == '' || arr[i][col[j]] == null) {
				} else {
				document.write(col[j] + ':');
				document.write(arr[i][col[j]]);
				document.write('<br>');
			}
		}
	}
    },
	
    calculatetotal: function(verzendmethode, totaal, parentId) {

	// load site configurations, such as currency selection.
	tinyshop.caller('inventory/site.json','settings');
	tinyshop.caller('inventory/shipping.json','shipping');

	// standaard NL
	// var verznd_gw = '190';
	// var vzdb = '1.90';
	// var totals = parseFloat(totaal) + parseFloat(vzdb);
	// this.dom(parentId,'html',"&euro;" + totals.toFixed(2));
    },

    wishlist: function(method, product, g) {

		var req = this.xhr();

		req.open("GET", '/wishlist/' + this.rnd() + '/' + method + '/' + escape(product) + '&tr=' + g, true);
		
		req.onreadystatechange = function() {

			if (req.readyState == 4 && req.status == 200) {
				var text = req.responseText.split('|');
				if (text[0].replace(' ', '') == 'O') {
					if (g != '0') {
						tinyshop.dom('fhs' + product,'html',text[1]);
						tinyshop.dom('favheart' + product,'className','heartfull_png');
						} else {
						tinyshop.dom('fhs' + product,'html',text[1]);
						tinyshop.dom('favheart' + product,'className','favheart_fixed');
					}
				return false;
				} else if (text[0].replace(' ', '') == 'X') {
					if (g != '0') {
						tinyshop.dom('fhs' + product,'html',text[1]);
						tinyshop.dom('favheart' + product,'className','heart_png');
						} else {
						tinyshop.dom('fhs' + product,'html',text[1]);
						tinyshop.dom('favheart' + product,'className','favheart');
					}
					return false;
					} else {
				return false;
				}
			}
		}
		
		req.send(null);
    },
	
    redeemVoucher: function() {

		var voucher = this.dom('voucher','get');

		if (voucher == '') {
			this.message('Please enter voucher code. This code is a sequence of numbers and letters.');
		} else {
			
			var req = this.xhr();
			req.open("GET", '/query/' + this.rnd() + '/voucher/' + escape(voucher) + '/', true);
			req.onreadystatechange = function() {
				if (req.readyState == 4 && req.status == 200) {

					if (req.responseText) {

						var check = req.responseText.split('|');

						if (check[0].replace(' ', '') == 'OK') {
							
							var tot = this.dom('total','gethtml');
							tot = tot.replace('&euro;', '').replace(/\u20ac/g, '').replace(',', '.').replace(' ', '');
							
							var totals = this.math('float',tot);

							if (check[1] != '') {
								var t = check[1];
								var ta = this.math('float',t);
								var totalsx = (totals - ta);
							} else if (check[2] != '' && check[2] != '|') {
								var totals_sub = (totals / 100 * check[2]);
								var totalsx = (totals - totals_sub);
							} else {}

							if (totals < 0) {
								this.message('The amount is too tow to redeem the voucher.');
							} else {
								if (totalsx.toFixed(2) == 'NaN') {
									this.dom('total','html',"&euro;" + totalsx);
									} else {
									this.dom('total','html',"&euro;" + totalsx.toFixed(2));
								}
							}

						} else if (check[0].replace(' ', '') == 'ERR') {
							this.message('Code has already been redeemed, or is wrong.');
						} else {
							this.message('There was a problem with redeeming the voucher code. Please check if the code is correct.');
						}

					} else {
						this.message('There was a problem with redeeming. Please check if the code is correct.');
					}
				}
			}
			req.send(null);
		}
	}

};
