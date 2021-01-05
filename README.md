# Tiny Shop

N.B. Under construction. 
*december 2020: a beta demo version is ready. The Shop is workable and accepts PayPal payments. Blog, Articles and Themes are not fully functioning yet.*

A tiny shop written in PHP and flat file JSON. Useful for small webshops that do not receive much web traffic, and for those who do not want to install unwieldy and large pieces of software that have too many features. A shop owner wants to sell a product, and that does not have to be complex.

Tiny shop will be ideal for clients who make one-time single purchases, handmade or boutique items such as: art prints, antiques, music, jewelry, books and stationary. It is not recommended for mass retail and high volume shops, eventhough it compete with regards to speed and delivery.

# Installing
- Clone or download the zip and upload them to a folder on your server. TinyShop expects that folder to be named /shop/. If you do want another path, it requires manual editing of the configuration file. 
- Run install.php in your browser.

TinyShop checks all requirements and if satisfied, the package should be installed seamlessly. If not, it will prompt for further action.

# Requirements
- PHP 5.4+ (the higher the better)
- PHP extensions (the installer will check on them and prompt for missing extensions)
- The /shop/ folder needs to be writeable by the server (In Apache for example, the owner should be www-data. If not, it needs to be manually chowned through a terminal.)

# Payment types:

By default, the free version, only accepts PayPal payments. 

Long term planned integration (with a future paid plan) will support more Payment Gateways:

Bancontact, KNET, CitrusPay, Mada, QPAY, EPS, Giropay, iDEAL, Bitcoin: Coingate, Poli, Przelewy24, Sofort, Boleto Bancário, Fawry, Multibanco, OXXO, Pago Fácil, Rapipago, Via Baloto, ACH, SEPA Direct Debit, Klarna, Bancontact, KNET, Mada, QPAY, Stripe, Alipay, Apple Pay, BenefitPay, Google Pay and PayPal.


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

# Product list minimal demo:

- Function: getproducts() takes only two params: method & category. 
- Method: either list or group. 
- Category: the shop category. If empty, it shows all categories.

```
include("resources/php/header.inc.php");
include("class.Shop.php");
	
$shop     = new Shop();
$products = $shop->getproducts('list','index');
  
echo $products;
```
# Loading a JSON file, and get all keys and values:
```
include("resources/php/header.inc.php");
include("class.Shop.php");

$shop     = new Shop();

$shopconf = $shop->load_json("inventory/shop.conf.json");

foreach($shopconf as $row)
{
	foreach($row as $key => $value)
	{
	echo "<b>".$key."</b>".':'.$value.'<br>';
	}
}
	
```
	
# Product list demo:

![Image of Product list](https://raw.githubusercontent.com/flaneurette/tiny-shop/master/resources/images/product-screen.png)

# JSON values and parameters:

An upload page is used to convert each CSV to JSON. In this way, only the CSV files have to be edited and the shop will be updated automatically. Obviously, it is also possible to upload each JSON and CSV file through either SCP, FTP or command line, rendering the upload page expendable. 

```
site.url 
site.domain 
site.canonical 
site.cdn 
site.charset utf-8
site.title 
site.description 
site.logo
site.icon 
site.status 
site.updated 
site.meta.title 
site.meta.description 
site.meta.tags 
site.meta.name.1 
site.meta.name.2 
site.meta.name.3 
site.meta.name.4 
site.meta.value.1 
site.meta.value.2 
site.meta.value.3 
site.meta.value.4 
site.tags 
site.socialmedia.option1 
site.socialmedia.option2 
site.socialmedia.option3 
site.socialmedia.option4 
site.socialmedia.option5 
site.javascript 
site.ext.javascript 
site.stylesheet 
site.ext.stylesheet 
site.google.tags 
site.cookie.name.1 
site.cookie.name.2 
site.cookie.name.3 
site.cookie.value.1 
site.cookie.value.2 
site.cookie.value.3 
site.analytics 
site.payment.gateways
```
# Shop.conf 
```
products.orientation: thumb|list
products.alt.tags
products.scene.type
products.row.count
products.per.page
products.per.cat
products.quick.cart
products.carousel
```
# Products 
```
product.id
product.status 
product.title 
product.description 
product.category 
product.catno 
product.stock 
product.quantity 
product.format 
product.type 
product.weight 
product.condition 
product.ean 
product.sku 
product.vendor 
product.price 
product.margin 
product.price.min 
product.price.max 
product.price.varies 
product.date 
product.url 
product.image 
product.tags 
product.images 
product.featured 
product.featured.location 
product.featured.carousel 
product.featured.image 
product.content 
product.variants 
product.available 
product.selected.variant 
product.collections 
product.options 
socialmedia.option1 
socialmedia.option2 
socialmedia.option3 
variant.title1 
variant.title2 
variant.title3 
variant.image1 
variant.image2 
variant.image3 
variant.option1 
variant.option2 
variant.option3 
variant.price1 
variant.price2 
variant.price3 
shipping
shipping.fixed.price 
shipping.flatfee 
shipping.locations 
payment.paypal.button.id 
payment.payment.button1 
payment.payment.button2 
payment.payment.button3 
payment.payment.array  
```
# Pages:
```
page.id
page.title
page.description
page.short.text
page.long.text
page.url
page.tags
page.image.header
page.image.main
page.image.left
page.image.right
page.status
page.archived
page.created
page.published
page.updated
page.meta.title
page.meta.description
page.meta.tags
```
# Cart:
```
cart.id
cart.customer.id
cart.creation.date
cart.data
cart.sum
cart.tax
cart.product.list
cart.checkout.status
cart.checkout.discount
cart.session.id
cart.session.attempts
cart.diff
```
# Orders:
```
orders.id 
orders.customer.id 
orders.product.list 
orders.creation.date 
orders.data 
orders.sum 
orders.tax 	
orders.customer.email 
orders.delivered 
orders.refunded 
orders.discount 
orders.voucher 
orders.checkout.method 
orders.checkout.payment 
orders.checkout.status 
orders.checkout.discount 
orders.checkout.success 
orders.session.id 
orders.session.ip 
orders.session.ua 
orders.session.attempts 
orders.diff 
```
# Customer:
```
customer.id
customer.attn
customer.first.name
customer.last.name
customer.address
customer.address.number
customer.postalcode
customer.region
customer.city
customer.country
customer.password
customer.hash
customer.email
customer.newsletter
customer.signup.date
customer.signup.ip
customer.signup.ua
customer.diff
```
# Blog:
```
blog.id
blog.title
blog.description
blog.short.text
blog.long.text
blog.url
blog.tags
blog.author
blog.handle
blog.created
blog.published
blog.image.header
blog.image.main
blog.status
blog.archived
```   
# Articles:
``` 
article.id
article.title
article.description
article.short.text
article.long.text
article.url
article.tags
article.author
article.handle
article.created
article.published
article.image.header
article.image.main
article.status
article.archived
```

