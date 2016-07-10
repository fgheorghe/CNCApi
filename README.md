CNC Manufacturing API

###Install Instructions

NOTE: This application must be run on a private network - there is no client authentication.

Configure PHP timezone (/etc/php5/cli/php.ini or /etc/php5/apache2/php.ini):

date.timezone=Europe/London

Run the Symfony 2 health check script:

php cnc-api/app/check.php

Install composer (https://www.digitalocean.com/community/tutorials/how-to-install-and-use-composer-on-ubuntu-14-04):

curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

###Configuration

Edit cnc-api/app/config/parameters.yml

###Usage

As per Symfony 2 documentation, issue:

cd cnc-api && php app/console server:run

Browse:

http://localhost:8000

###Notes:

Versioning is stored in: app/config/version.yml