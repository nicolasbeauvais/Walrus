VHOST :

  $ sudo nano /etc/apache2/sites-available/walrus


	<VirtualHost *:80>
                ServerAdmin webmaster@localhost
                ServerName walrus

                DocumentRoot /var/www/Walrus
                <Directory /var/www/Walrus>
                        Options Indexes FollowSymLinks MultiViews
                        AllowOverride All
                        Order allow,deny
                        allow from all
                </Directory>
                
                # Chroot PHP script to this path
                php_admin_value open_basedir "/var/www/Walrus"
                # Tmp upload directory
                php_admin_value upload_tmp_dir "/var/www/Walrus/tmp"

                ScriptAlias /cgi-bin/ /usr/lib/cgi-bin/

                ErrorLog ${APACHE_LOG_DIR}/error.log

                # Possible values include: debug, info, notice, warn, error, crit,
                # alert, emerg.
                LogLevel warn

                CustomLog ${APACHE_LOG_DIR}/access.log combined
        </VirtualHost>

___

	$ sudo a2ensite walrus
	$ sudo service apache2 reload

___

Host to add (Windows ou Mac) AND VM :

    127.0.0.1       walrus

path VM :

    sudo nano /etc/hosts
