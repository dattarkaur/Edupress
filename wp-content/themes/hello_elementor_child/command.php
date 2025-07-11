

sudo /etc/init.d/apache2 stop
sudo /etc/init.d/apache2 start 




sudo apt-get purge apache2 apache2-bin apache2-utils apache2-data
sudo apt-get autoremove
sudo apt-get update
sudo apt-get install apache2
sudo apt-get install php libapache2-mod-php
sudo systemctl restart apache2
