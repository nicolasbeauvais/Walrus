VHOST :

<<<<<<< HEAD
  $ sudo nano /etc/apache2/sites-available/walrus
=======
  $ sudo nano /etc/apache2/sites-available/walrus.dev
>>>>>>> develop


	<VirtualHost *:80>
                ServerAdmin webmaster@localhost
<<<<<<< HEAD

                ServerName walrus
=======
                ServerName walrus.dev
                ServerAlias www.walrus.dev
>>>>>>> develop

                DocumentRoot /var/www/Walrus
                <Directory /var/www/Walrus>
                        Options Indexes FollowSymLinks MultiViews
                        AllowOverride All
                        Order allow,deny
                        allow from all
                </Directory>

<<<<<<< HEAD
                # Chroot PHP script to this path
                php_admin_value open_basedir "/var/www/Walrus"
                # Tmp upload directory
                php_admin_value upload_tmp_dir "/var/www/Walrus/tmp"

=======
>>>>>>> develop
                ScriptAlias /cgi-bin/ /usr/lib/cgi-bin/

                ErrorLog ${APACHE_LOG_DIR}/error.log

                # Possible values include: debug, info, notice, warn, error, crit,
                # alert, emerg.
                LogLevel warn

                CustomLog ${APACHE_LOG_DIR}/access.log combined
        </VirtualHost>

___

<<<<<<< HEAD
	$ sudo a2ensite walrus
=======
	$ sudo a2ensite walrus.dev
>>>>>>> develop
	$ sudo service apache2 reload

___

Host to add (Windows ou Mac) AND VM :

    127.0.0.1       walrus.dev

path VM :

<<<<<<< HEAD
    sudo nano /etc/hosts
=======
    sudo nano /etc/hosts
>>>>>>> develop
