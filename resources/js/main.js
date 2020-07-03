/*
 * Main.js Tiny Shop custom javascript. For external javascript, edit site.json and add the uri.
 */
 
var tinyshop = {

	// vars
	name: "tinyshop javascript library",
	version: "1.1",
	instanceid: 1000000,
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

	addtocart: function(productId) {

		this.id = parseInt(productId);
		this.fetchHTML('/cart/' + Math.random() + '/addtocart/'+this.id+'/', 'GET', 'result');
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

	calculateTotalPayPal: function(amount) {

		var price = this.dom('item_price','get');
		var shipping = this.dom('shipping','get');
		var handling = this.dom('handling','get');
		var total_amount = this.dom('total_amount','get');
		
		var pre = parseInt(parseInt(shipping) + parseInt(handling));
		var sub_total = parseInt(price * amount);
		var total = parseInt(parseInt(pre) + parseInt(sub_total));
		
		this.dom('total_amount','set',total);
		
		return true;
	},

	fetchJSON: function(uri) {

		var req = this.xhr();
		var res = '';
		req.open("GET", uri, true);
		req.withCredentials = true;
		req.setRequestHeader('Access-Control-Allow-Origin', '*');

		req.onreadystatechange = function() {
			if (req.readyState == 4 && req.status == 200) {
				this.res = req.responseText;
				tinyshop.dom('result','html',this.res);
			}
		}
		
		req.send(null);
		return res;
	},
	
	fetchHTML: function(uri, id, method) {

		var req = this.xhr();

		var g = null;
		var product = null;
		var res = '';

		req.open("GET", uri + '&rnd=' + Math.random(), true);
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

	wishlist: function(method, product, g) {

		var req = this.xhr();

		req.open("GET", '/wishlist/' + Math.random() + '/' + method + '/' + escape(product) + '&tr=' + g, true);
		
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

		//document.location = location.href;
	},

	calculatetotal: function(verzendmethode, totaal, parentId) {

		var req = this.xhr();

		// Our JSOn object with shipping values.
		var shipping = this.fetchJSON('../shop/inventory/shipping.json');
		
		// window.alert(shipping);

		// standaard NL
		var verznd_gw = '190';
		var vzdb = '1.90';
		
		var totals = parseFloat(totaal) + parseFloat(vzdb);
		
		this.dom(parentId,'html',"&euro;" + totals.toFixed(2));
	},

	redeemVoucher: function() {

		var voucher = this.dom('voucher','get');

		if (voucher == '') {
			alert('Please enter voucher code. This code is a sequence of numbers and letters.');
		} else {
			
			var req = this.xhr();
			req.open("GET", '/query/' + Math.random() + '/voucher/' + escape(voucher) + '/', true);
			req.onreadystatechange = function() {
				if (req.readyState == 4 && req.status == 200) {

					if (req.responseText) {

						var check = req.responseText.split('|');

						if (check[0].replace(' ', '') == 'OK') {
							
							var tot = this.dom('total','gethtml');
							
							tot = tot.replace('&euro;', '').replace(/\u20ac/g, '').replace(',', '.').replace(' ', '');

							var totals = parseFloat(tot);

							if (check[1] != '') {
								var t = check[1];
								var ta = parseFloat(t);
								var totalsx = (totals - ta);
							} else if (check[2] != '' && check[2] != '|') {
								var totals_sub = (totals / 100 * check[2]);
								var totalsx = (totals - totals_sub);
							} else {}

							if (totals < 0) {
								alert('The amount is too tow to redeem the voucher.');
							} else {
								if (totalsx.toFixed(2) == 'NaN') {
									this.dom('total','html',"&euro;" + totalsx);
									} else {
									this.dom('total','html',"&euro;" + totalsx.toFixed(2));
								}
							}

						} else if (check[0].replace(' ', '') == 'ERR') {
							alert('Code has already been redeemed, or is wrong.');
						} else {
							alert('There was a problem with redeeming the voucher code. Please check if the code is correct.');
						}

					} else {
						alert('There was a problem with redeeming. Please check if the code is correct.');
					}
				}
			}
			req.send(null);
		}

	}

};
