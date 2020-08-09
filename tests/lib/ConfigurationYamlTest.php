<?php
/** ---------------------------------------------------------------------
 * tests/lib/ConfigurationYamlTest.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2018 Whirl-i-Gig
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
 * @subpackage tests
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 */

define("__CA_DISABLE_CONFIG_CACHING__", true);

require_once(__CA_LIB_DIR__ . '/ConfigurationYaml.php');
require_once(__CA_BASE_DIR__ . '/tests/BaseTestClearCache.php');


class ConfigurationYamlTest extends BaseTestClearCache {
    /**
     * @var ConfigurationYaml
     */
    private $o_config;

    protected function setUp(): void {
        parent::setUp();
        $this->o_config = new ConfigurationYaml(__CA_BASE_DIR__ . '/tests/lib/data/test.yaml', true, true);
    }

    public function testScalars() {
        $this->assertEquals('Hi there', $this->o_config->getScalar('a_scalar'));
        $this->assertEquals('Hej da!', $this->o_config->getScalar('a_translated_scalar'));
        $this->assertEquals('[The bracket is part of the string]', $this->o_config->getScalar('a_scalar_starting_with_a_bracket'));
        $this->assertEquals('/usr/local/fish', $this->o_config->getScalar('a_scalar_using_a_macro'));
        $this->assertEquals('This scalar is embedded: "/usr/local/fish"', $this->o_config->getScalar('a_scalar_using_an_embedded_macro'));
        $this->assertEquals('Expreß zug: חי תהער', $this->o_config->getScalar('a_scalar_with_utf_8_chars'));
        $this->assertEquals("Foo\nHello\nWorld\n", $this->o_config->getScalar('a_scalar_with_line_breaks'));
    }

    public function testLists() {
        $va_array = $this->o_config->getList('a_list');
        $this->assertEquals(4, sizeof($va_array));
        $this->assertEquals('clouds', $va_array[0]);
        $this->assertEquals('rain', $va_array[1]);
        $this->assertEquals('sun', $va_array[2]);
        $this->assertEquals('gewitter', $va_array[3]);

        $va_array = $this->o_config->getList('a_list_with_quoted_scalars');
        $this->assertEquals(2, sizeof($va_array));
        $this->assertEquals('cloudy days', $va_array[0]);
        $this->assertEquals('rainy days, happy nights', $va_array[1]);

        $va_array = $this->o_config->getList('a_list_with_translated_scalars');
        $this->assertEquals(3, sizeof($va_array));
        $this->assertEquals('red', $va_array[0]);
        $this->assertEquals('blue', $va_array[1]);
        $this->assertEquals('green', $va_array[2]);

        $va_array = $this->o_config->getList('a_list_with_a_macro');
        $this->assertEquals(2, sizeof($va_array));
        $this->assertEquals('/usr/local/fish', $va_array[0]);
        $this->assertEquals('and so it goes', $va_array[1]);

        $va_array = $this->o_config->getList('macro_list');
        $this->assertEquals(3, sizeof($va_array), 'Size of list defined in global.conf is not 3');
        $this->assertEquals('flounder', $va_array[0]);
        $this->assertEquals('lobster', $va_array[1]);
        $this->assertEquals('haddock', $va_array[2]);

        $va_array = $this->o_config->getList('a_list_with_embedded_brackets');
        $this->assertEquals('Hello [there]', $va_array[0]);
    }

    public function testAssocLists() {
        $va_assoc = $this->o_config->getAssoc('an_associative_list');
        $this->assertEquals(1, sizeof(array_keys($va_assoc)));
        $this->assertEquals(5, sizeof(array_keys($va_assoc['key 1'])));
        $this->assertEquals(1, $va_assoc['key 1']['subkey1']);
        $this->assertEquals(2, $va_assoc['key 1']['subkey2']);
        $this->assertTrue(is_array($va_assoc['key 1']['subkey3']));
        $this->assertEquals('at the bottom of the hole', $va_assoc['key 1']['subkey3']['subsubkey1']);
        $this->assertEquals('this is a quoted string', $va_assoc['key 1']['subkey3']['subsubkey2']);
        $this->assertTrue(is_array($va_assoc['key 1']['subkey4']));
        $this->assertEquals('Providence', $va_assoc['key 1']['subkey4'][0]);
        $this->assertEquals('Pawtucket', $va_assoc['key 1']['subkey4'][1]);
        $this->assertEquals('Woonsocket', $va_assoc['key 1']['subkey4'][2]);
        $this->assertEquals('Narragansett', $va_assoc['key 1']['subkey4'][3]);
        $this->assertEquals('/usr/local/fish', $va_assoc['key 1']['subkey5']);

        $va_assoc = $this->o_config->getAssoc('macro_assoc');
        $this->assertEquals(3, sizeof(array_keys($va_assoc)));
        $this->assertEquals(3, sizeof(array_keys($va_assoc['fish'])));
        $this->assertEquals(3, sizeof(array_keys($va_assoc['shellfish'])));
        $this->assertEquals(3, sizeof(array_keys($va_assoc['other'])));
        $this->assertEquals('flounder', $va_assoc['fish'][0]);
        $this->assertEquals('scallop', $va_assoc['shellfish'][0]);
        $this->assertEquals('chicken', $va_assoc['other'][0]);

        $va_assoc = $this->o_config->getAssoc('an_assoc_list_with_embedded_brackets');
        $this->assertEquals('Hello {there}', $va_assoc['test']);
    }

    public function testBoolean() {
        $vb_scalar = $this->o_config->getBoolean('boolean_yes');
        $this->assertTrue($vb_scalar);
        $vb_scalar = $this->o_config->getBoolean('boolean_ja');
        $this->assertTrue($vb_scalar);
        $vb_scalar = $this->o_config->getBoolean('boolean_wahr');
        $this->assertTrue($vb_scalar);
        $vb_scalar = $this->o_config->getBoolean('boolean_no');
        $this->assertFalse($vb_scalar);
        $vb_scalar = $this->o_config->getBoolean('boolean_nein');
        $this->assertFalse($vb_scalar);
    }

    public function testMisc() {
        $va_keys = $this->o_config->getScalarKeys();
        $this->assertTrue(is_array($va_keys));
        $this->assertEquals(25, sizeof($va_keys));        // 24 in config file + 1 "LOCALE" value that's automatically inserted
        $va_keys = $this->o_config->getListKeys();
        $this->assertTrue(is_array($va_keys));
        $this->assertEquals(25, sizeof($va_keys));
        $va_keys = $this->o_config->getAssocKeys();
        $this->assertTrue(is_array($va_keys));
        $this->assertEquals(25, sizeof($va_keys));
    }

    public function testUpdateConfigFileListReturnsNullandEmpty() {
        $va_config_file_list = array();
        list($vs_top_level_config_path, $va_config_file_list) = ConfigurationYaml::_updateConfigFileList('not_exists_config.yaml', $va_config_file_list);
        $this->assertNull($vs_top_level_config_path);
        $this->assertEmpty($va_config_file_list);
    }

    public function testUpdateConfigFileListReturnsLocalConfig() {
        $va_config_file_list = array();
        list($vs_top_level_config_path, $va_config_file_list) = ConfigurationYaml::_updateConfigFileList('local_config.yaml', $va_config_file_list);
        $this->assertNotNull($vs_top_level_config_path);
        $this->assertEquals(1, sizeof($va_config_file_list));
        $this->assertStringEndsWith('tests/conf/local_config.yaml', $vs_top_level_config_path);
    }

    public function testUpdateConfigFileListReturnsThemeConfig() {
        $va_config_file_list = array();
        list($vs_top_level_config_path, $va_config_file_list) = ConfigurationYaml::_updateConfigFileList('theme_config.yaml', $va_config_file_list);
        $this->assertNotNull($vs_top_level_config_path);
        $this->assertEquals(1, sizeof($va_config_file_list));
        $this->assertStringEndsWith('tests/conf/theme/theme_config.yaml', $vs_top_level_config_path);
    }

    public function testUpdateConfigFileListReturnsAppConfig() {
        $va_config_file_list = array();
        list($vs_top_level_config_path, $va_config_file_list) = ConfigurationYaml::_updateConfigFileList('app_config.yaml', $va_config_file_list);
        $this->assertNotNull($vs_top_level_config_path);
        $this->assertEquals(1, sizeof($va_config_file_list));
        $this->assertStringEndsWith('tests/conf/app_config_collectiveaccess.yaml', $vs_top_level_config_path);
    }

    public function testInterpolateScalarWithConstant() {
        $this->assertEquals('This scalar is embedded: ' . __CA_LOCAL_CONFIG_DIRECTORY__, $this->o_config->getScalar('a_scalar_using_an_embedded_constant'));
    }

}
