# Tiny Shop

*under construction*

A tiny shop written in PHP and flat file JSON. Useful for small webshops that do not receive much web traffic, and for those who do not want to install unwieldy and large pieces of software that have too many features. A shop owner wants to sell a product, and that does not have to be complex.

Tiny shop will be ideal for clients who make one-time single purchases, or who buy boutique items. It is not recommended for mass retail.

# Payment types:
- Paypal.
- Bank.
- Optional: add your own payment processor or gateway.

# Storage
Tiny shop uses JSON to store data. The benefit of a flat file database, is that it works on all platforms and operating systems, and there is no need to install database software.

# JSON values and params example:

```
{
   "id": 100002,
   "product": "Bella Makeup",
   "title": "Makeup powder",
   "description": "Makeup powder in 12 colors.",
   "category": "Powders",
   "image": "images/.png",
   "catno": "M00002",
   "quantity": 2,
   "stock": 100,
   "EAN": "",
   "format": "Box",
   "price": 10,
   "datetime": "2019-04-09",
   "condition": "new",
   "weight": 55,
   "shipping": 0,
   "status": 1
}
```
# Status
N.B. Under construction. It currenly only reads the JSON product list. Adding and editing is planned.
