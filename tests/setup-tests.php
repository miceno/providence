<?php

// Ensure that the base dir is set correctly; this should normally be the parent of the "tests" dir.
if (!defined("__CA_BASE_DIR__")) {
	define("__CA_BASE_DIR__", dirname(__DIR__));
}

// Ensure that the local configuration directory is set to a location that does not exist; this ensures only defaults
// are used by the tests and site-specific overrides do not cause unexpected failures.
if (!defined("__CA_LOCAL_CONFIG_DIRECTORY__")) {
	define("__CA_LOCAL_CONFIG_DIRECTORY__", __DIR__ . "/conf");
}

// If you require any overrides in setup.php that are specific to running unit tests, put them here.


if (!defined("__CA_DB_HOST__")) {
	define("__CA_DB_HOST__", 'localhost');
}

# __CA_DB_USER__ = Database login user name
#
if (!defined("__CA_DB_USER__")) {
	define("__CA_DB_USER__", 'ca');
}

# __CA_DB_PASSWORD__ = Database login password
#
if (!defined("__CA_DB_PASSWORD__")) {
	define("__CA_DB_PASSWORD__", '1234ca5678');
}

# __CA_DB_DATABASE__ = The name of your CollectiveAccess database
#
if (!defined("__CA_DB_DATABASE__")) {
	define("__CA_DB_DATABASE__", 'collectiveaccess');
}

if (!defined("__CA_DB_TYPE__")) {
	define("__CA_DB_TYPE__", 'mysqli');
}

define('__CA_CACHE_BACKEND__', 'file');

// Use remaining settings from main config.
require_once(__CA_BASE_DIR__ . '/setup.php');
