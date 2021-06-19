# Tiny Shop

A tiny shop written in PHP and flat file JSON. TinyShop works with CVS files, which are converted to JSON. Useful for small webshops that do not receive much web traffic, and for those who do not want to install unwieldy and large pieces of software that have too many features. A shop owner wants to sell a product, and that does not have to be complex. 

Tiny shop will be ideal for clients who make one-time single purchases, handmade or boutique items such as: art prints, antiques, music, jewelry, books and stationary. It is not recommended for mass retail and high volume shops, eventhough it can compete with regards to speed and delivery due to a lean architecture and adequate JSON caching.

Administration: CSV files can be uploaded through the administration folder, which are then automatically converted to JSON flat file database, through which TinyShop functions. An example CSV file which contains all shop products: https://github.com/flaneurette/tiny-shop/blob/master/inventory/csv/shop.csv

# Installing
- Clone or download the zip and upload them to a folder on your server.
- Run install.php in your browser and follow directions.

TinyShop checks all requirements and if satisfied, the package should be installed seamlessly. If not, it will prompt for further action.

# Requirements
- PHP 5.4+ (the higher the better)
- PHP extensions (the installer will check on them and prompt for missing extensions)
- Server module: (Apache) mod_rewrite for .htaccess functionalities. The .htaccess is written dynamically upon installing. By default, a standard .htaccess is present.
- The /shop/ and especially the /administration/ folder needs to be writeable by the server (In Apache for example, the owner should be www-data. If not, it needs to be manually chowned through a terminal.) otherwise, session data and the .htaccess and .htpasswd cannot be written.

The following files need to be writeable by the installer. The installer attempts to chmod the files automatically, if this fails, then file rights need to be manually assigned as follows:

- administration/.htpasswd : 0777
- administration/session.ses : 0777
- administration/.htaccess : 0777
- .htaccess : 0777
- payment/paypal/paypal.json : 0777
- inventory/site.json : 0777

Remember to change permissions back to 0755 after the installer has run. Again, the installer itself tries to do this automatically but it would be wise to check manually. The installer will give a notice if the chmodding fails.
		
# Payment types:

By default, the free version, only accepts PayPal payments (including credit cards). 

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

# Products
The file shop.csv|.json, contains all the products. All products require to have a unique product identifier, or productId. This is numeric, for example: 10000234. It is advised to have a large product identifier, in this way one can add more products. Without productId, or duplicate productIds, the shop might not work properly.

To place a product inside a category or subcategory, the subsequent csv files need to be edited or viewed to obtain the categoryId or subcategoryId. In this way, products are linked. As an example, TinyShop has a basic list of demo products and (sub) categories, which makes it easy to see how TinyShop works. 

- Future of product modification.
In a future version of TinyShop, all CSV files will be linked into a single Excel document, making it even more practical. In this way the whole shop can be modified from a single excel document.

# Style and themes.

To change the colors and thematical style of the shop these CSS files can be modified:

Two main stylesheets for the webshop:
- /resources/css.css
- /resources/style.css

Pages stylesheet for articles, pages and blogs:
- /resources/pages.css

Administration stylesheet:
- /resources/admin.css

Reset stylesheet, needs not to be modified:
- /resources/reset.css


# CSV & JSON files of interest

Most CSV and JSON files can be edited, and it is advised to do so for TinyShop to work properly. A few are listed below that are required to be edited:

- Site.csv|.json

This file contains site wide settings, such as meta tags, logo, javascripts and stylesheets.

- Navigation.csv|.json

TinyShop has a preset navigation which can be changed according to one's wishes. Currently, TinyShop does not support dropdown navigation only a horizontal navigation bar. This might change in future versions. Navigation supports relative paths only. 

- Shop.csv|.json

This file contains all shop products. By default, preset shop products are loaded and displayed.

- Categories.csv|.json

This file contains the categories that are loaded in the left-side navigation bar.

- Subcategories.csv|.json

This file contains the subcategories that are loaded in the left-side navigation bar and displayed under each particular category.

- payment/paypal/paypal.csv|.json

This file contains the PayPal information. Upon installation, the PayPal e-mailadress will be asked and is written to this file. Further information could be changed manually, such as return pages and cancellation pages.

- Currencies.csv|.json

This file contains all currencies, normally this file needs not to be changed.

- Articles.csv|.json

This file contains all the articles, if written and displayed under the navigation of articles.

- Blog.csv|.json

This file contains all the weblogs, if written and displayed under the navigation of weblog or blog.


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

