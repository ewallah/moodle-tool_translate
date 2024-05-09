<?php
// This file is part of translate plugins for Moodle - http://moodle.org/
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
 * Engine Awstranslate settings.
 *
 * @package   translateengine_aws
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (is_siteadmin()) {
    $trans = get_string_manager()->get_list_of_translations();
    if (count($trans) > 1 || PHPUNIT_TEST) {
        $s = 'translateengine_aws';
        $t = get_strings(['pluginname', 'access_key', 'secret_key', 'region'], $s);
        $regions = [
            'us-west-1' => 'US West (N. California)',
            'ap-east-1' => 'Asia Pacific (Hong Kong)',
            'ap-southeast-2' => 'Asia Pacific (Sydney)',
            'eu-north-1' => 'Europe (Stockholm)',
            'eu-west-2' => 'Europe (London)',
            'eu-west-3' => 'Europe (Paris)', ];

        $settings = new admin_settingpage($s . '_settings', $t->pluginname);
        $setting = new admin_setting_configtext("$s/access_key", $t->access_key, '', '');
        $settings->add($setting);
        $setting = new admin_setting_configpasswordunmask("$s/secret_key", $t->secret_key, '', '');
        $settings->add($setting);
        $setting = new admin_setting_configselect("$s/region", $t->region, '', 'us-west-1', $regions);
        $settings->add($setting);
    }
}
