<?php
/*
 * ----------------------------------------------------------------------
 * ConfigurationSetup.php
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
 * @subpackage providence
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 *
 */

use Symfony\Component\Yaml\Yaml;

// TODO: Add base configuration class with common code for configuration subclasses
//  (ConfigurationYaml, ConfigurationSetup, etc.) like:
//   - dot notation access to fields
//   - etc.

class ConfigurationBase {
    protected $_config;

    public function __construct($config, $section = null) {
        $section_config = $config;
        if (!is_null($section)) {
            $section_config = $config[$section];
        }

        $this->_config = $section_config;
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param      $names string|array      A dot string or an array of the elements in the path
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($names) {
        if (!is_array($names)) {
            $names = explode('.', $names);
        }
        $result = array_reduce($names,
                function ($o, $p) {
                    return $o[$p];
                }, $this->_config);

        return $result;
    }

    /**
     * Only allow setting of a property if $allowModifications
     * was set to true on construction. Otherwise, throw an exception.
     *
     * @param $names string|array   A dot string or an array of the elements in the path
     * @param $value
     *
     * @return $this
     */
    public function set($names, $value) {
        if (!is_array($names)) {
            $names = explode('.', $names);
        }

        $arr = &$this->_config;
        $last = array_pop($names);
        foreach ($names as $key) {
            $arr = &$arr[$key];
        }

        $arr[$last] = $value;

        return $this;
    }

    /**
     * Interpolates a scalar value. Allowed interpolations are:
     *
     *  - macro, like in <macro>
     *  - constant, like in __XXXXX__
     *  - translation, like in _t('hello') or _('hello')
     *
     * @param $ps_text
     *
     * @return mixed|string|string[]|null
     */
    protected function _interpolateScalar($ps_text) {
        do {
            $last_text = $ps_text;

            // perform macro/variable substitution
            if (preg_match_all("/<([A-Za-z0-9_\-.]+)\>/", $ps_text, $va_matches)) {
                foreach ($va_matches[1] as $vs_key) {
                    if (($vs_val = $this->get($vs_key))!==false) {
                        $ps_text = preg_replace("/<$vs_key>/", $vs_val, $ps_text);
                    }
                }
            }

            // perform constant substitution
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
                        $vs_trans_text = preg_replace(caMakeDelimitedRegexp("_[t]?\([\"']+{$vs_match}[\"']\)"),
                                _t($vs_match), $vs_trans_text);
                    }
                    $ps_text = $vs_trans_text;
                }
            }
        } while ($ps_text!==$last_text);
        return $ps_text;
    }

}


class CaSetup extends ConfigurationBase {
    protected $_config;

    public function __construct($config, $section = null) {
        parent::__construct($config, $section);
        $this->init();
    }

    /**
     *  Initialize setup, creating constants for each key matching '__CA'
     */
    public function init() {
        $this->_interpolate();
        $this->_toConstants();
    }

    protected function _toConstants(): void {
        $vm_all_config = $this->_config;
        foreach ($vm_all_config as $key => $value) {
            if (strpos($key, '__CA')===0) {
                if (!defined($key)) {
                    define($key, $value);
                }
                else {
                    // Show a warning
                    trigger_error("Trying to redefine a defined variable $key", E_USER_NOTICE);
                }
            }
        }
    }

    private function _interpolate() {
        array_walk_recursive($this->_config, function (&$value) {
            $value = $this->_interpolateScalar($value);
        });
    }
}


$o_setup = new CaSetup(
    Yaml::parseFile(__CA_SETUP_FILE__, Yaml::PARSE_CONSTANT),
    APPLICATION_ENV);
