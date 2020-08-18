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


class CaSetup {
    protected $_config;

    public function __construct($config) {
        $this->_config = $config;
    }

    /**
     * Retrieve a value and return $default if there is no element set.
     *
     * @param      $names       A dot string or an array of the elements in the path
     * @param null $default
     *
     * @return mixed|null
     */
    public function get($names, $default = null) {
        if (!is_array($names)){
            $names = explode('.', $names);
        }
        // To avoid infinite recursion, call parent get, since it is not overridden.
        $result = array_reduce($names,
                function ($o, $p) use ($default) { return $o->get($p, $default); }, $this->_config);

        return $result;
    }

    /**
     * Only allow setting of a property if $allowModifications
     * was set to true on construction. Otherwise, throw an exception.
     *
     * @param $names    A dot string or an array of the elements in the path
     * @param $value
     *
     * @return $this
     */
    public function set($names, $value) {
        if (!is_array($names)){
            $names = explode('.', $names);
        }

        $arr = $this->_config;
        $last = array_pop($names);
        foreach ($names as $key) {
            $arr = $arr->$key;
        }

        $arr->__set($last, $value);

        return $this;
    }

};

$o_setup = new CaSetup(
        __DIR__ . '/setup.yaml',
        APPLICATION_ENV
);
