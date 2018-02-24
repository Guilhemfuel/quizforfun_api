#php bin/console server:run
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load
php -d memory_limit=-1 composer.phar

parameters:
    database_host: 127.0.0.1
    database_port: null
    database_name: api
    database_user: root
    database_password: null
    mailer_transport: smtp
    mailer_host: 127.0.0.1
    mailer_user: null
    mailer_password: null
    secret: ge687gfezf6uty68a

parameters:
    database_host: localhost
    database_port: 3306
    database_name: u142109db1
    database_user: u142109db1
    database_password: ***
    mailer_transport: smtp
    mailer_host: 127.0.0.1
    mailer_user: null
    mailer_password: null
    secret: ge687gfezf6uty68a