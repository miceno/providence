<?php
/** ---------------------------------------------------------------------
 * tests/helpers/ImportHelpersTest.php
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
 * @package CollectiveAccess
 * @subpackage tests
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 * 
 * ----------------------------------------------------------------------
 */
use PHPUnit\Framework\TestCase;

require_once(__CA_APP_DIR__."/helpers/importHelpers.php");

class ImportHelpersTest extends TestCase {

    protected $data;
    protected $item;
    protected $attributes;
    protected $parents;

    protected function setUp(): void {
        $this->data = [
                1 => "Verdun",
                2 => ['Cambrai', 'Arras'],
                3 => 'Chateau Thierry',
                4 => 'Somme',
                5 => 'Popperinge',
                6 => 'Ypres;Somme;Cambrai;Ypres;Popperinge',
                7 => ['Antwerp', 'Dieppe|Charleois|Paschendale', 'Bruges']
        ];
        $this->item = [
                'settings' => [
                        'original_values' => [
                                'sector_ypres','sector_somme', 'sector_cambrai'
                        ],
                        'replacement_values' => [
                                'Value_Ypres', 'Value_Somme', 'Value_Cambrai'
                        ]
                ]
        ];

        $this->attributes = array(
                'movement_reason' => "^1",
                'preferred_label' => ["^3"],
                'nonpreferred_label' => array("name" => "^5")
        );

        $this->parents = array(
                array('idno' => '^1',
                        'name' => '^3',
                        'type' => 'internal',
                        'attributes' => array(
                                'description' => '^5'
                        )
                )
        );

    }

    # -------------------------------------------------------
	public function testAATMatchPeople() {
        // some real-world examples
        $vm_ret = caMatchAAT(
                explode(':', 'People and Culture:Associated Concepts:concepts in the arts:artistic concepts:forms of expression:forms of expression: visual arts:abstraction')
        );

        $this->assertEquals('http://vocab.getty.edu/aat/300056508', $vm_ret);
    }

    public function testAATMatchPrints() {
        // some real-world examples
        $vm_ret = caMatchAAT(
                explode(':', 'Objects We Use:Visual Works:visual works:visual works by medium or technique:prints:prints by process or technique:prints by process: transfer method:intaglio prints:etchings')
        );

        $this->assertEquals('http://vocab.getty.edu/aat/300041365', $vm_ret);
    }

    public function testAATMatchPaper() {
        // some real-world examples
        $vm_ret = caMatchAAT(
                explode(':', 'Objects We Use:Visual Works:visual works:visual works by medium or technique:works on paper')
        );

        $this->assertEquals('http://vocab.getty.edu/aat/300189621', $vm_ret);
    }

    public function testAATMatchAbstractArt() {
        // some real-world examples

        $vm_ret = caMatchAAT(
                explode(':', 'People and Culture:Styles and Periods:styles and periods by region:European:European styles and periods:modern European styles and movements:modern European fine arts styles and movements:Abstract'),
                180, array('removeParensFromLabels' => true)
        );

        $this->assertEquals('http://vocab.getty.edu/aat/300108127', $vm_ret);
    }

    public function testAATMatchComputerArt() {
        // some real-world examples

        $vm_ret = caMatchAAT(
                explode(':', 'People and Culture:Associated Concepts:concepts in the arts:artistic concepts:art genres:computer art'),
                180, array('removeParensFromLabels' => true)
        );

        $this->assertEquals('http://vocab.getty.edu/aat/300069478', $vm_ret);
    }

    public function testAATMatchAcrylicPainting() {
        // some real-world examples

        $vm_ret = caMatchAAT(
                explode(':', 'Descriptors:Processes and Techniques:processes and techniques:processes and techniques by specific type:image-making processes and techniques:painting and painting techniques:painting techniques:painting techniques by medium:acrylic painting (technique)'),
                180, array('removeParensFromLabels' => true)
        );

        $this->assertEquals('http://vocab.getty.edu/aat/300182574', $vm_ret);
    }

    public function testAATMatchPainting() {
        // some real-world examples

        $vm_ret = caMatchAAT(
			explode(':', 'Descriptors:Processes and Techniques:processes and techniques:processes and techniques by specific type:image-making processes and techniques:painting and painting techniques:painting (image-making)'),
			180, array('removeParensFromLabels' => true)
		);

		$this->assertEquals('http://vocab.getty.edu/aat/300054216', $vm_ret);
	}
	# -------------------------------------------------------


    /**
     *
     *
     */
    public function testCaProcessImportItemSettingsForValue() {
        $ps_value = '7.30.pepe';
        $pa_item_settings = array(
                'applyRegularExpressions' => array(
                        array("match" => '([0-9]+)\\.([0-9]+)',
                                "replaceWith" => "\\1:\\2"),
                        array(
                                "match" => "[^0-9:]+",
                                "replaceWith" => ""
                        )
                )
        );

        $result = caProcessImportItemSettingsForValue($ps_value, $pa_item_settings);
        try {
            $this->assertSame('7:30', $result);
        } catch (Exception $exception){
            $this->markTestIncomplete();
        }
    }

    public function testCaValidateGoogleSheetsUrlReturnsNullForBadUrl(){
        $url = "http://collectiveaccess.org";
        $result = caValidateGoogleSheetsUrl($url);
        $this->assertNull($result);
    }
    public function testCaValidateGoogleSheetsUrlReturnsValidatedUrl(){
        $url = "https://docs.google.com/file/d/";
        $result = caValidateGoogleSheetsUrl($url);
        $this->assertSame('https://docs.google.com/file/d/export?format=xlsx', $result);
    }

    public function testCaProcessRefineryAttributesEmptyIsNotNull(){
        $pa_attributes = $this->attributes;
        $pa_source_data = $this->data;
        $pa_item = $this->item;
        $pn_c = 0;
        $pa_options = array();
        $result = caProcessRefineryAttributes($pa_attributes, $pa_source_data, $pa_item, $pn_c, $pa_options);
        $this->assertNotNull($result);
    }
    public function testCaProcessRefineryAttributesEmptyProducesEmptyResults(){
        $pa_attributes = array();
        $pa_source_data = array();
        $pa_item = array();
        $pn_c = 0;
        $pa_options = array();
        $result = caProcessRefineryAttributes($pa_attributes, $pa_source_data, $pa_item, $pn_c, $pa_options);
        $this->assertNotNull($result);
        $this->assertEquals(0, sizeof($result));
    }
    public function testCaProcessRefineryAttributesWithFileType(){
        $this->markTestIncomplete('testCaProcessRefineryAttributesWithFileType pending test');
    }
    public function testCaProcessRefineryAttributesWithIndexedArray(){
        $pa_attributes = $this->attributes;
        $pa_source_data = $this->data;
        $pa_item = $this->item;
        $pn_c = 0;
        $pa_options = array();
        $result = caProcessRefineryAttributes($pa_attributes, $pa_source_data, $pa_item, $pn_c, $pa_options);
        $this->assertNotNull($result);
        $this->assertEquals($this->data[3], $result["preferred_label"][0]);
    }
    public function testCaProcessRefineryAttributesWithAssociativeArray(){
        $pa_attributes = $this->attributes;
        $pa_source_data = $this->data;
        $pa_item = $this->item;
        $pn_c = 0;
        $pa_options = array();
        $result = caProcessRefineryAttributes($pa_attributes, $pa_source_data, $pa_item, $pn_c, $pa_options);
        $this->assertNotNull($result);
        $this->assertEquals($this->data[5], $result["nonpreferred_label"]["name"]);
    }


    public function testCaProcessRefineryParentsPlacesHierarchy(){

        require_once (__CA_APP_DIR__. '/refineries/placeHierarchyBuilder/placeHierarchyBuilderRefinery.php');

        $stubRefinery = $this->createMock(placeHierarchyBuilderRefinery::class);
        $stubRefinery->method('getName')->willReturn('placeHierarchyBuilderRefinery');
        $ps_refinery_name = 'placeHierarchyBuilderRefinery';
        $ps_table = 'ca_places';
        $pa_parents = $this->parents;
        $pa_source_data = $this->data;
        $pa_item = $this->item;
        $pn_c = 0;
        $pa_options = array(
                'refinery' => $stubRefinery,
        );
        $result = caProcessRefineryParents($ps_refinery_name, $ps_table, $pa_parents, $pa_source_data, $pa_item, $pn_c, $pa_options);
        $this->assertNotNull($result);
    }

    public function testCaProcessRefineryParentsCollectionHierarchy(){

        require_once (__CA_APP_DIR__. '/refineries/collectionHierarchyBuilder/collectionHierarchyBuilderRefinery.php');

        $stubRefinery = $this->createMock(collectionHierarchyBuilderRefinery::class);
        $stubRefinery->method('getName')->willReturn('collectionHierarchyBuilderRefinery');
        $ps_refinery_name = 'collectionHierarchyBuilderRefinery';
        $ps_table = 'ca_collections';
        $pa_parents = $this->parents;
        $pa_source_data = $this->data;
        $pa_item = $this->item;
        $pn_c = 0;
        $pa_options = array(
                'refinery' => $stubRefinery
        );
        $result = caProcessRefineryParents($ps_refinery_name, $ps_table, $pa_parents, $pa_source_data, $pa_item, $pn_c, $pa_options);
        $this->assertNotNull($result);
    }
}
