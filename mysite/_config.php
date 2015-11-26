<?php

global $project;
$project = 'mysite';

global $database;
$database = 'somepainter';

require_once('conf/ConfigureFromEnv.php');

// Set timezone
date_default_timezone_set('Australia/Melbourne');

// Set the site locale
i18n::set_locale('en_AU');

//Log notices
if(defined('MY_SS_ERROR_LOG')) {
  SS_Log::add_writer(new SS_LogFileWriter(MY_SS_ERROR_LOG), SS_Log::NOTICE, '<=');
}

// Configure cache
$liveCacheLife = 60*5;  // 5 minutes
$devCacheLife = -1; // disabled
$cacheLife = Director::isDev() ? $devCacheLife : $liveCacheLife;
SS_Cache::set_cache_lifetime(JSONController::CACHE_NAME, $cacheLife, 100);
