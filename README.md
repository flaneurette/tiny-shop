# Tiny Shop

*under construction: not for production!*

A tiny shop written in PHP and flat file JSON. Useful for small webshops that do not receive much web traffic, and for those who do not want to install unwieldy and large pieces of software that have too many features. A shop owner wants to sell a product, and that does not have to be complex.

Tiny shop will be ideal for clients who make one-time single purchases, handmade or boutique items such as: art prints, antiques, music, jewelry, books and stationary. It is not recommended for mass retail and high volume shops.

# Payment types:
- Paypal.
- Bank.
- Optional: add your own payment processor or gateway.

- PayPal: the shop owner can add a Paypal "button code" for each product.
- Bank: this could be checking or manual payment.
- Optional: a session container with array data can be send to a 3rd party payment processor.

# Currencies
Tiny Shop supports 36 different currencies, including Bitcoin.

# Storage
Tiny shop uses JSON to store data. The benefit of a flat file database, is that it works on all platforms and operating systems, and there is no need to install database software. JSON can be easely converted back and forth into CSV and excel, making it easy for a shop owner to update the shop, without having to login into a complex portal or a server-side administration screen. 

# Encryption
Tiny shop has a reasonably safe encryption method to encrypt the shop data, namely AES 256. Since it does not store user-details, the encryption is disabled by default. All user details are not stored, but e-mailed to the shop owner. It is possible to store the details and thus encrypt it through Tiny Shop, but that is up to the shop owner.

# Backups
Tiny Shop makes (real-time) automatic backups of the JSON database each time a product is added, changed or removed.

# SEO
Tiny Shop creates SEO friendly URL's of all products.

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
   "paypal_hosted_button_id": DHSO0FUE9J2,
   "datetime": "2019-04-09",
   "condition": "new",
   "weight": 55,
   "shipping": 0,
   "status": 1
}
```
# Status
N.B. Under construction. It currenly only reads the JSON product list. Adding and editing is planned.
