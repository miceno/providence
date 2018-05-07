<?php
/** ---------------------------------------------------------------------
 * app/lib/core/Plugins/Media/Gmagick.php :
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2012-2018 Whirl-i-Gig
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
 * @subpackage Media
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 *
 * ----------------------------------------------------------------------
 */
 
 /**
  *
  */
 
/**
 * Plugin for processing images using GraphicsMagick via the Gmagick PECL extension
*/

include_once(__CA_LIB_DIR__."/core/Plugins/Media/BaseMediaPlugin.php");
include_once(__CA_LIB_DIR__."/core/Plugins/IWLPlugMedia.php");
include_once(__CA_LIB_DIR__."/core/Parsers/TilepicParser.php");
include_once(__CA_LIB_DIR__."/core/Configuration.php");
include_once(__CA_APP_DIR__."/helpers/mediaPluginHelpers.php");
include_once(__CA_LIB_DIR__."/core/Parsers/MediaMetadata/XMPParser.php");

include_once(__CA_LIB_DIR__."/core/Plugins/Media/GraphicsMagick.php");
include_once(__CA_LIB_DIR__."/core/Plugins/Media/ImageMagick.php");
include_once(__CA_LIB_DIR__."/core/Plugins/Media/Imagick.php");

class WLPlugMediaGmagick Extends BaseMediaPlugin Implements IWLPlugMedia {
	var $errors = array();
	
	var $ps_filepath;
	var $handle;
	var $ohandle;
	var $properties;
	var $metadata = array();
	
	var $opo_config;
	var $opo_external_app_config;
	
	var $opa_faces;
	var $filepath;
	
	var $info = array(
		'IMPORT' => array(
			'image/jpeg' 		=> 'jpg',
			'image/gif' 		=> 'gif',
			'image/tiff' 		=> 'tiff',
			'image/png' 		=> 'png',
			'image/x-bmp' 		=> 'bmp',
			'image/x-psd' 		=> 'psd',
			'image/tilepic' 	=> 'tpc',
			'image/x-dpx'		=> 'dpx',
			'image/x-exr'		=> 'exr',
			'image/jp2'		=> 'jp2',
			'image/x-adobe-dng'	=> 'dng',
			'image/x-canon-cr2'	=> 'cr2',
			'image/x-canon-crw'	=> 'crw',
			'image/x-sony-arw'	=> 'arw',
			'image/x-olympus-orf'	=> 'orf',
			'image/x-pentax-pef'	=> 'pef',
			'image/x-epson-erf'	=> 'erf',
			'image/x-nikon-nef'	=> 'nef',
			'image/x-sony-sr2'	=> 'sr2',
			'image/x-sony-srf'	=> 'srf',
			'image/x-sigma-x3f'	=> 'x3f',
			'image/x-dcraw'	=> 'raw',
			'application/dicom' => 'dcm',
		),
		'EXPORT' => array(
			'image/jpeg' 		=> 'jpg',
			'image/gif' 		=> 'gif',
			'image/tiff' 		=> 'tiff',
			'image/png' 		=> 'png',
			'image/x-bmp' 		=> 'bmp',
			'image/x-psd' 		=> 'psd',
			'image/tilepic' 	=> 'tpc',
			'image/x-dpx'		=> 'dpx',
			'image/x-exr'		=> 'exr',
			'image/jp2'		=> 'jp2',
			'image/x-adobe-dng'	=> 'dng',
			'image/x-canon-cr2'	=> 'cr2',
			'image/x-canon-crw'	=> 'crw',
			'image/x-sony-arw'	=> 'arw',
			'image/x-olympus-orf'	=> 'orf',
			'image/x-pentax-pef'	=> 'pef',
			'image/x-epson-erf'	=> 'erf',
			'image/x-nikon-nef'	=> 'nef',
			'image/x-sony-sr2'	=> 'sr2',
			'image/x-sony-srf'	=> 'srf',
			'image/x-sigma-x3f'	=> 'x3f',
			'image/x-dcraw'	=> 'raw',
			'application/dicom' => 'dcm',
		),
		'TRANSFORMATIONS' => array(
			'SCALE' 			=> array('width', 'height', 'mode', 'antialiasing'),
			'CROP' 				=> array('width', 'height', 'x', 'y'),
			'ANNOTATE'			=> array('text', 'font', 'size', 'color', 'position', 'inset'),
			'WATERMARK'			=> array('image', 'width', 'height', 'position', 'opacity'),
			'ROTATE' 			=> array('angle'),
			'SET' 				=> array('property', 'value'),
			'FLIP'				=> array('direction'),
			
			# --- filters
			'MEDIAN'			=> array('radius'),
			'DESPECKLE'			=> array(''),
			'SHARPEN'			=> array('radius', 'sigma'),
			'UNSHARPEN_MASK'	=> array('radius', 'sigma', 'amount', 'threshold'),
		),
		'PROPERTIES' => array(
			'width' 			=> 'R',
			'height' 			=> 'R',
			'mimetype' 			=> 'R',
			'typename' 			=> 'R',
			'tiles'				=> 'R',
			'layers'			=> 'W',
			'quality' 			=> 'W',
			'colorspace'		=> 'W',
			'tile_width'		=> 'W',
			'tile_height'		=> 'W',
			'antialiasing'		=> 'W',
			'layer_ratio'		=> 'W',
			'tile_mimetype'		=> 'W',
			'output_layer'		=> 'W',
			'gamma'				=> 'W',
			'reference-black'	=> 'W',
			'reference-white'	=> 'W',
			'no_upsampling'		=> 'W',
			'faces'				=> 'W',
			'version'			=> 'W'	// required of all plug-ins
		),
		
		'NAME' => 'Gmagick'
	);
	
	var $typenames = array(
		'image/jpeg' 		=> 'JPEG',
		'image/gif' 		=> 'GIF',
		'image/tiff' 		=> 'TIFF',
		'image/png' 		=> 'PNG',
		'image/x-bmp' 		=> 'Windows Bitmap (BMP)',
		'image/x-psd' 		=> 'Photoshop',
		'image/tilepic' 	=> 'Tilepic',
		'image/x-dpx'		=> 'DPX',
		'image/x-exr'		=> 'OpenEXR',
		'image/jp2'		=> 'JPEG-2000',
		'image/x-adobe-dng'	=> 'Adobe DNG',
		'image/x-canon-cr2'	=> 'Canon CR2 RAW Image',
		'image/x-canon-crw'	=> 'Canon CRW RAW Image',
		'image/x-sony-arw'	=> 'Sony ARW RAW Image',
		'image/x-olympus-orf'	=> 'Olympus ORF Raw Image',
		'image/x-pentax-pef'	=> 'Pentax Electronic File Image',
		'image/x-epson-erf'	=> 'Epson ERF RAW Image',
		'image/x-nikon-nef'	=> 'Nikon NEF RAW Image',
		'image/x-sony-sr2'	=> 'Sony SR2 RAW Image',
		'image/x-sony-srf'	=> 'Sony SRF RAW Image',
		'image/x-sigma-x3f'	=> 'Sigma X3F RAW Image',
		'image/x-dcraw'	=> 'RAW Image',
		'application/dicom' => 'DICOM medical imaging data',
	);
	
	var $magick_names = array(
		'image/jpeg' 		=> 'JPEG',
		'image/gif' 		=> 'GIF',
		'image/tiff' 		=> 'TIFF',
		'image/png' 		=> 'PNG',
		'image/x-bmp' 		=> 'BMP',
		'image/x-psd' 		=> 'PSD',
		'image/tilepic' 	=> 'TPC',
		'image/x-dpx'		=> 'DPX',
		'image/x-exr'		=> 'EXR',
		'image/jp2'		=> 'JP2',
		'image/x-adobe-dng'	=> 'DNG',
		'image/x-canon-cr2'	=> 'CR2',
		'image/x-canon-crw'	=> 'CRW',
		'image/x-sony-arw'	=> 'ARW',
		'image/x-olympus-orf'	=> 'ORF',
		'image/x-pentax-pef'	=> 'PEF',
		'image/x-epson-erf'	=> 'ERF',
		'image/x-nikon-nef'	=> 'NEF',
		'image/x-sony-sr2'	=> 'SR2',
		'image/x-sony-srf'	=> 'SRF',
		'image/x-sigma-x3f'	=> 'X3F',
		'image/x-dcraw'		=> 'RAW',
		'application/dicom' => 'DCM',
	);
	
	#
	# Some versions of ImageMagick return variants on the 'normal'
	# mimetypes for certain image formats, so we convert them here
	#
	var $magick_mime_map = array(
		'image/x-jpeg' 		=> 'image/jpeg',
		'image/x-gif' 		=> 'image/gif',
		'image/x-tiff' 		=> 'image/tiff',
		'image/x-png' 		=> 'image/png',
		'image/dpx' 		=> 'image/x-dpx',
		'image/exr' 		=> 'image/x-exr',
		'image/jpx'		=> 'image/jp2',
		'image/jpm'		=> 'image/jp2',
		'image/dng'		=> 'image/x-adobe-dng'
	);
	
	#
	# Alternative extensions for supported types
	#
	var $alternative_extensions = [
		'tif' => 'image/tiff',
		'jpeg' => 'image/jpeg'
	];	

	private $ops_dcraw_path;
	private $ops_graphicsmagick_path;
	private $ops_imagemagick_path;
	
	/**
	 * Per-request cache of extracted metadata from read files
	 */
	static $s_metadata_read_cache = [];
	# ------------------------------------------------
	public function __construct() {
		$this->description = _t('Provides image processing and conversion services using ImageMagick via the PECL Gmagick PHP extension');
	}
	# ------------------------------------------------
	# Tell WebLib what kinds of media this plug-in supports
	# for import and export
	public function register() {
		$this->opo_config = Configuration::load();
		$this->opo_external_app_config = Configuration::load(__CA_CONF_DIR__."/external_applications.conf");
		$this->ops_graphicsmagick_path = $this->opo_external_app_config->get('graphicsmagick_app');
		$this->ops_imagemagick_path = $this->opo_external_app_config->get('imagemagick_path');
		
		$this->ops_dcraw_path = $this->opo_external_app_config->get('dcraw_app');
		
		if (!caMediaPluginGmagickInstalled()) {
			return null;	// don't use if Gmagick functions are unavailable
		}
		
		$this->info["INSTANCE"] = $this;
		return $this->info;
	}
	# ------------------------------------------------
	public function checkStatus() {
		$va_status = parent::checkStatus();
		
		if ($this->register()) {
			$va_status['available'] = true;
		} else {
			if (!caMediaPluginGmagickInstalled()) {	
				$va_status['errors'][] = _t("Didn't load because Gmagick is not available");
			} 
		}
		
		if (!caMediaPluginDcrawInstalled($this->ops_dcraw_path)) {
			$va_status['warnings'][] = _t("RAW image support is not enabled because DCRAW cannot be found");
		}
		
		return $va_status;
	}
	# ------------------------------------------------
	public function divineFileFormat($ps_filepath) {
		# is it a camera raw image?
		if (caMediaPluginDcrawInstalled($this->ops_dcraw_path)) {
			exec($this->ops_dcraw_path." -i ".caEscapeShellArg($ps_filepath)." 2> /dev/null", $va_output, $vn_return);
			if ($vn_return == 0) {
				if ((!preg_match("/^Cannot decode/", $va_output[0])) && (!preg_match("/Master/i", $va_output[0]))) {
					return 'image/x-dcraw';
				}
			}
		}
		
		try {
			if ($ps_filepath != '' && ($r_handle = new Gmagick($ps_filepath))) {
				$this->setResourceLimits($r_handle);
				$mimetype = $this->_getMagickImageMimeType($r_handle);
				if (($mimetype) && $this->info["IMPORT"][$mimetype]) {
					return $mimetype;
				} else {
					return '';
				}
			} 
		} catch (Exception $e) {
			# is it a tilepic?
			$tp = new TilepicParser();
			if ($tp->isTilepic($ps_filepath)) {
				return 'image/tilepic';
			} else {
				# file format is not supported by this plug-in
				return '';
			}
		}
	}
	# ----------------------------------------------------------
	public function _getMagickImageMimeType($pr_handle) {
		$ps_format = $pr_handle->getimageformat();
		foreach($this->magick_names as $vs_mimetype => $vs_format) {
			if ($ps_format == $vs_format) {
				return $vs_mimetype;
			}
		}
		return "image/x-unknown";
	}
	# ----------------------------------------------------------
	public function get($property) {
		if ($this->handle) {
			if ($this->info["PROPERTIES"][$property]) {
				return $this->properties[$property];
			} else {
				//print "Invalid property";
				return "";
			}
		} else {
			return "";
		}
	}
	# ----------------------------------------------------------
	public function set($property, $value) {
		if ($this->handle) {
			if ($property == "tile_size") {
				if (($value < 10) || ($value > 10000)) {
					$this->postError(1650, _t("Tile size property must be between 10 and 10000"), "WLPlugGmagick->set()");
					return "";
				}
				$this->properties["tile_width"] = $value;
				$this->properties["tile_height"] = $value;
			} else {
				if ($this->info["PROPERTIES"][$property]) {
					switch($property) {
						case 'quality':
							if (($value < 1) || ($value > 100)) {
								$this->postError(1650, _t("Quality property must be between 1 and 100"), "WLPlugGmagick->set()");
								return "";
							}
							$this->properties["quality"] = $value;
							break;
						case 'tile_width':
							if (($value < 10) || ($value > 10000)) {
								$this->postError(1650, _t("Tile width property must be between 10 and 10000"), "WLPlugGmagick->set()");
								return "";
							}
							$this->properties["tile_width"] = $value;
							break;
						case 'tile_height':
							if (($value < 10) || ($value > 10000)) {
								$this->postError(1650, _t("Tile height property must be between 10 and 10000"), "WLPlugGmagick->set()");
								return "";
							}
							$this->properties["tile_height"] = $value;
							break;
						case 'antialiasing':
							if (($value < 0) || ($value > 100)) {
								$this->postError(1650, _t("Antialiasing property must be between 0 and 100"), "WLPlugGmagick->set()");
								return "";
							}
							$this->properties["antialiasing"] = $value;
							break;
						case 'layer_ratio':
							if (($value < 0.1) || ($value > 10)) {
								$this->postError(1650, _t("Layer ratio property must be between 0.1 and 10"), "WLPlugGmagick->set()");
								return "";
							}
							$this->properties["layer_ratio"] = $value;
							break;
						case 'layers':
							if (($value < 1) || ($value > 25)) {
								$this->postError(1650, _t("Layer property must be between 1 and 25"), "WLPlugGmagick->set()");
								return "";
							}
							$this->properties["layers"] = $value;
							break;	
						case 'tile_mimetype':
							if ((!($this->info["EXPORT"][$value])) && ($value != "image/tilepic")) {
								$this->postError(1650, _t("Tile output type '%1' is invalid", $value), "WLPlugGmagick->set()");
								return "";
							}
							$this->properties["tile_mimetype"] = $value;
							break;
						case 'output_layer':
							$this->properties["output_layer"] = $value;
							break;
						default:
							if ($this->info["PROPERTIES"][$property] == 'W') {
								$this->properties[$property] = $value;
							} else {
								# read only
								return "";
							}
							break;
					}
				} else {
					# invalid property
					$this->postError(1650, _t("Can't set property %1", $property), "WLPlugGmagick->set()");
					return "";
				}
			}
		} else {
			return "";
		}
		return 1;
	}
	# ------------------------------------------------
	/**
	 * Returns array of extracted metadata, key'ed by metadata type or empty array if plugin doesn't support metadata extraction
	 *
	 * @return Array Extracted metadata
	 */
	public function getExtractedMetadata() {
		return $this->metadata;
	}
	# ----------------------------------------------------------
	public function read($ps_filepath, $mimetype="") {
		if (!(($this->handle) && ($ps_filepath === $this->filepath))) {
			
			if ($mimetype == 'image/tilepic') {
				#
				# Read in Tilepic format image
				#
				$this->handle = new TilepicParser($ps_filepath);
				if (!$this->handle->error) {
					$this->filepath = $ps_filepath;
					foreach($this->handle->properties as $k => $v) {
						if (isset($this->properties[$k])) {
							$this->properties[$k] = $v;
						}
					}
					$this->properties["mimetype"] = "image/tilepic";
					$this->properties["typename"] = "Tilepic";
					return 1;
				} else {
					$this->postError(1610, $this->handle->error, "WLPlugGmagick->read()");
					return false;
				}
			} else {
				$this->handle = "";
				$this->filepath = "";

				$this->metadata = array();

				// convert to tiff with dcraw if necessary
				if ($mimetype == 'image/x-dcraw') {
					$ps_filepath = $this->_dcrawConvertToTiff($ps_filepath);
				}

				if(!($handle = $this->_gmagickRead($ps_filepath))) {
					return false; // plugin cant handle format
				}

				# load image properties
				$va_tmp = $this->handle->getimagegeometry();
				$this->properties["width"] = $va_tmp['width'];
				$this->properties["height"] = $va_tmp['height'];
				$this->properties["quality"] = "";
				$this->properties["filesize"] = filesize($ps_filepath);
				$this->properties["bitdepth"] = $this->handle->getimagedepth();
				$this->properties["resolution"] = $this->handle->getimageresolution();
				$this->properties["colorspace"] = $this->_getColorspaceAsString($this->handle->getimagecolorspace());

				// force all images to true color (takes care of GIF transparency for one thing...)
				$this->handle->setimagetype(Gmagick::IMGTYPE_TRUECOLOR);

				if (!$this->handle->setimagecolorspace(Gmagick::COLORSPACE_RGB)) {
					$this->postError(1610, _t("Error during RGB colorspace transformation operation"), "WLPlugGmagick->read()");
					return false;
				}


				if (!$this->properties["faces"]) {
					$this->properties["faces"] = $this->opa_faces = caDetectFaces($ps_filepath, $va_tmp['width'], $va_tmp['height']);
				}

				$this->properties["mimetype"] = $this->_getMagickImageMimeType($this->handle);
				$this->properties["typename"] = $this->handle->getimageformat();

				$this->ohandle = clone $this->handle;
				return 1;
			}
		} else {
			# image already loaded by previous call (probably divineFileFormat())
			return 1;
		}
	}
	# ----------------------------------------------------------
	public function transform($operation, $parameters) {
		if ($this->properties["mimetype"] == "image/tilepic") { return false;} # no transformations for Tilepic
		if (!$this->handle) { return false; }
		
		if (!($this->info["TRANSFORMATIONS"][$operation])) {
			# invalid transformation
			$this->postError(1655, _t("Invalid transformation %1", $operation), "WLPlugGmagick->transform()");
			return false;
		}
		
		# get parameters for this operation
		$sparams = $this->info["TRANSFORMATIONS"][$operation];
		
		$w = $parameters["width"];
		$h = $parameters["height"];
		$cw = $this->get("width");
		$ch = $this->get("height");
		
		if((bool)$this->properties['no_upsampling']) {
			$w = min($cw, round($w)); 
			$h = min($ch, round($h));
		}
		
		$do_crop = 0;
		
		try {
			switch($operation) {
				# -----------------------
				case 'ANNOTATE':
					$d = new GmagickDraw();
					if ($parameters['font']) { 
						try {
							$d->setfont($parameters['font']);
						} catch (Exception $e) {
							$this->postError(1655, _t("Couldn't set font to %1. Gmagick error message: %2",$parameters['font'],$e->getMessage()), "WLPlugGmagick->transform()");
							break;
						}
					}
					$size = ($parameters['size'] > 0) ? $parameters['size']: 18;
					$d->setfontsize($size);
				
					$inset = ($parameters['inset'] > 0) ? $parameters['inset'] : 0;
					$pw= new GmagickPixel();
					$pw->setcolor($parameters['color'] ? $parameters['color'] : "black");
					$d->setfillcolor($pw);
					
					switch($parameters['position']) {
						default:
							break;
					}
					$this->handle->annotateimage($d,$inset, $size + $inset, 0, $parameters['text']);
					break;
				# -----------------------
				case 'WATERMARK':
					if (!file_exists($parameters['image'])) { break; }
					
					# gmagick can't handle opacity when compositing images
					#$vn_opacity_setting = $parameters['opacity'];
					#if (($vn_opacity_setting < 0) || ($vn_opacity_setting > 1)) {
					#	$vn_opacity_setting = 0.5;
					#}
					
					if (($vn_watermark_width = $parameters['width']) < 10) { 
						$vn_watermark_width = $cw/2;
					}
					if (($vn_watermark_height = $parameters['height']) < 10) {
						$vn_watermark_height = $ch/2;
					}
					
					switch($parameters['position']) {
						case 'north_east':
							$vn_watermark_x = $cw - $vn_watermark_width;
							$vn_watermark_y = 0;
							break;
						case 'north_west':
							$vn_watermark_x = 0;
							$vn_watermark_y = 0;
							break;
						case 'north':
							$vn_watermark_x = ($cw - $vn_watermark_width)/2;
							$vn_watermark_y = 0;
							break;
						case 'south_east':
							$vn_watermark_x = $cw - $vn_watermark_width;
							$vn_watermark_y = $ch - $vn_watermark_height;
							break;
						case 'south':
							$vn_watermark_x = ($cw - $vn_watermark_width)/2;
							$vn_watermark_y = $ch - $vn_watermark_height;
							break;
						case 'center':
							$vn_watermark_x = ($cw - $vn_watermark_width)/2;
							$vn_watermark_y = ($ch - $vn_watermark_height)/2;
							break;
						case 'south_west':
						default:
							$vn_watermark_x = $cw - $vn_watermark_width;
							$vn_watermark_y = $ch - $vn_watermark_height;
							break;
					}
					
					try {
						$w = new Gmagick($parameters['image']);
						$this->setResourceLimits($w);
					} catch (Exception $e) {
						$this->postError(1610, _t("Couldn't load watermark image at %1", $parameters['image']), "WLPlugGmagick->transform:WATERMARK()");
						return false;
					}
					$w->scaleimage($vn_watermark_width,$vn_watermark_height);
					$this->handle->compositeimage($w,1,$vn_watermark_x,$vn_watermark_y);
					break;
				# -----------------------
				case 'SCALE':
					$aa = $parameters["antialiasing"];
					if ($aa <= 0) { $aa = 0; }
					switch($parameters["mode"]) {
						# ----------------
						case "width":
							$scale_factor = $w/$cw;
							$h = $ch * $scale_factor;
							break;
						# ----------------
						case "height":
							$scale_factor = $h/$ch;
							$w = $cw * $scale_factor;
							break;
						# ----------------
						case "bounding_box":
							$scale_factor_w = $w/$cw;
							$scale_factor_h = $h/$ch;
							$w = $cw * (($scale_factor_w < $scale_factor_h) ? $scale_factor_w : $scale_factor_h); 
							$h = $ch * (($scale_factor_w < $scale_factor_h) ? $scale_factor_w : $scale_factor_h);	
							break;
						# ----------------
						case "fill_box":
							$crop_from = $parameters["crop_from"];
							if (!in_array($crop_from, array('center', 'north_east', 'north_west', 'south_east', 'south_west', 'random'))) {
								$crop_from = '';
							}
							$scale_factor_w = $w/$cw;
							$scale_factor_h = $h/$ch;
							$w = $cw * (($scale_factor_w > $scale_factor_h) ? $scale_factor_w : $scale_factor_h); 
							$h = $ch * (($scale_factor_w > $scale_factor_h) ? $scale_factor_w : $scale_factor_h);	
							
							$do_fill_box_crop = true;
							break;
						# ----------------
					}
			
					$w = round($w);
					$h = round($h);
					if ($w > 0 && $h > 0) {
						$crop_w_edge = $crop_h_edge = 0;
						if (preg_match("/^([\d]+)%$/", $parameters["trim_edges"], $va_matches)) {
							$crop_w_edge = ceil((intval($va_matches[1])/100) * $w);
							$crop_h_edge = ceil((intval($va_matches[1])/100) * $h);
						} else {
							if (isset($parameters["trim_edges"]) && (intval($parameters["trim_edges"]) > 0)) {
								$crop_w_edge = $crop_h_edge = intval($parameters["trim_edges"]);
							}
						}
						if (!$this->handle->resizeimage($w + ($crop_w_edge * 2), $h + ($crop_h_edge * 2), Gmagick::FILTER_CUBIC, $aa)) {
								$this->postError(1610, _t("Error during resize operation"), "WLPlugGmagick->transform()");
								return false;
						}
						if ($do_fill_box_crop) {
							// use face detection info to intelligently crop
							if(is_array($this->properties['faces']) && sizeof($this->properties['faces'])) {
								$va_info = array_shift($this->properties['faces']);
								$crop_from_offset_x = ceil($va_info['x'] * (($scale_factor_w > $scale_factor_h) ? $scale_factor_w : $scale_factor_h));
								$crop_from_offset_x -= ceil(0.15 * $parameters["width"]);	// since face will be tightly cropped give it some room
								$crop_from_offset_y = ceil($va_info['y'] * (($scale_factor_w > $scale_factor_h) ? $scale_factor_w : $scale_factor_h));
								$crop_from_offset_y -= ceil(0.15 * $parameters["height"]);	// since face will be tightly cropped give it some room
								
								// Don't try to crop beyond image boundaries, you just end up scaling the image, often awkwardly
								if ($crop_from_offset_x > ($w - $parameters["width"])) { $crop_from_offset_x = 0; }
								if ($crop_from_offset_y > ($h - $parameters["height"])) { $crop_from_offset_y = 0; }
								if ($crop_from_offset_x < 0) { $crop_from_offset_x = 0; }
								if ($crop_from_offset_y < 0) { $crop_from_offset_y = 0; }
							} else {
								switch($crop_from) {
									case 'north_west':
										$crop_from_offset_y = 0;
										$crop_from_offset_x = $w - $parameters["width"];
										break;
									case 'south_east':
										$crop_from_offset_x = 0;
										$crop_from_offset_y = $h - $parameters["height"];
										break;
									case 'south_west':
										$crop_from_offset_x = $w - $parameters["width"];
										$crop_from_offset_y = $h - $parameters["height"];
										break;
									case 'random':
										$crop_from_offset_x = rand(0, $w - $parameters["width"]);
										$crop_from_offset_y = rand(0, $h - $parameters["height"]);
										break;
									case 'north_east':
										$crop_from_offset_x = $crop_from_offset_y = 0;
										break;
									case 'center':
									default:
										$crop_from_offset_x = $crop_from_offset_y = 0;
										
										// Get image center
										$vn_center_x = caGetOption('_centerX', $parameters, 0.5);
										$vn_center_y = caGetOption('_centerY', $parameters, 0.5);
										if ($w > $parameters["width"]) {
											$crop_from_offset_x = ceil($w * $vn_center_x) - ($parameters["width"]/2);
											if (($crop_from_offset_x + $parameters["width"]) > $w) { $crop_from_offset_x = $w - $parameters["width"]; }
											if ($crop_from_offset_x < 0) { $crop_from_offset_x = 0; }
										} else {
											if ($h > $parameters["height"]) {
												$crop_from_offset_y = ceil($h * $vn_center_y) - ($parameters["height"]/2);
												if (($crop_from_offset_y + $parameters["height"]) > $h) { $crop_from_offset_y = $h - $parameters["height"]; }
												if ($crop_from_offset_y < 0) { $crop_from_offset_y = 0; }
											}
										}
										break;
								}
							}
							
							if (!$this->handle->cropimage($parameters["width"], $parameters["height"], $crop_w_edge + $crop_from_offset_x, $crop_h_edge + $crop_from_offset_y )) {
								$this->postError(1610, _t("Error during crop operation"), "WLPlugGmagick->transform()");
								return false;
							}
							$this->properties["width"] = $parameters["width"];
							$this->properties["height"] = $parameters["height"];
						} else {
							if ($crop_w_edge || $crop_h_edge) {
								if (!$this->handle->cropimage($w, $h, $crop_w_edge, $crop_h_edge )) {
									$this->postError(1610, _t("Error during crop operation"), "WLPlugGmagick->transform()");
									return false;
								}
							}
							$this->properties["width"] = $w;
							$this->properties["height"] = $h;
						}
					}
				break;
			# -----------------------
			case "ROTATE":
				$angle = $parameters["angle"];
				if (($angle > -360) && ($angle < 360)) {
					if ( !$this->handle->rotateimage("#FFFFFF", $angle) ) {
						$this->postError(1610, _t("Error during image rotate"), "WLPlugGmagick->transform():ROTATE");
						return false;
					}
				}
				break;
			# -----------------------
			case "DESPECKLE":
				if ( !$this->handle->despeckleimage() ) {
					$this->postError(1610, _t("Error during image despeckle"), "WLPlugGmagick->transform:DESPECKLE()");
					return false;
				}
				break;
			# -----------------------
			case "MEDIAN":
				$radius = $parameters["radius"];
				if ($radius < .1) { $radius = 1; }
				if ( !$this->handle->medianfilterimage($radius) ) {
					$this->postError(1610, _t("Error during image median filter"), "WLPlugGmagick->transform:MEDIAN()");
					return false;
				}
				break;
			# -----------------------
			case "SHARPEN":
				$radius = $parameters["radius"];
				if ($radius < .1) { $radius = 1; }
				$sigma = $parameters["sigma"];
				if ($sigma < .1) { $sigma = 1; }
				if ( !$this->handle->sharpenImage( $radius, $sigma) ) {
					$this->postError(1610, _t("Error during image sharpen"), "WLPlugGmagick->transform:SHARPEN()");
					return false;
				}
				break;
			# -----------------------
			case "CROP":
				$x = $parameters["x"];
				$y = $parameters["y"];
				$w = $parameters["width"];
				$h = $parameters["height"];
				
				if (!$this->handle->cropimage($w, $h, $x, $y)) {
					$this->postError(1610, _t("Error during image crop"), "WLPlugGmagick->transform:CROP()");
					return false;
				}
				break;
			# -----------------------
			case "FLIP":
				$dir = strtolower($parameters["direction"]);
				
				if ($dir == 'vertical') {
					if (!$this->handle->flipimage()) {
						$this->postError(1610, _t("Error during vertical image flip"), "WLPlugGmagick->transform:FLIP()");
						return false;
					}
				} else {
					if (!$this->handle->flopimage()) {
						$this->postError(1610, _t("Error during horizontal image flip"), "WLPlugGmagick->transform:FLIP()");
						return false;
					}
				}
				
				break;
			# -----------------------
			case "UNSHARPEN_MASK":
				# noop
				break;
			# -----------------------
			case "SET":
				while(list($k, $v) = each($parameters)) {
					$this->set($k, $v);
				}
				break;
			# -----------------------
			}
			return 1;
		} catch (Exception $e) {
			$this->postError(1610, _t("Gmagick exception"), "WLPlugGmagick->transform");
			return false;
		}
	}
	# ----------------------------------------------------------
	public function write($ps_filepath, $mimetype) {
		if (!$this->handle) { return false; }
		if(strpos($ps_filepath, ':') && (caGetOSFamily() != OS_WIN32)) {
			$this->postError(1610, _t("Filenames with colons (:) are not allowed"), "WLPlugGmagick->write()");
			return false;
		}
		if ($mimetype == "image/tilepic") {
			if ($this->properties["mimetype"] == "image/tilepic") {
				copy($this->filepath, $ps_filepath);
			} else {
				$tp = new TilepicParser();
				if (!($properties = $tp->encode($this->filepath, $ps_filepath, 
					array(
						"tile_width" => $this->properties["tile_width"],
						"tile_height" => $this->properties["tile_height"],
						"layer_ratio" => $this->properties["layer_ratio"],
						"quality" => $this->properties["quality"],
						"antialiasing" => $this->properties["antialiasing"],
						"output_mimetype" => $this->properties["tile_mimetype"],
						"layers" => $this->properties["layers"],
					)					
				))) {
					$this->postError(1610, $tp->error, "WLPlugTilepic->write()");	
					return false;
				}
			}
			# update mimetype
			foreach($properties as $k => $v) {
				$this->properties[$k] = $v;
			}
			$this->properties["mimetype"] = "image/tilepic";
			$this->properties["typename"] = "Tilepic";
			return 1;
		} else {
			# is mimetype valid?
			if (!($ext = $this->info["EXPORT"][$mimetype])) {
				# this plugin can't write this mimetype
				return false;
			} 
			
			$this->handle->setimageformat($this->magick_names[$mimetype]);
			# set quality
			if (($this->properties["quality"]) && ($this->properties["mimetype"] != "image/tiff")){ 
				$this->handle->setcompressionquality($this->properties["quality"]);
			}
			
			$this->handle->setimagebackgroundcolor(new GmagickPixel("#CC0000"));
		
			if ($this->properties['gamma']) {
				if (!$this->properties['reference-black']) { $this->properties['reference-black'] = 0; }
				if (!$this->properties['reference-white']) { $this->properties['reference-white'] = 65535; }
				$this->handle->levelimage($this->properties['reference-black'], $this->properties['gamma'], $this->properties['reference-white']);
			}
			
			if (($this->properties["colorspace"]) && ($this->properties["colorspace"] != "default")){ 
				$vn_colorspace = null;
				switch($this->properties["colorspace"]) {
					case 'greyscale':
					case 'grey':
						$vn_colorspace = Gmagick::COLORSPACE_GRAY;
						break;
					case 'RGB':
					case 'color':
						$vn_colorspace = Gmagick::COLORSPACE_RGB;
						break;
					case 'sRGB':
						$vn_colorspace = Gmagick::COLORSPACE_SRGB;
						break;
					case 'CMYK':
						$vn_colorspace = Gmagick::COLORSPACE_CMYK;
						break;
					case 'bitonal':
						$vn_colorspace = Gmagick::COLORSPACE_GRAY;
						$this->handle->setimagedepth(1);
						break;
				}
				if ($vn_colorspace) { $this->handle->setimagecolorspace($vn_colorspace); }
			}
			
			$this->handle->stripimage();	// remove all lingering metadata
			
			# write the file
			try {
				if (!$this->handle->writeimage($ps_filepath.".".$ext)) {
					$this->postError(1610, _t("Error writing file"), "WLPlugGmagick->write()");
					return false;
				}

				if(!file_exists($ps_filepath.".".$ext)){
					if($this->handle->getnumberimages() > 1){
						if(file_exists($ps_filepath."-1.".$ext)){
							@rename($ps_filepath."-0.".$ext,$ps_filepath.".".$ext);
							$this->properties["mimetype"] = $mimetype;
							$this->properties["typename"] = $this->handle->getimageformat();

							// get rid of other pages
							$i = 1;
							while(file_exists($vs_f = $ps_filepath."-".$i.".".$ext)){
								@unlink($vs_f);
								$i++;
							}

							return $ps_filepath.".".$ext;
						}
					}

					$this->postError(1610, _t("Error writing file"), "WLPlugGmagick->write()");
					return false;
				}
			} catch (Exception $e) {
				$this->postError(1610, _t("Error writing file: %1", $e->getMessage()), "WLPlugGmagick->write()");
				return false;
			}
			
			# update mimetype
			$this->properties["mimetype"] = $mimetype;
			$this->properties["typename"] = $this->handle->getimageformat();
			
			return $ps_filepath.".".$ext;
		}
	}
	# ------------------------------------------------
	/** 
	 *
	 */
	# This method must be implemented for plug-ins that can output preview frames for videos or pages for documents
	public function &writePreviews($ps_filepath, $pa_options) {
		$vo_plugin = null;

		// gmagick multi-image object traversal seems to be broken
		// the traversal works but writeimage always takes the first image in a sequence no matter where you set the pointer (with nextimage())
		// until this is fixed, we're going to try and use one of the other plugins to do this
		
		if(caMediaPluginGraphicsMagickInstalled($this->ops_graphicsmagick_path)){
			$vo_plugin = new WLPlugMediaGraphicsMagick();
		} else if(caMediaPluginImagickInstalled()){
			$vo_plugin = new WLPlugMediaImagick();
		} else if(caMediaPluginImageMagickInstalled($this->ops_imagemagick_path)){
			$vo_plugin = new WLPlugMediaImageMagick();
		}

		if(is_object($vo_plugin) && file_exists($this->filepath)){
			$vo_plugin->register();

			// read original file
			$vo_plugin->divineFileFormat($this->filepath);
			$vo_plugin->read($this->filepath);
			$va_return = $vo_plugin->writePreviews($this->filepath,$pa_options);
			
			return $va_return;
		} else {
			return null;
		}
	}
	# ------------------------------------------------
	public function joinArchiveContents($pa_files, $pa_options = array()) {
		// the gmagick multi image feature seems broken -> try and use one of the other plugins to do this
		$vo_plugin = null;

		if(caMediaPluginGraphicsMagickInstalled($this->ops_graphicsmagick_path)){
			$vo_plugin = new WLPlugMediaGraphicsMagick();
		} else if(caMediaPluginImagickInstalled()){
			$vo_plugin = new WLPlugMediaImagick();
		} else if(caMediaPluginImageMagickInstalled($this->ops_imagemagick_path)){
			$vo_plugin = new WLPlugMediaImageMagick();
		}

		if(is_object($vo_plugin)){
			$vo_plugin->register();
			return $vo_plugin->joinArchiveContents($pa_files,$pa_options);	
		} else {
			return false;
		}
	}
	# ------------------------------------------------
	public function getOutputFormats() {
		return $this->info["EXPORT"];
	}
	# ------------------------------------------------
	public function getTransformations() {
		return $this->info["TRANSFORMATIONS"];
	}
	# ------------------------------------------------
	public function getProperties() {
		return $this->info["PROPERTIES"];
	}
	# ------------------------------------------------
	public function mimetype2extension($mimetype) {
		return $this->info["EXPORT"][$mimetype];
	}
	# ------------------------------------------------
	public function extension2mimetype($extension) {
		reset($this->info["EXPORT"]);
		while(list($k, $v) = each($this->info["EXPORT"])) {
			if ($v === $extension) {
				return $k;
			}
		}
		return "";
	}
	# ------------------------------------------------
	private function _getColorspaceAsString($pn_colorspace) {
		switch($pn_colorspace) {
			case Gmagick::COLORSPACE_UNDEFINED:
				$vs_colorspace = 'UNDEFINED';
				break;
			case Gmagick::COLORSPACE_RGB:
				$vs_colorspace = 'RGB';
				break;
			case Gmagick::COLORSPACE_GRAY:
				$vs_colorspace = 'GRAY';
				break;
			case Gmagick::COLORSPACE_TRANSPARENT:
				$vs_colorspace = 'TRANSPARENT';
				break;
			case Gmagick::COLORSPACE_OHTA:
				$vs_colorspace = 'OHTA';
				break;
			case Gmagick::COLORSPACE_LAB:
				$vs_colorspace = 'LAB';
				break;
			case Gmagick::COLORSPACE_XYZ:
				$vs_colorspace = 'XYZ';
				break;
			case Gmagick::COLORSPACE_YCBCR:
				$vs_colorspace = 'YCBCR';
				break;
			case Gmagick::COLORSPACE_YCC:
				$vs_colorspace = 'YCC';
				break;
			case Gmagick::COLORSPACE_YIQ:
				$vs_colorspace = 'YIQ';
				break;
			case Gmagick::COLORSPACE_YPBPR:
				$vs_colorspace = 'YPBPR';
				break;
			case Gmagick::COLORSPACE_YUV:
				$vs_colorspace = 'YUV';
				break;
			case Gmagick::COLORSPACE_CMYK:
				$vs_colorspace = 'CMYK';
				break;
			case Gmagick::COLORSPACE_SRGB:
				$vs_colorspace = 'SRGB';
				break;
			/*case Gmagick::COLORSPACE_HSB:
				$vs_colorspace = 'HSB';
				break;*/
			case Gmagick::COLORSPACE_HSL:
				$vs_colorspace = 'HSL';
				break;
			case Gmagick::COLORSPACE_HWB:
				$vs_colorspace = 'HWB';
				break;
			case Gmagick::COLORSPACE_REC601LUMA:
				$vs_colorspace = 'REC601LUMA';
				break;
			case Gmagick::COLORSPACE_REC709LUMA:
				$vs_colorspace = 'REC709LUMA';
				break;
			case Gmagick::COLORSPACE_LOG:
				$vs_colorspace = 'LOG';
				break;
			default:
				$vs_colorspace = 'UNKNOWN';
				break;
		}
		return $vs_colorspace;
	}
	# ------------------------------------------------
	public function mimetype2typename($mimetype) {
		return $this->typenames[$mimetype];
	}
	# ------------------------------------------------
	public function reset() {
		if ($this->ohandle) {
			$this->handle = clone $this->ohandle;
			# load image properties
			$va_tmp = $this->handle->getimagegeometry();
			$this->properties["width"] = $va_tmp['width'];
			$this->properties["height"] = $va_tmp['height'];
			$this->properties["quality"] = "";
			$this->properties["mimetype"] = $this->_getMagickImageMimeType($this->handle);
			$this->properties["typename"] = $this->handle->getimageformat();
			$this->properties["faces"] = $this->opa_faces;
			return 1;
		}
		return false;
	}
	# ------------------------------------------------
	public function init() {
		unset($this->handle);
		unset($this->ohandle);
		unset($this->properties);
		unset($this->filepath);
		
		$this->metadata = array();
		$this->errors = array();
		$this->opa_faces = null;
	}
	# ------------------------------------------------
	private function setResourceLimits($po_handle) {
		$po_handle->setResourceLimit(Gmagick::RESOURCETYPE_MEMORY, 1024*1024*1024);		// Set maximum amount of memory in bytes to allocate for the pixel cache from the heap.
		$po_handle->setResourceLimit(Gmagick::RESOURCETYPE_MAP, 1024*1024*1024);		// Set maximum amount of memory map in bytes to allocate for the pixel cache.
		$po_handle->setResourceLimit(Gmagick::RESOURCETYPE_AREA, 6144*6144);			// Set the maximum width * height of an image that can reside in the pixel cache memory.
		$po_handle->setResourceLimit(Gmagick::RESOURCETYPE_FILE, 1024);					// Set maximum number of open pixel cache files.
		$po_handle->setResourceLimit(Gmagick::RESOURCETYPE_DISK, 64*1024*1024*1024);					// Set maximum amount of disk space in bytes permitted for use by the pixel cache.	

		return true;
	}
	# ------------------------------------------------
	public function cleanup() {
		$this->destruct();
	}
	# ------------------------------------------------
	public function destruct() {
		if(is_object($this->handle)) { $this->handle->destroy(); }
		if(is_object($this->ohandle)) { $this->ohandle->destroy(); }
	}
	# ------------------------------------------------
	public function htmlTag($ps_url, $pa_properties, $pa_options=null, $pa_volume_info=null) {
		if (!is_array($pa_options)) { $pa_options = array(); }
		if (!is_array($pa_properties)) { $pa_properties = array(); }
		return caHTMLImage($ps_url, array_merge($pa_options, $pa_properties));
	}	
	# ------------------------------------------------
	private function _dcrawConvertToTiff($ps_filepath) {
		if (!caMediaPluginDcrawInstalled($this->ops_dcraw_path)) {
			$this->postError(1610, _t("Could not convert Camera RAW format file because conversion tool (dcraw) is not installed"), "WLPlugGmagick->read()");
			return false;
		}

		$vs_tmp_name = tempnam(caGetTempDirPath(), "rawtmp");
		if (!copy($ps_filepath, $vs_tmp_name)) {
			$this->postError(1610, _t("Could not copy Camera RAW file to temporary directory"), "WLPlugGmagick->read()");
			return false;
		}
		exec($this->ops_dcraw_path." -T ".caEscapeShellArg($vs_tmp_name), $va_output, $vn_return);
		if ($vn_return != 0) {
			$this->postError(1610, _t("Camera RAW file conversion failed: %1", $vn_return), "WLPlugGmagick->read()");
			return false;
		}
		if (!(file_exists($vs_tmp_name.'.tiff') && (filesize($vs_tmp_name.'.tiff') > 0))) {
			$this->postError(1610, _t("Translation from Camera RAW to TIFF failed"), "WLPlugGmagick->read()");
			return false;
		}

		return $vs_tmp_name.'.tiff';
	}
	# ----------------------------------------------------------------------
	private function _gmagickRead($ps_filepath) {
		try {
			$handle = new Gmagick($ps_filepath);
			$this->setResourceLimits($handle);
			$handle->setimageindex(0);        // force use of first image in multi-page TIFF
			$this->handle = $handle;
			$this->filepath = $ps_filepath;

			$this->metadata = array();

		if (WLPlugMediaGmagick::$s_metadata_read_cache[$ps_filepath]) {
			$this->metadata = WLPlugMediaGmagick::$s_metadata_read_cache[$ps_filepath];
		} else {
			// handle metadata

			/* EXIF */
			if(function_exists('exif_read_data') && !($this->opo_config->get('dont_use_exif_read_data'))) {
			    $va_exif_data = @exif_read_data($ps_filepath, 'IFD0', true, false);
			    $vn_exif_size = strlen(print_R($va_exif_data, true));
				if (($vn_exif_size <= $this->opo_config->get('dont_use_exif_read_data_if_larger_than')) && (is_array($va_exif = caSanitizeArray($va_exif_data)))) { $va_metadata['EXIF'] = $va_exif; }
			}

			// if the builtin EXIF extraction is not used or failed for some reason, try ExifTool
			if(!isset($va_metadata['EXIF']) || !is_array($va_metadata['EXIF'])) {
				if(caExifToolInstalled()) {
					$va_metadata['EXIF'] = caExtractMetadataWithExifTool($ps_filepath, true);
				}
			}

			// Rotate incoming image as needed

			if (isset($va_metadata['EXIF']['IFD0']['Orientation'])) {
				$vn_orientation = $va_metadata['EXIF']['IFD0']['Orientation'];
				$vs_tmp_basename = tempnam(caGetTempDirPath(), 'ca_image_tmp');

				$vb_is_rotated = false;
				switch ($vn_orientation) {
					case 3:
						$this->handle->rotateimage("#FFFFFF", 180);
						unset($va_metadata['EXIF']['IFD0']['Orientation']);
						$vb_is_rotated = true;
						break;
					case 6:
						$this->handle->rotateimage("#FFFFFF", 90);
						unset($va_metadata['EXIF']['IFD0']['Orientation']);
						$vb_is_rotated = true;
						break;
					case 8:
						$this->handle->rotateimage("#FFFFFF", -90);
						unset($va_metadata['EXIF']['IFD0']['Orientation']);
						$vb_is_rotated = true;
						break;
				}

				if ($vb_is_rotated) {
					if ($this->handle->writeimage($vs_tmp_basename)) {
						$va_tmp = $this->handle->getimagegeometry();
						$this->properties["faces"] = $this->opa_faces = caDetectFaces($vs_tmp_basename, $va_tmp['width'], $va_tmp['height']);
					}
					@unlink($vs_tmp_basename);
				}
			}

			// get XMP
			$o_xmp = new XMPParser();
			if ($o_xmp->parse($ps_filepath)) {
				if (is_array($va_xmp_metadata = $o_xmp->getMetadata()) && sizeof($va_xmp_metadata)) {
					$va_metadata['XMP'] = array();
					foreach($va_xmp_metadata as $vs_xmp_tag => $va_xmp_values) {
						$va_metadata['XMP'][$vs_xmp_tag] = join('; ',$va_xmp_values);
					}

				}
			}

			// try to get IPTC and DPX with GraphicsMagick, if available
			 if(caMediaPluginGraphicsMagickInstalled()) {
 				/* IPTC metadata */
 				$vs_iptc_file = tempnam(caGetTempDirPath(), 'gmiptc');
 				@rename($vs_iptc_file, $vs_iptc_file.'.iptc');  // GM uses the file extension to figure out what we want
 				$vs_iptc_file .= '.iptc';
 				exec($this->ops_graphicsmagick_path." convert ".caEscapeShellArg($ps_filepath)." ".caEscapeShellArg($vs_iptc_file).(caIsPOSIX() ? " 2> /dev/null" : ""), $va_output, $vn_return);
 
 				$vs_iptc_data = file_get_contents($vs_iptc_file);
 				@unlink($vs_iptc_file);
 
 				$va_iptc_raw = iptcparse($vs_iptc_data);
 
 				$va_iptc_tags = array(
 					'2#004'=>'Genre',
 					'2#005'=>'DocumentTitle',
 					'2#010'=>'Urgency',
 					'2#015'=>'Category',
 					'2#020'=>'Subcategories',
 					'2#025'=>'Keywords',
 					'2#040'=>'SpecialInstructions',
 					'2#055'=>'CreationDate',
 					'2#060'=>'TimeCreated',
 					'2#080'=>'AuthorByline',
 					'2#085'=>'AuthorTitle',
 					'2#090'=>'City',
 					'2#095'=>'State',
 					'2#100'=>'CountryCode',
 					'2#101'=>'Country',
 					'2#103'=>'OTR',
 					'2#105'=>'Headline',
 					'2#110'=>'Credit',
 					'2#115'=>'PhotoSource',
 					'2#116'=>'Copyright',
 					'2#120'=>'Caption',
 					'2#122'=>'CaptionWriter'
 				);
 
 				$va_iptc = array();
 				if (is_array($va_iptc_raw)) {
 					foreach($va_iptc_raw as $vs_iptc_tag => $va_iptc_tag_data){
 						if(isset($va_iptc_tags[$vs_iptc_tag])) {
 							$va_iptc[$va_iptc_tags[$vs_iptc_tag]] = join('; ',$va_iptc_tag_data);
 						}
 					}
 				}
 
 				if (sizeof($va_iptc)) {
 					$va_metadata['IPTC'] = $va_iptc;
 				}
 
 				/* DPX metadata */
 				exec($this->ops_graphicsmagick_path." identify -format '%[DPX:*]' ".caEscapeShellArg($ps_filepath).(caIsPOSIX() ? " 2> /dev/null" : ""), $va_output, $vn_return);
 				if ($va_output[0]) { $va_metadata['DPX'] = $va_output; }
 			}

			if (sizeof(WLPlugMediaGmagick::$s_metadata_read_cache) > 100) { WLPlugMediaGmagick::$s_metadata_read_cache = array_slice(WLPlugMediaGmagick::$s_metadata_read_cache, 50); }
			$this->metadata = WLPlugMediaGmagick::$s_metadata_read_cache[$ps_filepath] = $va_metadata;
		}

			return $handle;
		} catch(Exception $e) {
			$this->postError(1610, _t("Could not read image file"), "WLPlugGmagick->read()");
			return false; // gmagick couldn't read file, presumably
		}
	}
}
# ----------------------------------------------------------------------
