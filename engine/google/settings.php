<?php
// This file is part of the translateengine_google plugin for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Engine Google translate settings.
 *
 * @package   translateengine_google
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (is_siteadmin()) {
    $trans = get_string_manager()->get_list_of_translations();
    if (count($trans) > 1 || PHPUNIT_TEST) {
        $s = 'translateengine_google';
        $settings = new admin_settingpage($s . '_settings', get_string('pluginname', 'translateengine_google'));
        $setting = new admin_setting_configtext("$s/googleapikey", get_string('googleapikey', 'translateengine_google'), '', '');
        $settings->add($setting);
    }
}
