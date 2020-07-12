<?php

// If you require any overrides in setup.php that are specific to running unit tests, put them here.

# Define test-specific constants.
if (!defined("__CA_DB_HOST__")) {
	define("__CA_DB_HOST__", 'localhost');
}
if (!defined("__CA_DB_USER__")) {
	define("__CA_DB_USER__", 'ca_test');
}
if (!defined("__CA_DB_PASSWORD__")) {
	define("__CA_DB_PASSWORD__", 'password');
}
if (!defined("__CA_DB_DATABASE__")) {
	define("__CA_DB_DATABASE__", 'ca_test');
}
if (!defined("__CA_APP_DISPLAY_NAME__")) {
	define("__CA_APP_DISPLAY_NAME__", 'CollectiveAccess Unit Tests');
}
if (!defined("__CA_DB_TYPE__")) {
	define("__CA_DB_TYPE__", 'mysqli');
}

// Ensure that the base dir is set correctly; this should normally be the parent of the "tests" dir.
if (!defined("__CA_BASE_DIR__")) {
	define("__CA_BASE_DIR__", dirname(__DIR__));
}

// Override to allow tests to read local configuration.
if (!defined("__CA_LOCAL_CONFIG_DIRECTORY__")) {
	define("__CA_LOCAL_CONFIG_DIRECTORY__", __DIR__ . "/conf");
}

// Override to allow tests to read theme local configuration.
if (!defined("__CA_DEFAULT_THEME_CONFIG_DIRECTORY__")) {
    define("__CA_DEFAULT_THEME_CONFIG_DIRECTORY__", __DIR__ . "/conf/theme");
}

if (!defined('__CA_CACHE_BACKEND__')) {
	define('__CA_CACHE_BACKEND__', 'file');
}

// Use remaining settings from main config.
require_once(__CA_BASE_DIR__ . '/setup.php-dist');
