<?php


/**
 * ----------------------------------------------------------------------
 * ConfigurationYaml.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2015-2020 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This source code is free and modifiable under the terms of
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * @package CollectiveAccess
 * @subpackage Core
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 *
 * A configuration class that reads configuration from a YAML file.
 *
 * In addition to Symfony YAML syntax, it allows some more features:
 *  - interpolation of translated strings. For example:
 *
 *          yaml_key: "_t('hello')"
 *          yaml_another_key: "_('hello')"
 *
 *  - interpolation of macro (configuration variables). For example:
 *
 *          previously_defined_variable: value
 *          yaml_key: <previously_defined_variable>
 *
 *  - interpolation of PHP constants. For example:
 *
 *          yaml_key: __XXXXXXX__
 *
 * YAML files are searched in the following order:
 *
 *  1. yaml file
 *  2. __CA_APP_CONF/yaml_file.yaml
 *  3. __CA_LOCAL_CONFIG_DIRECTORY__/yaml_file.yaml
 *  4. __CA_DEFAULT_THEME_CONFIG_DIRECTORY__/yaml_file.yaml
 *  5. __CA_LOCAL_CONFIG_DIRECTORY__/yaml_file.'_'.__CA_APP_NAME__.yaml
 *
 * Configuration in a file may override defined variables in previously
 * loaded files.
 *
 */

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ConfigurationYaml extends Configuration {

    public function __construct($ps_file_path = __CA_APP_CONFIG__, $pb_die_on_error = false, $pb_dont_cache = false, $pb_dont_load_from_default_path = false) {
        global $g_ui_locale, $g_configuration_cache_suffix;

        # path to configuration file
        $this->ops_config_file_path = $ps_file_path ? $ps_file_path : __CA_APP_CONFIG__;

        $va_config_file_list = [];

        // cache key for on-disk caching
        $vs_path_as_md5 = md5($_SERVER['HTTP_HOST'] . $this->ops_config_file_path . '/' . $g_ui_locale . (isset($g_configuration_cache_suffix) ? '/' . $g_configuration_cache_suffix : ''));

        #
        # Is configuration file already cached?
        #
        $va_config_path_components = explode("/", $this->ops_config_file_path);
        $vs_config_filename = array_pop($va_config_path_components);

        $vs_top_level_config_path = $this->ops_config_file_path;
        if (!$pb_dont_load_from_default_path) {
            list($vs_proposed_top_level_config_path, $va_config_file_list) = ConfigurationYaml::_updateConfigFileList($vs_config_filename, $va_config_file_list);
            $vs_top_level_config_path = $vs_proposed_top_level_config_path===null ? $this->ops_config_file_path : $vs_proposed_top_level_config_path;
        }
        $o_config = (($vs_top_level_config_path===$this->ops_config_file_path) ? $this : static::load($vs_top_level_config_path, false, false, true));

        $vs_filename = pathinfo($ps_file_path, PATHINFO_BASENAME);
        if (($vb_inherit_config = $o_config->get('allowThemeInheritance')) && !$pb_dont_load_from_default_path) {

            $va_config_file_list = $this->_updateInheritedConfigFileList($o_config, $vs_filename, $va_config_file_list);
        }
        array_unshift($va_config_file_list, $this->ops_config_file_path);

        // try to figure out if we can get it from cache
        if ((!defined('__CA_DISABLE_CONFIG_CACHING__') || !__CA_DISABLE_CONFIG_CACHING__) && !$pb_dont_cache) {

            if (self::_loadConfigFromCache($vs_path_as_md5, $va_config_file_list)) {
                return;
            }
        }

        # load hash
        $this->ops_config_settings = [];

        # try loading global.yaml file
        $vs_global_path = join("/", $va_config_path_components) . '/global.yaml';
        if (file_exists($vs_global_path)) {
            $this->loadFile($vs_global_path, false);
        }

        //
        // Insert current user locale as constant into configuration.
        //
        $this->ops_config_settings['LOCALE'] = $g_ui_locale;

        #
        # load specified config file
        #
        $vs_config_file_path = array_shift($va_config_file_list);
        if (file_exists($vs_config_file_path) && $this->loadFile($vs_config_file_path, $pb_die_on_error, null)) {
            $this->ops_config_file_path = $vs_config_file_path;
        }


        if (sizeof($va_config_file_list) > 0) {
            foreach ($va_config_file_list as $vs_config_file_path) {
                if (file_exists($vs_config_file_path)) {
                    $this->loadFile($vs_config_file_path, $pb_die_on_error, null);
                }
            }
        }

        if ($vs_path_as_md5 && !$pb_dont_cache) {
            self::$s_config_cache[$vs_path_as_md5] = $this->ops_config_settings;
            // we loaded this cfg from file, so we have to write the
            // config cache to disk at least once on this request
            self::$s_have_to_write_config_cache = true;

            ExternalCache::save('ConfigurationCache', self::$s_config_cache, 'default', 3600 * 3600 * 30);
        }
    }

    /**
     * @param mixed $pm_key
     * @return array|false|mixed|string|null
     */
    public function get($pm_key) {
        return $this->getValue($pm_key);
    }
    /* ---------------------------------------- */
    /**
     * Parses CONF configuration file located at $ps_file_path.
     *
     * @param $ps_filepath - absolute path to configuration file to parse
     * @param $pb_die_on_error - if true, die() will be called on parse error halting request; default is false
     * @param $pn_num_lines_to_read - if set to a positive integer, will abort parsing after the first $pn_num_lines_to_read lines of the config file are read. This is useful for reading in headers in config files without having to parse the entire file.
     * @return boolean - returns true if parse succeeded, false if parse failed
     */
    public function loadFile($ps_filepath, $pb_die_on_error = false, $pn_num_lines_to_read = null) {
        $yaml_config = $this->loadYaml($ps_filepath, $pb_die_on_error);

        $this->ops_config_settings = static::mergeAndReplaceConfig($this->ops_config_settings, $yaml_config);
        return true;
    }

    /* ---------------------------------------- */
    /**
     * Parses YAML configuration file located at $ps_file_path.
     *
     * @param $ps_filepath - absolute path to configuration file to parse
     * @param $pb_die_on_error - if true, die() will be called on parse error halting request; default is false
     * @return boolean - returns true if parse succeeded, false if parse failed
     */
    public function loadYaml($ps_filepath, $pb_die_on_error = false) {

        try {
            $config = Yaml::parseFile($ps_filepath, Yaml::PARSE_CONSTANT);
            // Interpolate recursively
            array_walk_recursive($config, function (&$value) {
                $value = $this->_interpolateScalar($value);
            });
        } catch (ParseException $e) {
            $this->ops_error = "Couldn't open configuration file '" . $ps_filepath . "'";
            if ($pb_die_on_error) {
                $this->_dieOnError();
            }
            return false;
        }

        return $config;

    }

    /* ---------------------------------------- */
    /**
     * Merge two configurations.
     *
     * @param $left
     * @param $right
     * @return array
     */
    static public function mergeConfig($left, $right) {
        return array_merge_recursive($left, $right);
    }
    /* ---------------------------------------- */
    /**
     * Merge with replace two configurations.
     *
     * @param $left
     * @param $right
     * @return array
     */
    static public function mergeAndReplaceConfig($left, $right) {
        return array_replace_recursive($left, $right);
    }

    protected function _loadConfigFromCache(string $vs_path_as_md5, array $va_config_file_list) {
        self::loadConfigCacheInMemory();

        if ($vb_setup_has_changed = caSetupPhpHasChanged()) {
            self::clearCache();
        }

        if (!$vb_setup_has_changed && isset(self::$s_config_cache[$vs_path_as_md5])) {
            $vb_cache_is_invalid = false;

            foreach ($va_config_file_list as $vs_config_file_path) {
                $vs_config_mtime = caGetFileMTime($vs_config_file_path);
                if ($vs_config_mtime!=self::$s_config_cache[$k = 'mtime_' . $vs_path_as_md5 . md5($vs_config_file_path)]) { // config file has changed
                    self::$s_config_cache[$k] = $vs_config_mtime;
                    $vb_cache_is_invalid = true;
                    break;
                }
            }

            if (!$vb_cache_is_invalid) { // cache is ok
                $this->ops_config_settings = self::$s_config_cache[$vs_path_as_md5];
                $this->ops_md5_path = md5($this->ops_config_file_path);
                return true;
            }
        }
        return false;
    }

    /**
     * Update theme configuration file list if configuration inheritance is enabled.
     *
     * @param $o_config
     * @param $vs_filename
     * @param $va_config_file_list
     * @return mixed
     */
    protected function _updateInheritedConfigFileList($o_config, $vs_filename, $va_config_file_list) {
        $i = 0;
        while ($vs_inherit_from_theme = trim(trim($o_config->get(['inheritFrom', 'inherit_from'])), "/")) {
            $i++;
            $vs_inherited_config_path = __CA_THEMES_DIR__ . "/{$vs_inherit_from_theme}/conf/{$vs_filename}";
            # TODO: Original code also checked for an undefined variable
            #   .
            #   removed code:
            #   .
            #   && ($vs_inherited_config_path!==$vs_config_file_path)
            #
            if (file_exists($vs_inherited_config_path) && !in_array($vs_inherited_config_path, $va_config_file_list)) {
                array_unshift($va_config_file_list, $vs_inherited_config_path);
            }
            $config_filename = __CA_THEMES_DIR__ . "/{$vs_inherit_from_theme}/conf/app.yaml";
            if (!file_exists($config_filename)) {
                break;
            }
            $o_config = static::load($config_filename, false, false, true);
            if ($i > 10) {
                break;
            } // max 10 levels
        }
        return $va_config_file_list;
    }

    /**
     * Update configuration file list to include local, theme and app-specific configuration
     * files.
     *
     * @param string $vs_config_filename
     * @param array $va_config_file_list
     * @return array
     */
    public static function _updateConfigFileList(string $vs_config_filename, array $va_config_file_list): array {
        $vs_top_level_config_path = null;

        if (defined('__CA_LOCAL_CONFIG_DIRECTORY__') && file_exists($local_config_path = __CA_LOCAL_CONFIG_DIRECTORY__ . '/' . $vs_config_filename)) {
            $va_config_file_list[] = $vs_top_level_config_path = $local_config_path;
        }

        // Theme config overrides local config
        if (defined('__CA_DEFAULT_THEME_CONFIG_DIRECTORY__') && file_exists($theme_config_path = __CA_DEFAULT_THEME_CONFIG_DIRECTORY__ . '/' . $vs_config_filename)) {
            $va_config_file_list[] = $vs_top_level_config_path = __CA_DEFAULT_THEME_CONFIG_DIRECTORY__ . '/' . $vs_config_filename;
        }

        // Appname-specific config overrides local config
        if (defined('__CA_LOCAL_CONFIG_DIRECTORY__') && file_exists($appname_specific_path = __CA_LOCAL_CONFIG_DIRECTORY__ . '/' . pathinfo($vs_config_filename, PATHINFO_FILENAME) . '_' . __CA_APP_NAME__ . '.' . pathinfo($vs_config_filename, PATHINFO_EXTENSION))) {
            $va_config_file_list[] = $vs_top_level_config_path = $appname_specific_path;
        }
        return array($vs_top_level_config_path, $va_config_file_list);
    }

    /* ---------------------------------------- */
    /**
     * Get scalar configuration value
     *
     * @param string $ps_key Name of scalar configuration value to get. get() will look for the
     * configuration value only as a scalar. Like-named list or associative array values are
     * ignored.
     *
     * @return string
     */
    public function getScalar($ps_key) {
        return $this->getValue($ps_key);
    }
    /* ---------------------------------------- */
    /**
     * Get configuration value
     *
     * @param string $ps_key Name of  configuration value to get. getValue() will look for the
     * configuration value only as a scalar. Like-named list or associative array values are
     * ignored.
     *
     * @return string
     */
    public function getValue($ps_key) {
        $this->ops_error = "";
        if (isset($this->ops_config_settings[$ps_key])) {
            return $this->ops_config_settings[$ps_key];
        } else {
            return null;
        }
    }
    /* ---------------------------------------- */
    /**
     * Get associative configuration value
     *
     * @param string $ps_key Name of associative configuration value to get. get() will look for the
     * configuration value only as an associative array. Like-named scalar or list values are
     * ignored.
     *
     * @return array An associative array
     */
    public function getAssoc($ps_key) {
        $this->ops_error = "";
        $assoc = $this->getValue($ps_key);
        if (is_array($assoc)) {
            return $assoc;
        } else {
            return null;
        }
    }
    /* ---------------------------------------- */
    /**
     * Get list configuration value
     *
     * @param string $ps_key Name of list configuration value to get. get() will look for the
     * configuration value only as a list. Like-named scalar or associative array values are
     * ignored.
     *
     * @return array An indexed array
     */
    public function getList($ps_key) {
        $this->ops_error = "";
        $list = $this->getValue($ps_key);
        if (is_array($list)) {
            return $list;
        } else {
            return null;
        }
    }
    /* ---------------------------------------- */
    /**
     * Return currently loaded configuration file as JSON
     *
     * @return string
     */
    public function toJson() {
        return caFormatJson(json_encode($this->ops_config_settings));
    }

    /* ---------------------------------------- */
    /**
     * Get keys for associative values
     *
     *
     * @return array List of all possible keys for associative values
     */
    public function getAssocKeys() {
        $this->ops_error = "";
        return @array_keys($this->ops_config_settings);
    }

    /* ---------------------------------------- */
    /**
     * Get keys for list values
     *
     *
     * @return array List of all possible keys for list values
     */
    public function getListKeys() {
        $this->ops_error = "";
        return @array_keys($this->ops_config_settings);
    }

    /* ---------------------------------------- */
    /**
     * Get keys for scalar values
     *
     *
     * @return array List of all possible keys for scalar values
     */
    public function getScalarKeys() {
        $this->ops_error = "";
        return @array_keys($this->ops_config_settings);
    }

    protected function _interpolateScalar($ps_text) {

        if (preg_match_all("/<([A-Za-z0-9_\-.]+)>/", $ps_text, $va_matches)) {
            foreach ($va_matches[1] as $vs_key) {
                if (($vs_val = $this->getScalar($vs_key))!==false) {
                    $ps_text = preg_replace("/<$vs_key>/", $vs_val, $ps_text);
                }
            }
        }

        // perform constant var substitution
        if (preg_match("/(__[A-Za-z0-9_]+__)/", $ps_text, $va_matches)) {

            $vs_constant_name = $va_matches[1];
            if (defined($vs_constant_name)) {
                $ps_text = str_replace($vs_constant_name, constant($vs_constant_name), $ps_text);
            }
        }

        // attempt translation if text is enclosed in _( and ) ... for example _t(translate me)
        // assumes translation function _t() is present; if not loaded will not attempt translation
        if (preg_match("/_[t]?\([\"']+([^)]+)[\"']\)/", $ps_text, $va_matches)) {
            if (function_exists('_t')) {
                $vs_trans_text = $ps_text;
                array_shift($va_matches);
                foreach ($va_matches as $vs_match) {
                    $vs_trans_text = preg_replace(caMakeDelimitedRegexp("_[t]?\([\"']+{$vs_match}[\"']\)"), _t($vs_match), $vs_trans_text);
                }
                $ps_text = $vs_trans_text;
            }
        }
        return $ps_text;
    }
}