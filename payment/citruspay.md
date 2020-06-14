# Development guide for: Citrus Payment Gateway.

```

<script src="https://checkout-static.citruspay.com/lib/js/jquery.min.js"></script>

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no"/>

<script id="context" type="text/javascript" src="https://sboxcheckout-static.citruspay.com/kiwi/app-js/icp.js"></script>
<script id="context" type="text/javascript" src="https://checkout-static.citruspay.com/kiwi/app-js/icp.min.js"></script>

<script>

// citrusICP.launchIcp(dataObj, configObj);

var dataObj = {
	orderAmount: "1.00",
	currency: "POUND",
	email: "someone@validemail.com",
	phoneNumber: "9234567890",
	merchantTxnId: "Order-314921",
	returnUrl: "http://www.abc.com/return-url",
	vanityUrl: "YourVanity",
	secSignature: "d32r24e2daewee13sd341rsefs34wqwe3q1qe31d",
	firstName: "Jane",
	lastName: "Doe",
	addressStreet1: "Connaught Place",
	addressStreet2: "Street Number 20",
	addressCity: "London",
	addressState: "London",
	addressCountry: "England",
	addressZip: "400605",
	notifyUrl: "https://www.abc.com/notify-me",
	mode: "dropIn"
};

configObj = {
	eventHandler: function(cbObj) {
		if (cbObj.event === 'icpLaunched') {
			console.log('Overlay is launched');
			// Place to understand when overlay is launched on your website
		} else if (cbObj.event === 'icpClosed') {
			// Place to understand when overlay is closed on your website. 
		  	// This might be closure of the overlay or completion of the order with successful payment.
		  	// Hence capturing message object is important to know exact status of the order
			console.log(JSON.stringify(cbObj.message));
			console.log('Overlay is closed');
		}
	}
};
</script>

<form align="center" method="post">
<input type="hidden" id="merchantTxnId" name="merchantTxnId" value="<%=merchantTxnId%>" />
<input type="hidden" id="amount" name="orderAmount" value="<%=orderAmount%>" />
<input type="hidden" id="currency" name="currency" value="INR" />
<input type="text" name="email" value="testmerchant@mailnator.com" />
<input type="text" name="mobile" value="8527395492" />
<input type="hidden" id="securitySignature" name="secSignature" value="<%=securitySignature%>" />
</form>
<input type="Submit" value="Pay Now" id="launchICP"/>

```
Return data if case of exception:
```

try {
citrusICP.launchIcp({
    orderAmount: 'asdf',
    currency: 'POUND',
    email: 'nikhil',
    phoneNumber: 'asfa9923454040',
    returnUrl: 'ddfdf',
    merchantTxnId: 1111,
    //vanityUrl: “sdfassafsdf”,
    //secSignature: 'aslkjfslaf',
});
}
catch(error) {
   console.log(error);
}
```
