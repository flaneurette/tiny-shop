# Password protected area.

Tinyshop is entirely based upon CSV and JSON files. Since Tinyshop does not have an elaborate administration area, it uses a .htaccess and Apache basic authentication to access the administration folder. The administration folder contains a development folder, and a few upload pages to convert CSV to JSON. Personally, I upload these files with my own SCP software and Secure Copy the data. This administration page is therefore entirely optional.

A htpasswd needs to be generated and placed below the /www/ folder.

Example: /home/path/.htpasswd

Example Value: admin:HhSp99034

# Ip config
It is possible to restrict it by IP.

To Add your IP:

Allow from 111.222.333.444

# SSL
```
Require ssl
Require ssl-verify-client
Require valid-user
```

# Documentation

https://httpd.apache.org/docs/2.4/howto/auth.html

https://httpd.apache.org/docs/2.4/mod/mod_ssl.html

