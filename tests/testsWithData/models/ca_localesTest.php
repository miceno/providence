<?php
/**
 * ----------------------------------------------------------------------
 * ca_localesTest.php
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
 * @package    CollectiveAccess
 * @subpackage tests
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 *
 */

use PHPUnit\Framework\TestCase;

require_once(__CA_BASE_DIR__ . '/tests/testsWithData/BaseTestWithData.php');


class ca_localesTest extends BaseTestWithData {

    public function testGetDefaultCataloguingLocaleID() {
        $this->assertEquals(1, ca_locales::getDefaultCataloguingLocaleID());
    }

    public function testLocalesForLanguage() {
        $this->assertEqualsCanonicalizing(array('en_US', 'en_CA', 'en_AU'), array_keys(ca_locales::localesForLanguage('en')));
    }

    public function testIdToCode() {
        $this->assertEquals('en_US', ca_locales::IDToCode(1));
    }

    public function testGetLocaleListByCode() {
        $va_locale_list = ca_locales::getLocaleList(array('index_by_code' => true));

        $default_locale_codes = array('cs_CZ',
                'de_AT',
                'de_DE',
                'el_GR',
                'en_AU',
                'en_CA',
                'en_US',
                'es_ES',
                'fr_CA',
                'fr_FR',
                'it_IT',
                'nl_NL',
                'sr_RS',
                'sv_SE',
        );
        $this->assertEqualsCanonicalizing($default_locale_codes, array_keys($va_locale_list));
    }

    public function testGetLocaleListById() {
        $va_locale_list = ca_locales::getLocaleList(array('index_by_code' => false));
        $default_locale_ids = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14);
        $this->assertEqualsCanonicalizing($default_locale_ids, array_keys($va_locale_list));
    }

    public function testCodeToID() {
        $this->assertSame(1, ca_locales::codeToID('en_US'));
    }

    public function testCodeToIDNonNumericFails() {
        $this->assertNotSame("1", ca_locales::codeToID('en_US'));
    }

    public function testIDToName() {
        $this->assertEquals('English', ca_locales::IDToName(1));
    }

    public function testGetCataloguingLocaleList() {
        $this->assertEqualsCanonicalizing(array(1,2), array_keys(ca_locales::getCataloguingLocaleList()));
    }

    public function testGetCataloguingLocaleCodes() {
        $this->assertEqualsCanonicalizing(array('en_US', 'de_DE'), array_values(ca_locales::getCataloguingLocaleCodes()));
    }

    public function testNumberOfCataloguingLocales() {
        $this->assertEquals(2, ca_locales::numberOfCataloguingLocales());
    }
}
