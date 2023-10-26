# User_Management
Getestet in der PHP-Version 7.3.1

1. Gehe in dein PHP instalationsverzeichnis
2. Öffne die php.ini Datei
3. Suche nach dem eintrag ";extension=ldap"
4. Entferne das ";" vor ";extension=ldap" um ldap zu aktivieren
5. Im Projektverzeichnis die ".env.example" in ".env" umbenennen
6. Die ".env" an die Vorhandene Infrastruktur anpassen.
7. CMD im Projektverzeichnis öffnen und "composer up" ausführen
8. Im Verzeichnis xampp\apache\conf\extra die "httpd-vhosts.conf" anpassen
   "
<VirtualHost *:80>
  DocumentRoot "%Projekt-Verzeichnis%"
  <Directory "%Projekt-Verzeichnis%">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
  </Directory>
</VirtualHost>

<VirtualHost *:443>
	DocumentRoot "%Projekt-Verzeichnis%"
	SSLEngine on
	SSLCertificateFile "conf/ssl.crt/server.crt"
	SSLCertificateKeyFile "conf/ssl.key/server.key"
	<Directory "%Projekt-Verzeichnis%">
	Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
	</Directory>
</VirtualHost>
"
9. Apache- und MySQL-Server starten

Tested in PHP version 7.3.1

1. go to your PHP installation directory
2. open the php.ini file
3. search for the entry ";extension=ldap"
4. remove the ";" before ";extension=ldap" to activate ldap
5. rename the ".env.example" to ".env" in the project directory
6. Adjust ".env" to fit the existing infrastructure.
7. open CMD in the project directory and execute "composer up
8. in the directory xampp\apache\conf\extra adapt the "httpd-vhosts.conf".
   "
<VirtualHost *:80>
  DocumentRoot "%Project directory%"
  <Directory "%Project directory%">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
  </Directory>
</VirtualHost>

<VirtualHost *:443>
	DocumentRoot "%Project directory%"
	SSLEngine on
	SSLCertificateFile "conf/ssl.crt/server.crt"
	SSLCertificateKeyFile "conf/ssl.key/server.key"
	<Directory "%Project directory%">
	Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
	</Directory>
</VirtualHost>
"
9. Starting the Apache and MySQL Servers
