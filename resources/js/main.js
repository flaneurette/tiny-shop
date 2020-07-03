/*
 * Main.js Tiny Shop custom javascript. For external javascript, edit site.json and add the uri.
 */
 
var tinyshop = {

	// vars
	name: "tinyshop javascript library",
	version: "1.0",
	instanceid: 1000011,
	csp: ["Access-Control-Allow-Origin","*"];

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

	toggle: function(id, counter) {
		for (i = 0; i < counter; i++) {
			try {
				document.getElementById('toggle' + i).style.display = 'none';
				document.getElementById('cat' + id).style.fontWeight = '100';
			} catch (e) {}
		}
		document.getElementById('toggle' + id).style.display = 'block';
		document.getElementById('cat' + id).style.fontWeight = 'bold';
	},

	calculateTotalPayPal: function(amount) {

		var price = document.getElementById('item_price').value;
		var shipping = document.getElementById('shipping').value;
		var handling = document.getElementById('handling').value;
		var total_amount = document.getElementById('total_amount').value;
		var pre = parseInt(parseInt(shipping) + parseInt(handling));
		var sub_total = parseInt(price * amount);
		var total = parseInt(parseInt(pre) + parseInt(sub_total));
		document.getElementById('total_amount').value = total;
		return true;

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
				document.getElementById(id).innerHTML = this.res;
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
						document.getElementById('fhs' + product).innerHTML = text[1];
						document.getElementById('favheart' + product).className = 'heartfull_png';
						} else {
						document.getElementById('fhs' + product).innerHTML = text[1];
						document.getElementById('favheart' + product).className = 'favheart_fixed';
					}

					return false;

				} else if (text[0].replace(' ', '') == 'X') {

					if (g != '0') {
						document.getElementById('fhs' + product).innerHTML = text[1];
						document.getElementById('favheart' + product).className = 'heart_png';
					} else {
						document.getElementById('fhs' + product).innerHTML = text[1];
						document.getElementById('favheart' + product).className = 'favheart';
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

		switch (verzendmethode) {

			case '1':
				// standaard NL
				var verznd_gw = '190';
				var vzdb = '1.90';
				break;

			case '2':
				// aangetekend NL
				var verznd_gw = '750';
				var vzdb = '7.50';
				break;

			case '3':
				// Europe not insured
				var verznd_gw = '300';
				var vzdb = '3.00';
				break;

			case '4':
				// Europe (Track & Trace)
				var verznd_gw = '950';
				var vzdb = '9.50';
				break;

			case '5':
				// Gratis
				var verznd_gw = '0';
				var vzdb = '0';
				break;

			default:
				// standaard NL
				var verznd_gw = '190';
				var vzdb = '1.90';
				break;
		}

		var totals = parseFloat(totaal) + parseFloat(vzdb);
		document.getElementById(parentId).innerHTML = "&euro;" + totals.toFixed(2);
	},

	redeemVoucher: function() {

		var voucher = document.getElementById('voucher').value;

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
							var tot = document.getElementById('total').innerHTML;
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
									document.getElementById('total').innerHTML = "&euro;" + totalsx;
								} else {
									document.getElementById('total').innerHTML = "&euro;" + totalsx.toFixed(2);
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
