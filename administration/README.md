# Password protected area.

Tinyshop uses a .htaccess and Apache basic authentication to access the administration folder. It is also restricted by IP.

A htpasswd needs to be generated and placed below the /www/ folder.

Example: /home/path/.htpasswd

Example Value: admin:HhSp99034

# Ip config

Add your IP:

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

