# Installing

TinyShop's installer will create a .htpasswd automatically, plus it restricts the admin by IP. However, it could be possible that there aren't enough rights to write a .htpasswd automatically due to server or security settings. If this is the case, then you could manually create the .htpasswd as per instructions below. To generate a secure password, use a webtool like: https://www.transip.nl/htpasswd/ 

# Password protected area.

It uses a .htaccess and Apache basic authentication to access the folder. It is restricted by IP.

A .htpasswd needs to be generated and placed below the /www/ folder. (However, be aware that this is not always possible due various server security settings.) By default, TinyShop writes it directly into the /administration/ folder for fail-safe reasons. The installer is not able to write below the /www/ folder, if you require better security, the .htpasswd needs to be created or moved manually.

Example: /home/path/.htpasswd

Example Value: admin:HhSp99034

# Ip config

Add your IP:

Allow from 111.222.333.444


