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


class AbstractSetup {
    protected $opa_settings = array();

    public function __construct($pa_settings) {
        if (!is_null($pa_settings)) {
            $this->setOpaSettings($pa_settings);
        }
    }

    /**
     * @return array
     */
    public function &getOpaSettings(): array {
        return $this->opa_settings;
    }

    /**
     * @param array $opa_settings
     */
    public function setOpaSettings(array $opa_settings): void {
        $this->opa_settings = $opa_settings;
    }

    public function clear(): void {
        $this->setOpaSettings([]);
    }

    public function set($ps_key, $pm_value): void {
        $this->getOpaSettings()[$ps_key] = $pm_value;
    }

    /**
     * Get the value of a configuration option.
     *
     * @param      $pm_option           A dot string option or an array.
     * @param null $pm_default Default value
     * @param null $pa_parse_options Options (see \utilityHelpers\caGetOption)
     *
     * @return array|array[]|bool|float|int|mixed|null[]|string|string[]
     */
    public function get($pm_option, $pm_default = null, $pa_parse_options = null) {
        $pm_elements = preg_split('/\./', $pm_option);
        $result = $this->getOpaSettings();
        foreach ($pm_elements as $pm_element) {
            $result = caGetOption($pm_element,
                    $result,
                    $pm_default,
                    $pa_parse_options);
        }
        return $result;
    }
}


class Setup extends AbstractSetup {
}


$o_setup = new Setup($ga_setup);
