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
use PHPUnit\Framework\TestCase;

define("__CA_DISABLE_CONFIG_CACHING__", true);

require_once(__CA_LIB_DIR__.'/ConfigurationYaml.php');

class ConfigurationYamlTest extends TestCase {
	/**
	 * @var ConfigurationYaml
	 */
	private $o_config;

	protected function setUp(): void {
		parent::setUp();
		$this->o_config = new ConfigurationYaml(__CA_BASE_DIR__.'/tests/lib/data/test.yaml', false, true);

	}

	public function testScalars() {

		$this->assertEquals('Hi there', $this->o_config->get('a_scalar'));
		$this->assertEquals('Hej da!', $this->o_config->get('a_translated_scalar'));
		$this->assertEquals('[The bracket is part of the string]', $this->o_config->get('a_scalar_starting_with_a_bracket'));
		$this->assertEquals('/usr/local/fish', $this->o_config->get('a_scalar_using_a_macro'));
		$this->assertEquals('This scalar is embedded: "/usr/local/fish"', $this->o_config->get('a_scalar_using_an_embedded_macro'));
		$this->assertEquals('Expreß zug: חי תהער', $this->o_config->get('a_scalar_with_utf_8_chars'));
		$this->assertEquals("Foo\nHello\nWorld\n", $this->o_config->get('a_scalar_with_line_breaks'));
	}

	public function testLists() {

		$va_array = $this->o_config->getList('a_list');
		$this->assertEquals(sizeof($va_array), 4);
		$this->assertEquals($va_array[0], 'clouds');
		$this->assertEquals($va_array[1], 'rain');
		$this->assertEquals($va_array[2], 'sun');
		$this->assertEquals($va_array[3], 'gewitter');

		$va_array = $this->o_config->getList('a_list_with_quoted_scalars');
		$this->assertEquals(sizeof($va_array), 2);
		$this->assertEquals($va_array[0], 'cloudy days');
		$this->assertEquals($va_array[1], 'rainy days, happy nights');

		$va_array = $this->o_config->getList('a_list_with_translated_scalars');
		$this->assertEquals(sizeof($va_array), 3);
		$this->assertEquals($va_array[0], 'red');
		$this->assertEquals($va_array[1], 'blue');
		$this->assertEquals($va_array[2], 'green');

		$va_array = $this->o_config->getList('a_list_with_a_macro');
		$this->assertEquals(sizeof($va_array), 2);
		$this->assertEquals($va_array[0], '/usr/local/fish');
		$this->assertEquals($va_array[1], 'and so it goes');


		$va_array = $this->o_config->getList('macro_list');
		$this->assertEquals(sizeof($va_array), 3, 'Size of list defined in global.conf is not 3');
		$this->assertEquals($va_array[0], 'flounder');
		$this->assertEquals($va_array[1], 'lobster');
		$this->assertEquals($va_array[2], 'haddock');

		$va_array = $this->o_config->getList('a_list_with_embedded_brackets');
		$this->assertEquals($va_array[0], 'Hello [there]');
	}

	public function testAssocLists() {

		$va_assoc = $this->o_config->getAssoc('an_associative_list');
		$this->assertEquals(sizeof(array_keys($va_assoc)), 1);
		$this->assertEquals(sizeof(array_keys($va_assoc['key 1'])), 5);
		$this->assertEquals($va_assoc['key 1']['subkey1'], 1);
		$this->assertEquals($va_assoc['key 1']['subkey2'], 2);
		$this->assertTrue(is_array($va_assoc['key 1']['subkey3']));
		$this->assertEquals($va_assoc['key 1']['subkey3']['subsubkey1'], 'at the bottom of the hole');
		$this->assertEquals($va_assoc['key 1']['subkey3']['subsubkey2'], 'this is a quoted string');
		$this->assertTrue(is_array($va_assoc['key 1']['subkey4']));
		$this->assertEquals($va_assoc['key 1']['subkey4'][0], 'Providence');
		$this->assertEquals($va_assoc['key 1']['subkey4'][1], 'Pawtucket');
		$this->assertEquals($va_assoc['key 1']['subkey4'][2], 'Woonsocket');
		$this->assertEquals($va_assoc['key 1']['subkey4'][3], 'Narragansett');
		$this->assertEquals($va_assoc['key 1']['subkey5'], '/usr/local/fish');

		$va_assoc = $this->o_config->getAssoc('macro_assoc');
		$this->assertEquals(sizeof(array_keys($va_assoc)), 3);
		$this->assertEquals(sizeof(array_keys($va_assoc['fish'])), 3);
		$this->assertEquals(sizeof(array_keys($va_assoc['shellfish'])), 3);
		$this->assertEquals(sizeof(array_keys($va_assoc['other'])), 3);
		$this->assertEquals($va_assoc['fish'][0], 'flounder');
		$this->assertEquals($va_assoc['shellfish'][0], 'scallop');
		$this->assertEquals($va_assoc['other'][0], 'chicken');

		$va_assoc = $this->o_config->getAssoc('an_assoc_list_with_embedded_brackets');
		$this->assertEquals($va_assoc['test'], 'Hello {there}');
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
		$this->assertEquals(23, sizeof($va_keys));		// 12 in config file + 1 "LOCALE" value that's automatically inserted
		$va_keys = $this->o_config->getListKeys();
		$this->assertTrue(is_array($va_keys));
		$this->assertEquals(23, sizeof($va_keys));
		$va_keys = $this->o_config->getAssocKeys();
		$this->assertTrue(is_array($va_keys));
		$this->assertEquals(23, sizeof($va_keys));

	}
}
?>
