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

require_once 'Zend/Config/Yaml.php';

class CaSetup extends Zend_Config_Yaml {
    public function __construct($yaml, $section = null, $options = false) {
        parent::__construct($yaml, $section, $options);
    }

    public function get($name, $default = null) {
        if (!is_array($name)){
            $name = explode('.', $name);
        }

        $result = array_reduce($name,
                function ($o, $p) { return parent::get($p); }, $this);

        return $result;
    }
};

$o_setup = new CaSetup(
        __DIR__ . '/setup.yaml',
        APPLICATION_ENV
);
