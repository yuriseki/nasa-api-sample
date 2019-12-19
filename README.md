# Installation Instructions

## Considering you're using Ubuntu and LAMP Server.

1. Download the project into the web server folder ex.`/var/www/html/uaf`.
2. Set the permissions for the `files` folder accordingly to the web server 
you're using.
```bash
sudo mkdir /var/www/html/uaf/docroot/sites/default/files
sudo chown www-data:$USER /var/www/html/uaf/docroot/sites/default/files -R
```
3. Create the site config file on apache.
```bash
sudo nano /etc/apache2/sites-available/dev.uaf.conf
```
4. Paste the following content on `dev.uaf.conf` file
```
<VirtualHost *:80>
    ServerName dev.uaf
    
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html/uaf/docroot
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

</VirtualHost>
```
5. Activate the site.
```bash
sudo a2ensite dev.uaf.conf
sudo service apache2 restart
```
6. Register the UAF site on the `hosts` file by adding `127.0.0.1 dev.uaf` at 
the end of the file.
```bash
sudo nano /etc/hosts 
```
7. Create a database for the UAF site.
```bash
mysql -u root -p
```
```mysql
CREATE SCHEMA `uaf` DEFAULT CHARACTER SET latin1;
CREATE USER 'uaf'@'localhost' IDENTIFIED BY 'YOUR PASSWORD';
GRANT ALL PRIVILEGES ON uaf.* TO 'uaf'@'localhost';

exit;
```

8. Install the Drupal site using drush.
```bash
cd /var/www/html/uaf/docroot
sudo cp sites/default/default.settings.php sites/default/settings.php
sudo chown www-data:$USER sites/default/settings.php
drush si standard --db-url=mysql://uaf:'YOUR PASSWORD'@localhost/uaf
```
Take notes of your username and password.


9. Adjust the config folder on `dorcroot/default/settings.php`
```bash
nano docroot/sites/default/settings.php
```

Replace the last line (Something like:) 
```
$config_directories['sync'] = 'sites/default/files/config_tkFzoCY46CuHdBVEcgWs7LI9ZSMoqEH_DtzGjcRpKCG6euyT_QVwRoNEel0JzO91surASw5nWA/sync';
```
by the following:
```
$config_directories = [
  CONFIG_SYNC_DIRECTORY => '../config/default',
];
```

10. Change your site UUID to be able to import the configuration.
```bash
drush config-set "system.site" uuid "653059e0-5618-4625-9969-ba298fa6acf5"
```

11. Import the site configuration.
```bash
drush sqlq "truncate table shortcut"
drush cim
```

12. Load the exoplanets data from NASA (Optional at this moment).
```bash
drush cc drush
drush cr
drush import-exoplanets
```

13. Access the UAF local website using your browser.
```
http://dev.uaf
```
