<?php

# Override configuration

// Ensure that the base dir is set correctly; this should normally be the parent of the "tests" dir.
if (!defined('__CA_BASE_DIR__')) {
    define('__CA_BASE_DIR__', dirname(__DIR__));
}

// Override to allow tests to read a custom local configuration.
if (!defined('__CA_LOCAL_CONFIG_DIRECTORY__')) {
    define('__CA_LOCAL_CONFIG_DIRECTORY__', __DIR__ . '/conf');
}

// Override to allow tests to read theme local configuration.
if (!defined('__CA_DEFAULT_THEME_CONFIG_DIRECTORY__')) {
    define('__CA_DEFAULT_THEME_CONFIG_DIRECTORY__', __DIR__ . '/conf/theme');
}

# Load custom configuration
define('APPLICATION_ENV', 'test');
define('__CA_SETUP_FILE__', __DIR__ . '/setup-tests.yaml');

// Use remaining settings from main config.
require_once(__CA_BASE_DIR__ . '/setup.php');
