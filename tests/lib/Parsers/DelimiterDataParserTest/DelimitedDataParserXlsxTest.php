<?php
/** ---------------------------------------------------------------------
 * tests/lib/DelimitedDataParserXlsxTest.php
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
 */

require_once( __CA_LIB_DIR__ . '/Parsers/DelimitedDataParser.php' );
require_once( 'BaseDelimitedDataParser.php' );


class DelimitedDataParserXlsxTest extends BaseDelimitedDataParser {

	protected function setUp(): void {
		// Read XLSX File
		$this->file = __DIR__ . '/data/test.xlsx';
		$this->data = DelimitedDataParser::load( $this->file );
	}

	public function testFileType() {

		$this->assertNotNull( $this->data );
		$this->assertEquals( 'xlsx', $this->data->getType() );
	}

}
