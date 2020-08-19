<?php
/*
 * ----------------------------------------------------------------------
 * AbstractConfigurationTest.php
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

use PHPUnit\Framework\TestCase;

require_once(__CA_BASE_DIR__ . '/ConfigurationSetup.php');


class ConfigurationSetupTest extends TestCase {

    protected $o_setup = null;

    protected function setUp(): void {
        parent::setUp();
        define('__CA_TEST_CONSTANT__', 'overridden value');
        $this->o_setup = new \CaSetup(new Zend_Config_Yaml(__CA_BASE_DIR__ . '/tests/setup-tests.yaml', null,
                array('allow_modifications' => true)));
    }

    public function testSet() {
        $o_setup = $this->o_setup;

        $o_setup->set('config_format', 'pp');
        $this->assertEquals('pp', $o_setup->get('config_format'));
    }

    public function testNamespacesAllowUsingSameKey() {
        $o_setup = $this->o_setup;
        $this->assertEquals('yaml', $o_setup->get('config_format'));
        $this->assertEquals('yaml_on_global', $o_setup->get('global.config_format'));
    }

    public function testGetDotNotationExists() {
        $o_setup = $this->o_setup;

        $result = $o_setup->get('global.config_format');
        $this->assertEquals('yaml_on_global', $result);
    }

    public function testSetDotNotation() {
        $o_setup = $this->o_setup;

        $o_setup->set('global.config_format', 'pp');
        $result = $o_setup->get('global.config_format');
        $this->assertEquals('pp', $result);
    }

    public function testGetArrayExists() {
        $o_setup = $this->o_setup;

        $result = $o_setup->get(array('global', 'config_format'));
        $this->assertEquals('yaml_on_global', $result);
    }

    public function testGetDotNotationDoesNotExist() {
        $o_setup = $this->o_setup;

        $result = $o_setup->get('global.non_exists');
        $this->assertNull($result);
    }

    public function testGetKeyDoesNotExist() {
        $o_setup = $this->o_setup;

        $result = $o_setup->get('non_exists');
        $this->assertNull($result);
    }

    public function testDefinedConstant(){
        $o_setup = $this->o_setup;

        $this->assertTrue(defined('__CA_LOCAL_CONFIG_DIRECTORY__'));
        $this->assertEquals(__CA_BASE_DIR__ . '/tests/conf', __CA_LOCAL_CONFIG_DIRECTORY__);
    }

    public function testDefinedConstantIsNotOverriddenBySetup(){
        $o_setup = $this->o_setup;

        $this->assertTrue(defined('__CA_TEST_CONSTANT__'));
        $this->assertEquals('overridden value', __CA_TEST_CONSTANT__);
        $this->assertEquals('value from file', $o_setup->get('test.__CA_TEST_CONSTANT__'));
    }
}
