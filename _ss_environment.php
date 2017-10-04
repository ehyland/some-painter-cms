<?php

/* environment */
define('SS_ENVIRONMENT_TYPE', getenv('SS_ENVIRONMENT_TYPE'));

/* database */
define('SS_DATABASE_CLASS', 'MySQLPDODatabase');
define('SS_DATABASE_SERVER', getenv('SS_DATABASE_SERVER'));
define('SS_DATABASE_NAME', getenv('SS_DATABASE_NAME'));
define('SS_DATABASE_USERNAME', getenv('SS_DATABASE_USERNAME'));
define('SS_DATABASE_PASSWORD', getenv('SS_DATABASE_PASSWORD'));

define('SS_ERROR_LOG', 'silverstripe.log');

/* default admin username and password */
// define('SS_DEFAULT_ADMIN_USERNAME', 'admin');
// define('SS_DEFAULT_ADMIN_PASSWORD', 'password');

global $_FILE_TO_URL_MAPPING;
$_FILE_TO_URL_MAPPING['/site'] = 'http://localhost';
