/*
 * Main.js Tiny Shop custom javascript. For external javascript, edit site.json and add the uri.
*/

function calculateTotalPayPal(amount) {
	var price = document.getElementById('item_price').value;
	var shipping = document.getElementById('shipping').value;
	var handling  = document.getElementById('handling').value;
	var total_amount = document.getElementById('total_amount').value; 
	var pre = parseInt(parseInt(shipping) + parseInt(handling));
	var sub_total = parseInt(price * amount);
	var total = parseInt(parseInt(pre) + parseInt(sub_total));
	document.getElementById('total_amount').value = total;
	return true;
}
