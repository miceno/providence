<?php
/** ---------------------------------------------------------------------
 * tests/lib/Parsers/TimeExpressionParser_caTest.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2019 Whirl-i-Gig
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

require_once( __CA_LIB_DIR__ . '/Parsers/TimeExpressionParser.php' );

class TimeExpressionParser_caTest extends TestCase {

	/**
	 * @var TimeExpressionParser
	 */
	private $_timeParser = null;
	private $_va_roman_centuries = array( 'useRomanNumeralsForCenturies' => 1 );

	protected function setUp(): void {
		// most of the comparisons below rely on Eastern time zone
		date_default_timezone_set( 'America/New_York' );
		$ps_iso_code       = 'ca_ES';
		$this->_timeParser = new TimeExpressionParser();
		$this->_timeParser->setLanguage( $ps_iso_code );
	}

	public function testHyphensInSortOfOddPlaces() {

		$vb_res = $this->_timeParser->parse( 'c.1887-c.1918' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1887.010100000010" );
		$this->assertEquals( $va_parse['end'], "1918.123123595910" );
		$this->assertEquals( $va_parse[0], "1887.010100000010" );
		$this->assertEquals( $va_parse[1], "1918.123123595910" );
		$this->assertEquals( $this->_timeParser->getText(), "circa 1887 - 1918" );

		$vb_res = $this->_timeParser->parse( 'XIXè-segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1800.010100000000" );
		$this->assertEquals( $va_parse['end'], "1899.123123595900" );
		$this->assertEquals( $va_parse[0], "1800.010100000000" );
		$this->assertEquals( $va_parse[1], "1899.123123595900" );
		$this->assertEquals( $this->_timeParser->getText( $this->_va_roman_centuries ), "XIXè segle" );

		$vb_res = $this->_timeParser->parse( 'principis del XIXè segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1800.010100000000" );
		$this->assertEquals( $va_parse['end'], "1820.123123595900" );
		$this->assertEquals( $va_parse[0], "1800.010100000000" );
		$this->assertEquals( $va_parse[1], "1820.123123595900" );
		$this->assertEquals( $this->_timeParser->getText( $this->_va_roman_centuries ), "principis del XIXè segle" );

		$vb_res = $this->_timeParser->parse( 'començament del XIXè segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1800.010100000000" );
		$this->assertEquals( $va_parse['end'], "1820.123123595900" );
		$this->assertEquals( $va_parse[0], "1800.010100000000" );
		$this->assertEquals( $va_parse[1], "1820.123123595900" );
		$this->assertEquals( $this->_timeParser->getText( $this->_va_roman_centuries ), "principis del XIXè segle" );

		$vb_res = $this->_timeParser->parse( 'començaments del XIXè segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1800.010100000000" );
		$this->assertEquals( $va_parse['end'], "1820.123123595900" );
		$this->assertEquals( $va_parse[0], "1800.010100000000" );
		$this->assertEquals( $va_parse[1], "1820.123123595900" );
		$this->assertEquals( $this->_timeParser->getText( $this->_va_roman_centuries ), "principis del XIXè segle" );

		$vb_res = $this->_timeParser->parse( 'principis del XIXè segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1800.010100000000" );
		$this->assertEquals( $va_parse['end'], "1820.123123595900" );
		$this->assertEquals( $va_parse[0], "1800.010100000000" );
		$this->assertEquals( $va_parse[1], "1820.123123595900" );
		$this->assertEquals( $this->_timeParser->getText( $this->_va_roman_centuries ), "principis del XIXè segle" );
	}

	public function testQualifiedDecadeRanges() {
		$vb_res = $this->_timeParser->parse( '1920s - principis dels 1930s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1920.010100000000" );
		$this->assertEquals( $va_parse['end'], "1934.123123595900" );
		$this->assertEquals( $va_parse[0], "1920.010100000000" );
		$this->assertEquals( $va_parse[1], "1934.123123595900" );
		$this->assertEquals( $this->_timeParser->getText(), "principis del 1920s - principis del 1930s" );

		$vb_res = $this->_timeParser->parse( 'mig dels 1920s - principis dels 1930s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1923.010100000000" );
		$this->assertEquals( $va_parse['end'], "1934.123123595900" );
		$this->assertEquals( $va_parse[0], "1923.010100000000" );
		$this->assertEquals( $va_parse[1], "1934.123123595900" );
		// Don't output mid-decade dates and they're confusing to users
		$this->assertEquals( $this->_timeParser->getText(), "1923 - 1934" );

		$vb_res = $this->_timeParser->parse( 'meitat dels 1920s - principis dels 1930s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1923.010100000000" );
		$this->assertEquals( $va_parse['end'], "1934.123123595900" );
		$this->assertEquals( $va_parse[0], "1923.010100000000" );
		$this->assertEquals( $va_parse[1], "1934.123123595900" );
		// Don't output mid-decade dates and they're confusing to users
		$this->assertEquals( $this->_timeParser->getText(), "1923 - 1934" );

		$vb_res = $this->_timeParser->parse( 'finals del 1920s - principis dels 1930s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1926.010100000000" );
		$this->assertEquals( $va_parse['end'], "1934.123123595900" );
		$this->assertEquals( $va_parse[0], "1926.010100000000" );
		$this->assertEquals( $va_parse[1], "1934.123123595900" );
		$this->assertEquals( $this->_timeParser->getText(), "finals del 1920s - principis del 1930s" );

		$vb_res = $this->_timeParser->parse( 'principis dels 1920s - mig dels 1930s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1920.010100000000" );
		$this->assertEquals( $va_parse['end'], "1937.123123595900" );
		$this->assertEquals( $va_parse[0], "1920.010100000000" );
		$this->assertEquals( $va_parse[1], "1937.123123595900" );
		$this->assertEquals( $this->_timeParser->getText(), "1920 - 1937" );

		$vb_res = $this->_timeParser->parse( 'mig dels 1920s - mig dels 1930s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1923.010100000000" );
		$this->assertEquals( $va_parse['end'], "1937.123123595900" );
		$this->assertEquals( $va_parse[0], "1923.010100000000" );
		$this->assertEquals( $va_parse[1], "1937.123123595900" );
		$this->assertEquals( $this->_timeParser->getText(), "1923 - 1937" );

		$vb_res = $this->_timeParser->parse( 'finals dels 1920s - mig dels 1930s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1926.010100000000" );
		$this->assertEquals( $va_parse['end'], "1937.123123595900" );
		$this->assertEquals( $va_parse[0], "1926.010100000000" );
		$this->assertEquals( $va_parse[1], "1937.123123595900" );
		$this->assertEquals( $this->_timeParser->getText(), "1926 - 1937" );

		$vb_res = $this->_timeParser->parse( 'principis dels 1920s - finals dels 1930s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1920.010100000000" );
		$this->assertEquals( $va_parse['end'], "1939.123123595900" );
		$this->assertEquals( $va_parse[0], "1920.010100000000" );
		$this->assertEquals( $va_parse[1], "1939.123123595900" );
		$this->assertEquals( $this->_timeParser->getText(), "1920 - 1939" );

		$vb_res = $this->_timeParser->parse( 'mig dels 1920s - finals dels 1930s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1923.010100000000" );
		$this->assertEquals( $va_parse['end'], "1939.123123595900" );
		$this->assertEquals( $va_parse[0], "1923.010100000000" );
		$this->assertEquals( $va_parse[1], "1939.123123595900" );
		$this->assertEquals( $this->_timeParser->getText(), "1923 - 1939" );

		$vb_res = $this->_timeParser->parse( 'finals dels 1920s - finals dels 1930s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1926.010100000000" );
		$this->assertEquals( $va_parse['end'], "1939.123123595900" );
		$this->assertEquals( $va_parse[0], "1926.010100000000" );
		$this->assertEquals( $va_parse[1], "1939.123123595900" );
		$this->assertEquals( $this->_timeParser->getText(), "finals del 1920s - finals del 1930s" );

		$vb_res = $this->_timeParser->parse( 'principis del 1920s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1920.010100000000" );
		$this->assertEquals( $va_parse['end'], "1924.123123595900" );
		$this->assertEquals( $va_parse[0], "1920.010100000000" );
		$this->assertEquals( $va_parse[1], "1924.123123595900" );
		$this->assertEquals( $this->_timeParser->getText(), "principis del 1920s" );

		$vb_res = $this->_timeParser->parse( 'mig dels 1920s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1923.010100000000" );
		$this->assertEquals( $va_parse['end'], "1927.123123595900" );
		$this->assertEquals( $va_parse[0], "1923.010100000000" );
		$this->assertEquals( $va_parse[1], "1927.123123595900" );
		$this->assertEquals( $this->_timeParser->getText(), "mitjans del 1920s" );

		$vb_res = $this->_timeParser->parse( 'finals dels 1920s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1926.010100000000" );
		$this->assertEquals( $va_parse['end'], "1929.123123595900" );
		$this->assertEquals( $va_parse[0], "1926.010100000000" );
		$this->assertEquals( $va_parse[1], "1929.123123595900" );
		$this->assertEquals( $this->_timeParser->getText(), "finals del 1920s" );
	}

	public function testQualifiedCenturyRanges() {
		$vb_res = $this->_timeParser->parse( 'principis del XVIIIè segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1700.010100000000" );
		$this->assertEquals( $va_parse['end'], "1720.123123595900" );
		$this->assertEquals( $va_parse[0], "1700.010100000000" );
		$this->assertEquals( $va_parse[1], "1720.123123595900" );
		$this->assertEquals( $this->_timeParser->getText( $this->_va_roman_centuries ), "principis del XVIIIè segle" );

		$vb_res = $this->_timeParser->parse( 'mig dels XVIIIè segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1740.010100000000" );
		$this->assertEquals( $va_parse['end'], "1760.123123595900" );
		$this->assertEquals( $va_parse[0], "1740.010100000000" );
		$this->assertEquals( $va_parse[1], "1760.123123595900" );
		$this->assertEquals( $this->_timeParser->getText( $this->_va_roman_centuries ), "mitjans del XVIIIè segle" );

		$vb_res = $this->_timeParser->parse( 'finals del XVIIIè segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1780.010100000000" );
		$this->assertEquals( $va_parse['end'], "1799.123123595900" );
		$this->assertEquals( $va_parse[0], "1780.010100000000" );
		$this->assertEquals( $va_parse[1], "1799.123123595900" );
		$this->assertEquals( $this->_timeParser->getText( $this->_va_roman_centuries ), "finals del XVIIIè segle" );

		$vb_res = $this->_timeParser->parse( 'principis del XVIIIè segle - principis del XIXè segle',
			$this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1700.010100000000" );
		$this->assertEquals( $va_parse['end'], "1820.123123595900" );
		$this->assertEquals( $va_parse[0], "1700.010100000000" );
		$this->assertEquals( $va_parse[1], "1820.123123595900" );
		$this->assertEquals( $this->_timeParser->getText( $this->_va_roman_centuries ),
			"principis del XVIIIè segle - principis del XIXè segle" );

		$vb_res = $this->_timeParser->parse( 'mig dels XVIIIè segle - mig dels XIXè Segle',
			$this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1740.010100000000" );
		$this->assertEquals( $va_parse['end'], "1860.123123595900" );
		$this->assertEquals( $va_parse[0], "1740.010100000000" );
		$this->assertEquals( $va_parse[1], "1860.123123595900" );
		$this->assertEquals( $this->_timeParser->getText( $this->_va_roman_centuries ),
			"mitjans del XVIIIè segle - mitjans del XIXè segle" );

		$vb_res = $this->_timeParser->parse( 'finals dels XVè segle - mig dels XVIIè Segle',
			$this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1480.010100000000" );
		$this->assertEquals( $va_parse['end'], "1660.123123595900" );
		$this->assertEquals( $va_parse[0], "1480.010100000000" );
		$this->assertEquals( $va_parse[1], "1660.123123595900" );
		$this->assertEquals( $this->_timeParser->getText( $this->_va_roman_centuries ),
			"finals del XVè segle - mitjans del XVIIè segle" );

		$vb_res = $this->_timeParser->parse( 'finals dels XVè segle - finals dels XVIIè Segle',
			$this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1480.010100000000" );
		$this->assertEquals( $va_parse['end'], "1699.123123595900" );
		$this->assertEquals( $va_parse[0], "1480.010100000000" );
		$this->assertEquals( $va_parse[1], "1699.123123595900" );
		$this->assertEquals( $this->_timeParser->getText( $this->_va_roman_centuries ),
			"finals del XVè segle - finals del XVIIè segle" );
	}

	public function testCenturyRange() {

		$vb_res = $this->_timeParser->parse( 'XVIIIè segle - XIXè Segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1700.010100000000" );
		$this->assertEquals( $va_parse['end'], "1899.123123595900" );
		$this->assertEquals( $va_parse[0], "1700.010100000000" );
		$this->assertEquals( $va_parse[1], "1899.123123595900" );

		$vb_res = $this->_timeParser->parse( 'XVè segle - XXè Segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1400.010100000000" );
		$this->assertEquals( $va_parse['end'], "1999.123123595900" );
		$this->assertEquals( $va_parse[0], "1400.010100000000" );
		$this->assertEquals( $va_parse[1], "1999.123123595900" );
	}

	public function testPrePostDates() {

		$vb_res = $this->_timeParser->parse( 'abans 1600' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "-2000000000.000000000000" );
		$this->assertEquals( $va_parse['end'], "1600.123123595900" );
		$this->assertEquals( $va_parse[0], "-2000000000.000000000000" );
		$this->assertEquals( $va_parse[1], "1600.123123595900" );

		$vb_res = $this->_timeParser->parse( 'després 1600' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1600.010100000000" );
		$this->assertEquals( $va_parse['end'], "2000000000.123123595900" );
		$this->assertEquals( $va_parse[0], "1600.010100000000" );
		$this->assertEquals( $va_parse[1], "2000000000.123123595900" );

	}

	public function testModifiersWithoutTrailingSpaces() {

		$vb_res = $this->_timeParser->parse( 'c1959' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1959.010100000010" );
		$this->assertEquals( $va_parse['end'], "1959.123123595910" );
		$this->assertEquals( $va_parse[0], "1959.010100000010" );
		$this->assertEquals( $va_parse[1], "1959.123123595910" );

		$vb_res = $this->_timeParser->parse( 'c.1959' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1959.010100000010" );
		$this->assertEquals( $va_parse['end'], "1959.123123595910" );
		$this->assertEquals( $va_parse[0], "1959.010100000010" );
		$this->assertEquals( $va_parse[1], "1959.123123595910" );
	}

	public function testBPDates() {

		$vb_res = $this->_timeParser->parse( '55 BP' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1895.010100000080" );    # Date attribute 8=BP
		$this->assertEquals( $va_parse['end'], "1895.123123595980" );
		$this->assertEquals( $va_parse[0], "1895.010100000080" );
		$this->assertEquals( $va_parse[1], "1895.123123595980" );

		$vb_res = $this->_timeParser->parse( '7000 BP' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "-5050.010100000080" );    # Date attribute 8=BP
		$this->assertEquals( $va_parse['end'], "-5050.123123595980" );
		$this->assertEquals( $va_parse[0], "-5050.010100000080" );
		$this->assertEquals( $va_parse[1], "-5050.123123595980" );

		$vb_res = $this->_timeParser->parse( 'pre-1600' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "-2000000000.000000000000" );
		$this->assertEquals( $va_parse['end'], "1600.123123595900" );
		$this->assertEquals( $va_parse[0], "-2000000000.000000000000" );
		$this->assertEquals( $va_parse[1], "1600.123123595900" );

		$vb_res = $this->_timeParser->parse( 'post-1600' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1600.010100000000" );
		$this->assertEquals( $va_parse['end'], "2000000000.123123595900" );
		$this->assertEquals( $va_parse[0], "1600.010100000000" );
		$this->assertEquals( $va_parse[1], "2000000000.123123595900" );
	}

	public function testDatesWithoutStart() {

		$vb_res = $this->_timeParser->parse( '- 5/6/1950' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "-2000000000.000000000000" );
		$this->assertEquals( $va_parse['end'], "1950.060523595900" );
		$this->assertEquals( $va_parse[0], "-2000000000.000000000000" );
		$this->assertEquals( $va_parse[1], "1950.060523595900" );

		$vb_res = $this->_timeParser->parse( '- 3/2010' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "-2000000000.000000000000" );
		$this->assertEquals( $va_parse['end'], "2010.033123595900" );
		$this->assertEquals( $va_parse[0], "-2000000000.000000000000" );
		$this->assertEquals( $va_parse[1], "2010.033123595900" );

		$vb_res = $this->_timeParser->parse( '- 3/12' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "-2000000000.000000000000" );
		$this->assertEquals( $va_parse['end'], date( "Y" ) . ".031223595900" );
		$this->assertEquals( $va_parse[0], "-2000000000.000000000000" );
		$this->assertEquals( $va_parse[1], date( "Y" ) . ".031223595900" );

		$vb_res = $this->_timeParser->parse( '- 3/99' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "-2000000000.000000000000" );
		$this->assertEquals( $va_parse['end'], "1999.033123595900" );
		$this->assertEquals( $va_parse[0], "-2000000000.000000000000" );
		$this->assertEquals( $va_parse[1], "1999.033123595900" );

		$vb_res = $this->_timeParser->parse( '- 1950' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "-2000000000.000000000000" );
		$this->assertEquals( $va_parse['end'], "1950.123123595900" );
		$this->assertEquals( $va_parse[0], "-2000000000.000000000000" );
		$this->assertEquals( $va_parse[1], "1950.123123595900" );
	}

	public function testExifDates() {

		$vb_res = $this->_timeParser->parse( '2015:07:15 14:29:17.49' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "2015.071514291700" );
		$this->assertEquals( $va_parse['end'], "2015.071514291700" );
		$this->assertEquals( $va_parse[0], "2015.071514291700" );
		$this->assertEquals( $va_parse[1], "2015.071514291700" );

		$va_parse = $this->_timeParser->getUnixTimestamps();

		$this->assertEquals( $va_parse['start'], "1436984957" );
		$this->assertEquals( $va_parse['end'], "1436984957" );
		$this->assertEquals( $va_parse[0], "1436984957" );
		$this->assertEquals( $va_parse[1], "1436984957" );
	}

	public function testUnknownYearAACR2() {

		$vb_res = $this->_timeParser->parse( '199-' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( "1990.010100000000", $va_parse['start'] );
		$this->assertEquals( "1999.123123595900", $va_parse['end'] );
	}

	public function testUncertainDates() {

		$vb_res = $this->_timeParser->parse( '199?' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( "199.010100000010", $va_parse['start'] );
		$this->assertEquals( "199.123123595910", $va_parse['end'] );
	}

	public function testEarlyCEDatesWithoutEra() {

		$vb_res = $this->_timeParser->parse( '22/12/199' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( "199.122200000000", $va_parse['start'] );
		$this->assertEquals( "199.122223595900", $va_parse['end'] );
	}

	public function testEarlyCEDatesWithoutEraAussieStyle() {
		$this->_timeParser = new TimeExpressionParser();
		$this->_timeParser->setLanguage( 'en_AU' );

		$vb_res = $this->_timeParser->parse( '22/12/199' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( "199.122200000000", $va_parse['start'] );
		$this->assertEquals( "199.122223595900", $va_parse['end'] );

		$vb_res = $this->_timeParser->parse( '22.12.199' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( "199.122200000000", $va_parse['start'] );
		$this->assertEquals( "199.122223595900", $va_parse['end'] );
	}

	public function testImplicitCenturyDates() {

		$vb_res = $this->_timeParser->parse( '19/2/16' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( "2016.021900000000", $va_parse['start'] );
		$this->assertEquals( "2016.021923595900", $va_parse['end'] );
	}

	public function testQuarterCenturyDates() {

		$vb_res = $this->_timeParser->parse( '20 Q2' );    // 2nd quarter of XXè segle
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1925.010100000000" );
		$this->assertEquals( $va_parse['end'], "1950.123123595900" );
		$this->assertEquals( $va_parse[0], "1925.010100000000" );
		$this->assertEquals( $va_parse[1], "1950.123123595900" );

		$vb_res = $this->_timeParser->parse( '1 Q4' );        // 4th quarter of 1st century
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "75.010100000000" );
		$this->assertEquals( $va_parse['end'], "100.123123595900" );
		$this->assertEquals( $va_parse[0], "75.010100000000" );
		$this->assertEquals( $va_parse[1], "100.123123595900" );

	}

	public function testYearlessDates() {

		$vb_res = $this->_timeParser->parse( '24/10/??' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "0.102400000000" );
		$this->assertEquals( $va_parse['end'], "0.102423595900" );
		$this->assertEquals( $va_parse[0], "0.102400000000" );
		$this->assertEquals( $va_parse[1], "0.102423595900" );
		$this->assertEquals( $this->_timeParser->getText(), "24/10/????" );
	}

	public function testRangeSpanningEras() {

		$vb_res = $this->_timeParser->parse( '88 a.C. - 55 d.C.' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], - 88.0101000000 );
		$this->assertEquals( $va_parse['end'], 55.1231235959 );
		$this->assertEquals( $va_parse[0], - 88.0101000000 );
		$this->assertEquals( $va_parse[1], 55.1231235959 );

		$vb_res = $this->_timeParser->parse( '50 a.C. - 10 d.C.' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], - 50.0101000000 );
		$this->assertEquals( $va_parse['end'], 10.1231235959 );
		$this->assertEquals( $va_parse[0], - 50.0101000000 );
		$this->assertEquals( $va_parse[1], 10.1231235959 );
	}

	public function testRangeInFirstCentury() {

		$vb_res = $this->_timeParser->parse( '50 d.C. - 80 d.C.' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], 50.0101000000 );
		$this->assertEquals( $va_parse['end'], 80.1231235959 );
		$this->assertEquals( $va_parse[0], 50.0101000000 );
		$this->assertEquals( $va_parse[1], 80.1231235959 );
	}

	public function testEarlyCEDatesWithEra() {

		$vb_res = $this->_timeParser->parse( '22/12/99 EC' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( "99.122200000000", $va_parse['start'] );
		$this->assertEquals( "99.122223595900", $va_parse['end'] );
	}

	public function testSeasonDates() {

		$vb_res = $this->_timeParser->parse( 'Hivern 2010' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "2010.122100000000" );
		$this->assertEquals( $va_parse['end'], "2011.032023595900" );
		$this->assertEquals( $va_parse[0], "2010.122100000000" );
		$this->assertEquals( $va_parse[1], "2011.032023595900" );
	}

	public function testParseSimpleDelimitedDateForEnglishLocale() {

		$vb_res = $this->_timeParser->parse( '23/10/2004' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getUnixTimestamps();
		$this->assertEquals( $va_parse['start'], 1098504000 );
		$this->assertEquals( $va_parse['end'], 1098590399 );
		$this->assertEquals( $va_parse[0], 1098504000 );
		$this->assertEquals( $va_parse[1], 1098590399 );
	}

	public function testParseSimpleDelimitedDateForEuropeanLocale() {
		$this->_timeParser = new TimeExpressionParser();
		$this->_timeParser->setLanguage( 'fr_FR' );
		$vb_res = $this->_timeParser->parse( '23/10/2004' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getUnixTimestamps();

		$this->assertEquals( $va_parse['start'], 1098504000 );
		$this->assertEquals( $va_parse['end'], 1098590399 );
		$this->assertEquals( $va_parse[0], 1098504000 );
		$this->assertEquals( $va_parse[1], 1098590399 );
	}

	public function testParseTextDate() {

		$vb_res = $this->_timeParser->parse( 'maig 10 1990' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getUnixTimestamps();

		$this->assertEquals( $va_parse['start'], 642312000 );
		$this->assertEquals( $va_parse['end'], 642398399 );
		$this->assertEquals( $va_parse[0], 642312000 );
		$this->assertEquals( $va_parse[1], 642398399 );

		$vb_res = $this->_timeParser->parse( '10 de maig de 1990' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getUnixTimestamps();

		$this->assertEquals( $va_parse['start'], 642312000 );
		$this->assertEquals( $va_parse['end'], 642398399 );
		$this->assertEquals( $va_parse[0], 642312000 );
		$this->assertEquals( $va_parse[1], 642398399 );
	}

	public function testParseISO8601Date() {
		$this->_timeParser = new TimeExpressionParser();
		$vb_res            = $this->_timeParser->parse( '2009-09-18 21:03' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getUnixTimestamps();

		$this->assertEquals( $va_parse['start'], 1253322180 );
		$this->assertEquals( $va_parse['end'], 1253322180 );
		$this->assertEquals( $va_parse[0], 1253322180 );
		$this->assertEquals( $va_parse[1], 1253322180 );
	}

	public function testParseNowDate() {
		$this->_timeParser = new TimeExpressionParser();
		$this->_timeParser->setLanguage( 'ca_ES' );
		$vb_res = $this->_timeParser->parse( 'ara' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getUnixTimestamps();

		$this->assertEquals( $va_parse['start'], $t = time() );
		$this->assertEquals( $va_parse['end'], $t );
		$this->assertEquals( $va_parse[0], $t );
		$this->assertEquals( $va_parse[1], $t );
	}

	public function testParseTodayDate() {
		$this->_timeParser = new TimeExpressionParser();
		$this->_timeParser->setLanguage( 'ca_ES' );
		$vb_res = $this->_timeParser->parse( 'avui' );
		$this->assertEquals( $vb_res, true );

		$va_parse   = $this->_timeParser->getHistoricTimestamps();
		$today_date = date( 'Y.md' );

		$this->assertEquals( $today_date, substr( $va_parse['start'], 0, 9 ) );
		$this->assertEquals( $today_date, substr( $va_parse['end'], 0, 9 ) );
	}

	public function testParseYesterdayDate() {
		$this->_timeParser = new TimeExpressionParser();
		$this->_timeParser->setLanguage( 'ca_ES' );
		$vb_res = $this->_timeParser->parse( 'ahir' );
		$this->assertEquals( $vb_res, true );

		$va_parse       = $this->_timeParser->getHistoricTimestamps();
		$yesterday_date = date( "Y.md", strtotime( 'yesterday' ) );

		$this->assertEquals( $yesterday_date, substr( $va_parse['start'], 0, 9 ) );
		$this->assertEquals( $yesterday_date, substr( $va_parse['end'], 0, 9 ) );

	}

	public function testParseTomorrowDate() {
		$this->_timeParser = new TimeExpressionParser();
		$this->_timeParser->setLanguage( 'ca_ES' );
		$vb_res = $this->_timeParser->parse( 'demà' );
		$this->assertEquals( $vb_res, true );

		$va_parse      = $this->_timeParser->getHistoricTimestamps();
		$tomorrow_date = date( "Y.md", strtotime( 'tomorrow' ) );

		$this->assertEquals( $tomorrow_date, substr( $va_parse['start'], 0, 9 ) );
		$this->assertEquals( $tomorrow_date, substr( $va_parse['end'], 0, 9 ) );
	}

	public function testHistoricDayDateForEnglishLocale() {

		$vb_res = $this->_timeParser->parse( '8 de març de 1945' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1945.030800000000" );
		$this->assertEquals( $va_parse['end'], "1945.030823595900" );
		$this->assertEquals( $va_parse[0], "1945.030800000000" );
		$this->assertEquals( $va_parse[1], "1945.030823595900" );
	}

	public function testHistoricDayDateForGermanLocale() {
		$this->_timeParser = new TimeExpressionParser();
		$this->_timeParser->setLanguage( 'de_DE' );
		$vb_res = $this->_timeParser->parse( '8. Mai 1945' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1945.050800000000" );
		$this->assertEquals( $va_parse['end'], "1945.050823595900" );
		$this->assertEquals( $va_parse[0], "1945.050800000000" );
		$this->assertEquals( $va_parse[1], "1945.050823595900" );
	}

	public function testInvalidMonthDateForGermanLocale() {
		$this->_timeParser = new TimeExpressionParser();
		$this->_timeParser->setLanguage( 'de_DE' );
		$vb_res = $this->_timeParser->parse( '24.13.14' );
		$this->assertEquals( $vb_res, false );
	}

	public function testHistoricDayDateWithUmlautForGermanLocale() {
		$this->_timeParser = new TimeExpressionParser();
		$this->_timeParser->setLanguage( 'de_DE' );
		$vb_res = $this->_timeParser->parse( '11. März 1870' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1870.031100000000" );
		$this->assertEquals( $va_parse['end'], "1870.031123595900" );
		$this->assertEquals( $va_parse[0], "1870.031100000000" );
		$this->assertEquals( $va_parse[1], "1870.031123595900" );
	}

	public function testHistoricDayDateWithUmlautForFrenchLocale() {
		$this->_timeParser = new TimeExpressionParser();
		$this->_timeParser->setLanguage( 'fr_FR' );
		$vb_res = $this->_timeParser->parse( '24 Décembre 1870' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1870.122400000000" );
		$this->assertEquals( $va_parse['end'], "1870.122423595900" );
	}

	public function testCenturyDatesForGermanLocale() {
		$this->_timeParser = new TimeExpressionParser();
		$this->_timeParser->setLanguage( 'de_DE' );
		$vb_res = $this->_timeParser->parse( '20. Jahrhundert' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1900.010100000000" );
		$this->assertEquals( $va_parse['end'], "1999.123123595900" );
		$this->assertEquals( $va_parse[0], "1900.010100000000" );
		$this->assertEquals( $va_parse[1], "1999.123123595900" );
	}

	public function testFullDateWith3DigitYear() {

		$vb_res = $this->_timeParser->parse( '17 de gener de 999' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "999.011700000000" );
		$this->assertEquals( $va_parse['end'], "999.011723595900" );

		$vb_res = $this->_timeParser->parse( '17 gener 999' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "999.011700000000" );
		$this->assertEquals( $va_parse['end'], "999.011723595900" );

		$vb_res = $this->_timeParser->parse( '17/1/999' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "999.011700000000" );
		$this->assertEquals( $va_parse['end'], "999.011723595900" );

		$this->_timeParser->setLanguage( 'de_DE' );
		$vb_res = $this->_timeParser->parse( '17.1.999' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "999.011700000000" );
		$this->assertEquals( $va_parse['end'], "999.011723595900" );
	}

	public function testHistoricYearRanges() {

		$vb_res = $this->_timeParser->parse( '1930 - 1946' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1930.010100000000" );
		$this->assertEquals( $va_parse['end'], "1946.123123595900" );
		$this->assertEquals( $va_parse[0], "1930.010100000000" );
		$this->assertEquals( $va_parse[1], "1946.123123595900" );

		$vb_res = $this->_timeParser->parse( '1930-1946' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1930.010100000000" );
		$this->assertEquals( $va_parse['end'], "1946.123123595900" );
		$this->assertEquals( $va_parse[0], "1930.010100000000" );
		$this->assertEquals( $va_parse[1], "1946.123123595900" );

		$vb_res = $this->_timeParser->parse( '1951 - 1955' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1951.010100000000" );
		$this->assertEquals( $va_parse['end'], "1955.123123595900" );
		$this->assertEquals( $va_parse[0], "1951.010100000000" );
		$this->assertEquals( $va_parse[1], "1955.123123595900" );

		$vb_res = $this->_timeParser->parse( 'Entre 1951 i 1955' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1951.010100000000" );
		$this->assertEquals( $va_parse['end'], "1955.123123595900" );
		$this->assertEquals( $va_parse[0], "1951.010100000000" );
		$this->assertEquals( $va_parse[1], "1955.123123595900" );

		$vb_res = $this->_timeParser->parse( 'Des de 1951 - 1955' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1951.010100000000" );
		$this->assertEquals( $va_parse['end'], "1955.123123595900" );
		$this->assertEquals( $va_parse[0], "1951.010100000000" );
		$this->assertEquals( $va_parse[1], "1955.123123595900" );

		$vb_res = $this->_timeParser->parse( 'Des de 1951 fins 1955' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1951.010100000000" );
		$this->assertEquals( $va_parse['end'], "1955.123123595900" );
		$this->assertEquals( $va_parse[0], "1951.010100000000" );
		$this->assertEquals( $va_parse[1], "1955.123123595900" );
	}

	public function testCircaDateRanges() {

		$vb_res = $this->_timeParser->parse( 'circa 1950 - 1955' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1950.010100000010" );
		$this->assertEquals( $va_parse['end'], "1955.123123595910" );
		$this->assertEquals( $va_parse[0], "1950.010100000010" );
		$this->assertEquals( $va_parse[1], "1955.123123595910" );

		$vb_res = $this->_timeParser->parse( 'circa 6/1950 - 1955' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1950.060100000010" );
		$this->assertEquals( $va_parse['end'], "1955.123123595910" );
		$this->assertEquals( $va_parse[0], "1950.060100000010" );
		$this->assertEquals( $va_parse[1], "1955.123123595910" );
	}

	public function testCircaEndDateRanges() {

		$vb_res = $this->_timeParser->parse( '1950 - circa 1955' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1950.010100000000" );
		$this->assertEquals( $va_parse['end'], "1955.123123595910" );
		$this->assertEquals( $va_parse[0], "1950.010100000000" );
		$this->assertEquals( $va_parse[1], "1955.123123595910" );
		$this->assertEquals( $this->_timeParser->getText(), "1950 - circa 1955" );

		$vb_res = $this->_timeParser->parse( '6/1950 - circa 1955' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1950.060100000000" );
		$this->assertEquals( $va_parse['end'], "1955.123123595910" );
		$this->assertEquals( $va_parse[0], "1950.060100000000" );
		$this->assertEquals( $va_parse[1], "1955.123123595910" );
		$this->assertEquals( $this->_timeParser->getText(), "juny 1950 - circa desembre 1955" );

		$vb_res = $this->_timeParser->parse( 'circa juny 1950 - circa 1955' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1950.060100000010" );
		$this->assertEquals( $va_parse['end'], "1955.123123595910" );
		$this->assertEquals( $va_parse[0], "1950.060100000010" );
		$this->assertEquals( $va_parse[1], "1955.123123595910" );
		$this->assertEquals( $this->_timeParser->getText(), "circa juny 1950 - desembre 1955" );

		$vb_res = $this->_timeParser->parse( 'circa juny 1950 - circa 11/1955' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1950.060100000010" );
		$this->assertEquals( $va_parse['end'], "1955.113023595910" );
		$this->assertEquals( $va_parse[0], "1950.060100000010" );
		$this->assertEquals( $va_parse[1], "1955.113023595910" );
		$this->assertEquals( $this->_timeParser->getText(), "circa juny 1950 - novembre 1955" );

		$vb_res = $this->_timeParser->parse( 'circa juny 1950 - 11/1950' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1950.060100000010" );
		$this->assertEquals( $va_parse['end'], "1950.113023595910" );
		$this->assertEquals( $va_parse[0], "1950.060100000010" );
		$this->assertEquals( $va_parse[1], "1950.113023595910" );
		$this->assertEquals( $this->_timeParser->getText(), "circa juny - novembre 1950" );

	}

	public function testDecadeRanges() {

		$vb_res = $this->_timeParser->parse( '1950s - 1970s' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1950.010100000000" );
		$this->assertEquals( $va_parse['end'], "1979.123123595900" );
		$this->assertEquals( $va_parse[0], "1950.010100000000" );
		$this->assertEquals( $va_parse[1], "1979.123123595900" );
	}

	public function testCircaDecade() {

		$vb_res = $this->_timeParser->parse( 'circa 1950s' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1950.010100000010" );
		$this->assertEquals( $va_parse['end'], "1959.123123595910" );
		$this->assertEquals( $va_parse[0], "1950.010100000010" );
		$this->assertEquals( $va_parse[1], "1959.123123595910" );
	}

	public function testCircaDecadeRanges() {

		$vb_res = $this->_timeParser->parse( 'circa 1950s - 1970s' );
		$this->assertEquals( $vb_res, true );

		$va_parse = $this->_timeParser->getHistoricTimestamps();
		$this->assertEquals( $va_parse['start'], "1950.010100000010" );
		$this->assertEquals( $va_parse['end'], "1979.123123595910" );
		$this->assertEquals( $va_parse[0], "1950.010100000010" );
		$this->assertEquals( $va_parse[1], "1979.123123595910" );
	}

	public function testHistoricCircaYearDate() {

		$vb_res = $this->_timeParser->parse( 'c 1946' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1946.010100000010" );
		$this->assertEquals( $va_parse['end'], "1946.123123595910" );
		$this->assertEquals( $va_parse[0], "1946.010100000010" );
		$this->assertEquals( $va_parse[1], "1946.123123595910" );

		$vb_res = $this->_timeParser->parse( 'c. 1946' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1946.010100000010" );
		$this->assertEquals( $va_parse['end'], "1946.123123595910" );
		$this->assertEquals( $va_parse[0], "1946.010100000010" );
		$this->assertEquals( $va_parse[1], "1946.123123595910" );

		$vb_res = $this->_timeParser->parse( 'circa 1946' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1946.010100000010" );
		$this->assertEquals( $va_parse['end'], "1946.123123595910" );
		$this->assertEquals( $va_parse[0], "1946.010100000010" );
		$this->assertEquals( $va_parse[1], "1946.123123595910" );

		$vb_res = $this->_timeParser->parse( 'ca 1946' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1946.010100000010" );
		$this->assertEquals( $va_parse['end'], "1946.123123595910" );
		$this->assertEquals( $va_parse[0], "1946.010100000010" );
		$this->assertEquals( $va_parse[1], "1946.123123595910" );

		$vb_res = $this->_timeParser->parse( 'ca. 1946' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1946.010100000010" );
		$this->assertEquals( $va_parse['end'], "1946.123123595910" );
		$this->assertEquals( $va_parse[0], "1946.010100000010" );
		$this->assertEquals( $va_parse[1], "1946.123123595910" );

		$vb_res = $this->_timeParser->parse( '1946?' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1946.010100000010" );
		$this->assertEquals( $va_parse['end'], "1946.123123595910" );
		$this->assertEquals( $va_parse[0], "1946.010100000010" );
		$this->assertEquals( $va_parse[1], "1946.123123595910" );
	}

	public function testHistoricCircaMonthAndYearDate() {

		$vb_res = $this->_timeParser->parse( 'c juny 1946' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1946.060100000010" );
		$this->assertEquals( $va_parse['end'], "1946.063023595910" );
		$this->assertEquals( $va_parse[0], "1946.060100000010" );
		$this->assertEquals( $va_parse[1], "1946.063023595910" );

		$vb_res = $this->_timeParser->parse( 'c. 6/1946' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1946.060100000010" );
		$this->assertEquals( $va_parse['end'], "1946.063023595910" );
		$this->assertEquals( $va_parse[0], "1946.060100000010" );
		$this->assertEquals( $va_parse[1], "1946.063023595910" );

		$vb_res = $this->_timeParser->parse( 'circa juny 1946' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1946.060100000010" );
		$this->assertEquals( $va_parse['end'], "1946.063023595910" );
		$this->assertEquals( $va_parse[0], "1946.060100000010" );
		$this->assertEquals( $va_parse[1], "1946.063023595910" );

		$vb_res = $this->_timeParser->parse( 'ca 6/1946' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1946.060100000010" );
		$this->assertEquals( $va_parse['end'], "1946.063023595910" );
		$this->assertEquals( $va_parse[0], "1946.060100000010" );
		$this->assertEquals( $va_parse[1], "1946.063023595910" );

		$vb_res = $this->_timeParser->parse( 'ca. juny 1946' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1946.060100000010" );
		$this->assertEquals( $va_parse['end'], "1946.063023595910" );
		$this->assertEquals( $va_parse[0], "1946.060100000010" );
		$this->assertEquals( $va_parse[1], "1946.063023595910" );

		$vb_res = $this->_timeParser->parse( '6/1946?' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1946.060100000010" );
		$this->assertEquals( $va_parse['end'], "1946.063023595910" );
		$this->assertEquals( $va_parse[0], "1946.060100000010" );
		$this->assertEquals( $va_parse[1], "1946.063023595910" );
	}

	public function testDecadeDate() {
		$this->_timeParser = new TimeExpressionParser();
		$vb_res            = $this->_timeParser->parse( '1950s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1950.010100000000" );
		$this->assertEquals( $va_parse['end'], "1959.123123595900" );
		$this->assertEquals( $va_parse[0], "1950.010100000000" );
		$this->assertEquals( $va_parse[1], "1959.123123595900" );

		$vb_res = $this->_timeParser->parse( '1950\'s' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1950.010100000000" );
		$this->assertEquals( $va_parse['end'], "1959.123123595900" );
		$this->assertEquals( $va_parse[0], "1950.010100000000" );
		$this->assertEquals( $va_parse[1], "1959.123123595900" );

		$vb_res = $this->_timeParser->parse( '195-' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1950.010100000000" );
		$this->assertEquals( $va_parse['end'], "1959.123123595900" );
		$this->assertEquals( $va_parse[0], "1950.010100000000" );
		$this->assertEquals( $va_parse[1], "1959.123123595900" );
	}

	public function testCenturyDates() {

		$vb_res = $this->_timeParser->parse( 'XXè segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1900.010100000000" );
		$this->assertEquals( $va_parse['end'], "1999.123123595900" );
		$this->assertEquals( $va_parse[0], "1900.010100000000" );
		$this->assertEquals( $va_parse[1], "1999.123123595900" );

		$vb_res = $this->_timeParser->parse( '19--' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1900.010100000000" );
		$this->assertEquals( $va_parse['end'], "1999.123123595900" );
		$this->assertEquals( $va_parse[0], "1900.010100000000" );
		$this->assertEquals( $va_parse[1], "1999.123123595900" );
	}

	public function testADBCDates() {

		$vb_res = $this->_timeParser->parse( '2000ac' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], - "2000.010100000000" );
		$this->assertEquals( $va_parse['end'], - "2000.123123595900" );
		$this->assertEquals( $va_parse[0], - "2000.010100000000" );
		$this->assertEquals( $va_parse[1], - "2000.123123595900" );

		$vb_res = $this->_timeParser->parse( '2000dc' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "2000.010100000000" );
		$this->assertEquals( $va_parse['end'], "2000.123123595900" );
		$this->assertEquals( $va_parse[0], "2000.010100000000" );
		$this->assertEquals( $va_parse[1], "2000.123123595900" );

		// Negative year BCE format
		$vb_res = $this->_timeParser->parse( '-2150' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], - "2150.010100000000" );
		$this->assertEquals( $va_parse['end'], - "2150.123123595900" );
		$this->assertEquals( $va_parse[0], - "2150.010100000000" );
		$this->assertEquals( $va_parse[1], - "2150.123123595900" );
	}

	public function testADCenturies() {
		$vb_res = $this->_timeParser->parse( 'IIn segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "100.010100000000" );
		$this->assertEquals( $va_parse['end'], "199.123123595900" );
		$this->assertEquals( $va_parse[0], "100.010100000000" );
		$this->assertEquals( $va_parse[1], "199.123123595900" );
		$this->assertEquals( mb_strtolower( $this->_timeParser->getText( $this->_va_roman_centuries ) ),
			mb_strtolower( "IIn segle" ) );

		$vb_res = $this->_timeParser->parse( 'IIè segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "100.010100000000" );
		$this->assertEquals( $va_parse['end'], "199.123123595900" );
		$this->assertEquals( $va_parse[0], "100.010100000000" );
		$this->assertEquals( $va_parse[1], "199.123123595900" );
		$this->assertEquals( mb_strtolower( $this->_timeParser->getText( $this->_va_roman_centuries ) ),
			mb_strtolower( "IIn segle" ) );

		$vb_res = $this->_timeParser->parse( 'IIn segle dc', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "100.010100000000" );
		$this->assertEquals( $va_parse['end'], "199.123123595900" );
		$this->assertEquals( $va_parse[0], "100.010100000000" );
		$this->assertEquals( $va_parse[1], "199.123123595900" );
		$this->assertEquals( mb_strtolower( $this->_timeParser->getText( $this->_va_roman_centuries ) ),
			mb_strtolower( "IIn segle" ) );

		$vb_res = $this->_timeParser->parse( 'XVè segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1400.010100000000" );
		$this->assertEquals( $va_parse['end'], "1499.123123595900" );
		$this->assertEquals( $va_parse[0], "1400.010100000000" );
		$this->assertEquals( $va_parse[1], "1499.123123595900" );
		$this->assertEquals( mb_strtolower( $this->_timeParser->getText( $this->_va_roman_centuries ) ),
			mb_strtolower( "XVè segle" ) );

		$vb_res = $this->_timeParser->parse( 'XVè segle dc', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "1400.010100000000" );
		$this->assertEquals( $va_parse['end'], "1499.123123595900" );
		$this->assertEquals( $va_parse[0], "1400.010100000000" );
		$this->assertEquals( $va_parse[1], "1499.123123595900" );
		$this->assertEquals( mb_strtolower( $this->_timeParser->getText( $this->_va_roman_centuries ) ),
			mb_strtolower( "XVè segle" ) );
	}

	public function testBCECenturies() {

		$vb_res = $this->_timeParser->parse( 'Finals del Vè segle AC', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "-420.010100000000" );
		$this->assertEquals( $va_parse['end'], "-420.123123595900" );
		$this->assertEquals( $va_parse[0], "-420.010100000000" );
		$this->assertEquals( $va_parse[1], "-420.123123595900" );

		$vb_res = $this->_timeParser->parse( '600AC-500 AC', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "-600.010100000000" );
		$this->assertEquals( $va_parse['end'], "-500.123123595900" );
		$this->assertEquals( $va_parse[0], "-600.010100000000" );
		$this->assertEquals( $va_parse[1], "-500.123123595900" );

		$vb_res = $this->_timeParser->parse( 'vuitè segle ac-300ac', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "-799.010100000000" );
		$this->assertEquals( $va_parse['end'], "-300.123123595900" );
		$this->assertEquals( $va_parse[0], "-799.010100000000" );
		$this->assertEquals( $va_parse[1], "-300.123123595900" );

		$vb_res = $this->_timeParser->parse( 'IIn segle aec', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "-199.010100000000" );
		$this->assertEquals( $va_parse['end'], "-100.123123595900" );
		$this->assertEquals( $va_parse[0], "-199.010100000000" );
		$this->assertEquals( $va_parse[1], "-100.123123595900" );

		$this->assertEquals( mb_strtolower( $this->_timeParser->getText( $this->_va_roman_centuries ) ),
			mb_strtolower( "IIn segle ac" ) );

		$vb_res = $this->_timeParser->parse( 'XVè segle aec', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "-1499.010100000000" );
		$this->assertEquals( $va_parse['end'], "-1400.123123595900" );
		$this->assertEquals( $va_parse[0], "-1499.010100000000" );
		$this->assertEquals( $va_parse[1], "-1400.123123595900" );

		$this->assertEquals( mb_strtolower( $this->_timeParser->getText( $this->_va_roman_centuries ) ),
			mb_strtolower( "XVè segle ac" ) );

		$vb_res = $this->_timeParser->parse( 'Iè segle aec', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "-99.010100000000" );
		$this->assertEquals( $va_parse['end'], "0.123123595900" );
		$this->assertEquals( $va_parse[0], "-99.010100000000" );
		$this->assertEquals( $va_parse[1], "0.123123595900" );

		$this->assertEquals( mb_strtolower( $this->_timeParser->getText( $this->_va_roman_centuries ) ),
			mb_strtolower( "Ir segle ac" ) );

		$vb_res = $this->_timeParser->parse( 'Iè segle', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "0.010100000000" );
		$this->assertEquals( $va_parse['end'], "99.123123595900" );
		$this->assertEquals( $va_parse[0], "0.010100000000" );
		$this->assertEquals( $va_parse[1], "99.123123595900" );

		$this->assertEquals( mb_strtolower( $this->_timeParser->getText( $this->_va_roman_centuries ) ),
			mb_strtolower( "Ir segle" ) );

		$vb_res = $this->_timeParser->parse( 'Iè segle dc', $this->_va_roman_centuries );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "0.010100000000" );
		$this->assertEquals( $va_parse['end'], "99.123123595900" );
		$this->assertEquals( $va_parse[0], "0.010100000000" );
		$this->assertEquals( $va_parse[1], "99.123123595900" );

		$this->assertEquals( mb_strtolower( $this->_timeParser->getText( $this->_va_roman_centuries ) ),
			mb_strtolower( "Ir segle" ) );
	}

	public function testTimes() {

		$vb_res   = $this->_timeParser->parseTime( '10:55pm' );
		$va_parse = $this->_timeParser->getTimes();

		$this->assertEquals( $va_parse['start'], 82500 );
		$this->assertEquals( $va_parse['end'], 82500 );
		$this->assertEquals( $va_parse[0], 82500 );
		$this->assertEquals( $va_parse[1], 82500 );

		$vb_res   = $this->_timeParser->parseTime( '22:55' );
		$va_parse = $this->_timeParser->getTimes();

		$this->assertEquals( $va_parse['start'], 82500 );
		$this->assertEquals( $va_parse['end'], 82500 );
		$this->assertEquals( $va_parse[0], 82500 );
		$this->assertEquals( $va_parse[1], 82500 );

		$vb_res   = $this->_timeParser->parseTime( '22:55:15' );
		$va_parse = $this->_timeParser->getTimes();

		$this->assertEquals( $va_parse['start'], 82515 );
		$this->assertEquals( $va_parse['end'], 82515 );
		$this->assertEquals( $va_parse[0], 82515 );
		$this->assertEquals( $va_parse[1], 82515 );
	}

	public function testDatesWithTimes() {

		$vb_res = $this->_timeParser->parse( '6 de juny de 2009 a les 10:55pm' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "2009.060622550000" );
		$this->assertEquals( $va_parse['end'], "2009.060622550000" );
		$this->assertEquals( $va_parse[0], "2009.060622550000" );
		$this->assertEquals( $va_parse[1], "2009.060622550000" );

		$vb_res = $this->_timeParser->parse( '6 juny 2009 @ 22:55' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "2009.060622550000" );
		$this->assertEquals( $va_parse['end'], "2009.060622550000" );
		$this->assertEquals( $va_parse[0], "2009.060622550000" );
		$this->assertEquals( $va_parse[1], "2009.060622550000" );

		$vb_res = $this->_timeParser->parse( '6 juny 2009 @ 10:55:10pm' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "2009.060622551000" );
		$this->assertEquals( $va_parse['end'], "2009.060622551000" );
		$this->assertEquals( $va_parse[0], "2009.060622551000" );
		$this->assertEquals( $va_parse[1], "2009.060622551000" );

		$vb_res = $this->_timeParser->parse( '6 juny 2009 @ 10:55:10pm' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "2009.060622551000" );
		$this->assertEquals( $va_parse['end'], "2009.060622551000" );
		$this->assertEquals( $va_parse[0], "2009.060622551000" );
		$this->assertEquals( $va_parse[1], "2009.060622551000" );

		$vb_res = $this->_timeParser->parse( '16/6/2009 @ 10:55:10pm' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "2009.061622551000" );
		$this->assertEquals( $va_parse['end'], "2009.061622551000" );
		$this->assertEquals( $va_parse[0], "2009.061622551000" );
		$this->assertEquals( $va_parse[1], "2009.061622551000" );
	}

	public function testDateRangesWithTimes() {

		$vb_res = $this->_timeParser->parse( '15/6/2007 @ 9am .. 15/6/2007 @ 5pm' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "2007.061509000000" );
		$this->assertEquals( $va_parse['end'], "2007.061517000000" );
		$this->assertEquals( $va_parse[0], "2007.061509000000" );
		$this->assertEquals( $va_parse[1], "2007.061517000000" );

		// Midnight
		$vb_res = $this->_timeParser->parse( '13 de setembre a les 18:00 - 14 de setembre de 2014 a les 00:00' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], "2014.091318000000" );
		$this->assertEquals( $va_parse['end'], "2014.091400000000" );
		$this->assertEquals( $va_parse[0], "2014.091318000000" );
		$this->assertEquals( $va_parse[1], "2014.091400000000" );

		$this->assertEquals( $this->_timeParser->getText(), '13 setembre a les 18:00 - 14 setembre 2014 a les 0:00' );
	}

	public function testDatesWithImplicitYear() {

		$va_date = getDate();
		// TODO: '5/6 a les 9am - 5pm' in Catalan should be June 5th, but the parser translates it to May 6th.
		$vb_res = $this->_timeParser->parse( '6/5 a les 9am - 5pm' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], $va_date['year'] . '.060509000000' );
		$this->assertEquals( $va_parse['end'], $va_date['year'] . '.060517000000' );
		$this->assertEquals( $va_parse[0], $va_date['year'] . '.060509000000' );
		$this->assertEquals( $va_parse[1], $va_date['year'] . '.060517000000' );
	}

	public function testDateTextOutput() {

		$vb_res = $this->_timeParser->parse( '16/6/2009 @ 10:55:10pm' );
		$this->assertEquals( $vb_res, true );

		$this->assertEquals( $this->_timeParser->getText(), '16 juny 2009 a les 22:55:10' );

		$this->_timeParser->setLanguage( 'de_DE' );

		$this->assertEquals( $this->_timeParser->getText(), '16. Juni 2009 um 22:55:10' );
	}

	public function testMYADate() {

		$vb_res = $this->_timeParser->parse( '40 mda' );
		$this->assertEquals( $vb_res, true );

		$this->assertEquals( $this->_timeParser->getText(), '40000000 ac' );
	}

	public function testIncompleteRanges() {

		$va_date = getDate();

		$vb_res = $this->_timeParser->parse( '20 - 27 de agost de 2011' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], '2011.082000000000' );
		$this->assertEquals( $va_parse['end'], '2011.082723595900' );

		$vb_res = $this->_timeParser->parse( '20 - 27 agost 2011' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], '2011.082000000000' );
		$this->assertEquals( $va_parse['end'], '2011.082723595900' );

		$vb_res = $this->_timeParser->parse( '20 - 27 agost' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], $va_date['year'] . '.082000000000' );
		$this->assertEquals( $va_parse['end'], $va_date['year'] . '.082723595900' );

		$vb_res = $this->_timeParser->parse( 'març - juny 1850' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], '1850.030100000000' );
		$this->assertEquals( $va_parse['end'], '1850.063023595900' );

		$this->_timeParser->setLanguage( 'de_DE' );

		$vb_res = $this->_timeParser->parse( '20 - 27 August 2011' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], '2011.082000000000' );
		$this->assertEquals( $va_parse['end'], '2011.082723595900' );

		$vb_res = $this->_timeParser->parse( '20 August - 27 2011' );
		$this->assertEquals( $vb_res, true );
		$va_parse = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_parse['start'], '2011.082000000000' );
		$this->assertEquals( $va_parse['end'], '2011.082723595900' );

	}

	function testNormalizationYears() {
		$this->_timeParser = new TimeExpressionParser( '1500-1599', 'ca_ES' );
		$va_historic       = $this->_timeParser->getHistoricTimestamps();

		$va_years_expected = array();
		for ( $vn_i = 1500; $vn_i < 1600; $vn_i ++ ) {
			$va_years_expected[ $vn_i ] = $vn_i;
		}

		$va_years = $this->_timeParser->normalizeDateRange( $va_historic['start'], $va_historic['end'], 'years' );
		$this->assertEquals( 100, sizeof( $va_years ) );
		$this->assertEquals( $va_years_expected, $va_years );
	}

	function testNormalizationDecades() {
		$this->_timeParser = new TimeExpressionParser( '1500 - 1599', 'ca_ES' );
		$va_historic       = $this->_timeParser->getHistoricTimestamps();

		$va_decades_expected = array(
			1500 => '1500s',
			1510 => '1510s',
			1520 => '1520s',
			1530 => '1530s',
			1540 => '1540s',
			1550 => '1550s',
			1560 => '1560s',
			1570 => '1570s',
			1580 => '1580s',
			1590 => '1590s'
		);

		$va_decades = $this->_timeParser->normalizeDateRange( $va_historic['start'], $va_historic['end'], 'decades' );
		$this->assertEquals( 10, sizeof( $va_decades ) );
		$this->assertEquals( $va_decades_expected, $va_decades );
	}

	function testMultiWordConjunctions() {
		$this->_timeParser = new TimeExpressionParser();
		$this->_timeParser->setLanguage( "it_IT" );
		$this->assertEquals( $this->_timeParser->parse( "23/3/2001 fino a 27/3/2001" ), true );
		$va_historic = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_historic['start'], '2001.032300000000' );
		$this->assertEquals( $va_historic['end'], '2001.032723595900' );

		$this->assertEquals( $this->_timeParser->parse( "23 fino a 27 Marzo 2001" ), true );
		$va_historic = $this->_timeParser->getHistoricTimestamps();

		$this->assertEquals( $va_historic['start'], '2001.032300000000' );
		$this->assertEquals( $va_historic['end'], '2001.032723595900' );
	}
}
