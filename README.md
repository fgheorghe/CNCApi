CNC Manufacturing API

###Install Instructions

NOTE: This application must be run on a private network - there is no client authentication.

Configure PHP timezone (/etc/php5/cli/php.ini or /etc/php5/apache2/php.ini):

date.timezone=Europe/London

Run the Symfony 3 health check script:

php cnc-api/app/symfony_requirements 

Install composer (https://www.digitalocean.com/community/tutorials/how-to-install-and-use-composer-on-ubuntu-14-04):

curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

###Configuration

Edit cnc-api/app/config/parameters.yml

###Usage

As per Symfony 3 documentation, issue:

cd cnc-api && php bin/console server:run 0.0.0.0:8000

Or to start as a background process:

cd cnc-api && nohup php bin/console server:run 0.0.0.0:8000 &

Browse:

http://localhost:8000

###Notes:

Versioning is stored in: app/config/version.yml

###Makes use of:

https://github.com/nelmio/NelmioApiDocBundle