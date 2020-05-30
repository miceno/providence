<?php
/** ---------------------------------------------------------------------
 * tests/helpers/LocaleHelpersTest.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2015 Whirl-i-Gig
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
 */

use PHPUnit\Framework\TestCase;

require_once(__CA_APP_DIR__ . "/helpers/CLIHelpers.php");

class LocaleHelpersTest extends TestCase {

    protected function setUp(): void {
    }

    /**
     * Delete all records we created for this test to avoid side effects with other tests
     */
    protected function tearDown() : void {
    }

    # -------------------------------------------------------
    public function testGetLanguageFromLocaleWithLanguage() {
        $locale = 'ca_ES';
        $country = caGetLanguageFromLocale($locale);
        $this->assertEquals('ca', $country);
    }

    # -------------------------------------------------------
    public function testGetLanguageFromLocaleWithEmptyLanguage() {
        $locale = 'es_';
        $country = caGetLanguageFromLocale($locale);
        $this->assertEquals('es', $country);
    }

    # -------------------------------------------------------
    public function testGetLanguageFromLocaleWithOnlyLanguage() {
        $locale = 'es';
        $country = caGetLanguageFromLocale($locale);
        $this->assertEquals('es', $country);
    }

    # -------------------------------------------------------
    public function testGetLanguageFromLocaleFromNullUsesDefaultLocale() {
        $locale = null;
        $country = caGetLanguageFromLocale($locale);
        $this->assertEquals('en', $country);
    }

}
