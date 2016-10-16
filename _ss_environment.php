<?php

/* environment */
define('SS_ENVIRONMENT_TYPE', 'live');

/* database */
define('SS_DATABASE_CLASS', 'MySQLPDODatabase');
define('SS_DATABASE_SERVER', 'mariadb');
define('SS_DATABASE_NAME', $_ENV['MYSQL_DATABASE']);
define('SS_DATABASE_USERNAME', $_ENV['MYSQL_USER']);
define('SS_DATABASE_PASSWORD', $_ENV['MYSQL_PASSWORD']);

define('SS_ERROR_LOG', 'silverstripe.log');

/* default admin username and password */
// define('SS_DEFAULT_ADMIN_USERNAME', 'admin');
// define('SS_DEFAULT_ADMIN_PASSWORD', 'password');

global $_FILE_TO_URL_MAPPING;
$_FILE_TO_URL_MAPPING['/var/www/cms'] = 'http://localhost';
