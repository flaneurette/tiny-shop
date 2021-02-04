	

# Optional headers to set. 

Use at your own discretion, as many of them might impact functionality. The tradeoff for more security, is less freedom.

# Custom header:

```
header("HeaderName: HeaderValue");
```
	 
# Prevent caching:
```
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
```
	 
# Content security policy: 
These are quite strict, and may break functionaility. Use at your own discretion.

```
header("Content-Security-Policy: max-age=30");
header("Content-Security-Policy: default-src 'self'; script-src example.com 'nonce-".uniqid()."'; frame-src 'self'; style-src 'self'; img-src 'self';");
header("Content-Security-Policy: script-src 'unsafe-inline' ; style-src 'unsafe-inline' ");
header("HTTP Strict Transport Security: max-age=31536000 ; includeSubDomains");
header("Public-Key-Pins: pin-sha256="d6qzRu9zOECb90Uez27xWltNsj0e1Md7GkYYkVoZWmM="; pin-sha256="E9CZ9INDbd+2eRQozYqqbQ2yXLVKB9+xcprMF+44U1g="; report-uri="http://example.com/pkp-report"; max-age=10000; includeSubDomains");
header("X-Content-Type-Options: nosniff"); 
header("Referrer-Policy: no-referrer");
header("Expect-CT: max-age=86400, enforce, report-uri="https://foo.example/report"");
header("Feature-Policy: vibrate 'none'; geolocation 'none'");
```
	 
