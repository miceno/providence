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
    protected $o_config;

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

    public function testLocalOverridesConfiguration(){
        $o_config = new ConfigurationYaml(__CA_BASE_DIR__ . '/tests/lib/data/test_local_override.yaml', true, true);

        $this->assertEquals('override_by_local_value', $o_config->get('override_local'));
    }

    public function testThemeOverridesConfiguration(){
        $o_config = new ConfigurationYaml(__CA_BASE_DIR__ . '/tests/lib/data/test_theme_override.yaml', true, true);

        $this->assertEquals('override_by_theme_value', $o_config->get('variable_on_theme'));
    }

    public function testLocalOverridesConfigurationListElement(){
        $o_config = new ConfigurationYaml(__CA_BASE_DIR__ . '/tests/lib/data/test_local_override.yaml', true, true);

        $this->assertIsArray($o_config->get('overriden_list'));
        $this->assertEqualsCanonicalizing(array('list100', 'list2', 'list3'),$o_config->get('overriden_list'));
    }

    public function testLocalOverridesConfigurationMapElement(){
        $o_config = new ConfigurationYaml(__CA_BASE_DIR__ . '/tests/lib/data/test_local_override.yaml', true, true);

        $this->assertIsArray($o_config->get('overriden_map'));
        $values = array('value1000', 'value2', 'value3', 'value4000', null);
        $keys = array('key1', 'key2', 'key3', 'key4', 'key5');
        $this->assertEquals(array_combine($keys, $values), $o_config->get('overriden_map'));
    }

    public function testLocalOverridesConfigurationWholeList(){
        $o_config = new ConfigurationYaml(__CA_BASE_DIR__ . '/tests/lib/data/test_local_override.yaml', true, true);

        $this->assertEquals(array('list100', null, null), $o_config->get('overriden_whole_list'));
    }

    public function testLocalOverridesConfigurationWholeMap(){
        $o_config = new ConfigurationYaml(__CA_BASE_DIR__ . '/tests/lib/data/test_local_override.yaml', true, true);
        $values = array('value100', 'value200', 'value300');
        $keys = array('key1', 'key2', 'key3');
        $this->assertEquals(array_combine($keys, $values), $o_config->get('overriden_whole_map'));
    }

    public function testExistsConfiguration(){
        $this->assertTrue($this->o_config->exists('override_value'));
    }

    public function testExistsConfigurationFromLocalCache(){
        $this->o_config->get('override_value');
        $this->assertTrue($this->o_config->exists('override_value'));
    }

    public function testDoesNotExistConfiguration(){
        $this->assertFalse($this->o_config->exists('not_exists'));
    }

    public function testGetValueNonExistsNull(){
        $this->assertNull($this->o_config->getValue('not_exists'));
    }

    public function testGetAssocNonExistsNull(){
        $this->assertNull($this->o_config->getAssoc('not_exists'));
    }

    public function testGetListNonExistsNull(){
        $this->assertNull($this->o_config->getList('not_exists'));
    }

    public function testGetValueExistsLast(){
        $this->assertEquals('global', $this->o_config->getValue(['not_exists', 'override_value']));
    }

    public function testJson(){
        $vs_expected_json = <<<EOT
{
  "override_value":"global",
  "macro_scalar":"\/usr\/local\/fish",
  "macro_list":[
    "flounder",
    "lobster",
    "haddock"
  ],
  "macro_assoc":{
    "fish":[
      "flounder",
      "cod",
      "haddock"
    ],
    "shellfish":[
      "scallop",
      "crab",
      "clam"
    ],
    "other":[
      "chicken",
      "pig",
      "cow"
    ]
  },
  "LOCALE":null,
  "a_scalar":"Hi there",
  "a_translated_scalar":"Hej da!",
  "a_scalar_starting_with_a_bracket":"[The bracket is part of the string]",
  "a_scalar_using_a_macro":"\/usr\/local\/fish",
  "a_scalar_using_an_embedded_macro":"This scalar is embedded: \"\/usr\/local\/fish\"",
  "a_scalar_using_an_embedded_constant":"This scalar is embedded: \/Users\/orestes\/devel\/providence\/tests\/conf",
  "a_scalar_with_utf_8_chars":"Expre\u00df zug: \u05d7\u05d9 \u05ea\u05d4\u05e2\u05e8",
  "a_scalar_with_line_breaks":"Foo\\nHello\\nWorld\\n",
  "a_list":[
    "clouds",
    "rain",
    "sun",
    "gewitter"
  ],
  "a_list_with_quoted_scalars":[
    "cloudy days",
    "rainy days, happy nights"
  ],
  "a_list_with_translated_scalars":[
    "red",
    "blue",
    "green"
  ],
  "a_list_with_a_macro":[
    "\/usr\/local\/fish",
    "and so it goes"
  ],
  "an_associative_list":{
    "key 1":{
      "subkey1":1,
      "subkey2":2,
      "subkey3":{
        "subsubkey1":"at the bottom of the hole",
        "subsubkey2":"this is a quoted string"
      },
      "subkey4":[
        "Providence",
        "Pawtucket",
        "Woonsocket",
        "Narragansett"
      ],
      "subkey5":"\/usr\/local\/fish"
    }
  },
  "boolean_yes":"yes",
  "boolean_ja":1,
  "boolean_wahr":true,
  "boolean_nein":0,
  "boolean_no":"no",
  "an_assoc_list_with_embedded_brackets":{
    "test":"Hello {there}"
  },
  "a_list_with_embedded_brackets":[
    "Hello [there]"
  ]
}

EOT;

        $vs_json = $this->o_config->toJson();
        $this->assertEquals($vs_expected_json, $vs_json);
    }
}
