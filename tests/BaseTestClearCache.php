<?php
/*
 * ----------------------------------------------------------------------
 * BaseTestClearCache.php
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

class BaseTestClearCache extends TestCase {

    protected function setUp(): void {
        parent::setUp();

        $o_config = Configuration::load();

        $va_cache_folders = [];

        if (defined('__CA_CACHE_BACKEND__')) {
            if (__CA_CACHE_BACKEND__==='file') {
                $va_cache_folders[] = __CA_CACHE_FILEPATH__;
            }
        }

        if (($vs_tmp_directory = $o_config->get('ajax_media_upload_tmp_directory'))
                && (file_exists($vs_tmp_directory))) {
            $va_cache_folders[] = $vs_tmp_directory;
        }
        if (($vs_tmp_directory = $o_config->get('ajax_media_upload_tmp_directory'))
                && file_exists($vs_tmp_directory)) {
            $va_cache_folders[] = $vs_tmp_directory;
        }

        foreach ($va_cache_folders as $va_cache_folder) {
            caRemoveDirectory($va_cache_folder, false);
        }
        PersistentCache::flush();
    }

}