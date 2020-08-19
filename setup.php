<?php
# --------------------------------------------------------------------------------------------
#   ____      _ _           _   _              _                         
#  / ___|___ | | | ___  ___| |_(_)_   _____   / \   ___ ___ ___  ___ ___ 
# | |   / _ \| | |/ _ \/ __| __| \ \ / / _ \ / _ \ / __/ __/ _ \/ __/ __|
# | |__| (_) | | |  __/ (__| |_| |\ V /  __// ___ \ (_| (_|  __/\__ \__ \
#  \____\___/|_|_|\___|\___|\__|_| \_/ \___/_/   \_\___\___\___||___/___/
#
#				Providence: Cataloguing system for CollectiveAccess
#               Open-source collections management software
#               Version 1.7.x
#				
# -------------------------------------------------------------------------------------------
# 
# This file defines the basic settings required for the CollectiveAccess Providence 
# cataloguing module. This is the first file you should modify when configuring the application.
#
# Need help? Visit https://www.collectiveaccess.org/support/
#
# --------------------------------------------------------------------------------------------
# THE VALUES BELOW MUST BE CHANGED TO SUIT YOUR ENVIRONMENT
# --------------------------------------------------------------------------------------------

# Set your preferred time zone here. The default is to use US Eastern Standard Time.
# A list of valid time zone settings is available at http://us3.php.net/manual/en/timezones.php
#
date_default_timezone_set('America/New_York');

if (!defined('APPLICATION_ENV')) {
    define('APPLICATION_ENV', 'production');
}

if (!defined('__CA_SETUP_FILE__')) {
    define('__CA_SETUP_FILE__', __DIR__ . '/setup.yaml');
}

require_once(__DIR__ . '/ConfigurationSetup.php');
require_once(__DIR__ . '/app/helpers/post-setup.php');
