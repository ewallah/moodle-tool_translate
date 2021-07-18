<?php
// This file is part of the tool_translate plugin for Moodle - http://moodle.org/
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
 * Sub plugin info.
 *
 * @package   tool_translate
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_translate\plugininfo;

defined('MOODLE_INTERNAL') || die();

use core\plugininfo\base, moodle_url, part_of_admin_tree, admin_settingpage;

/**
 * Sub plugin info.
 *
 * @package   tool_translate
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class translateengine extends base {

    /**
     * Allow users to uninstall these plugins.
     *
     * @return bool
     */
    public function is_uninstall_allowed() {
        return true;
    }

    /**
     * This plugin is enabled.
     *
     * @return bool
     */
    public function is_enabled() {
        return true;
    }

    /**
     * Get the settings section name.
     *
     * @return string the settings section name.
     */
    public function get_settings_section_name() {
        return 'translateengine_' . $this->name;
    }

    /**
     * Loads plugin settings to the settings tree.
     *
     * @param \part_of_admin_tree $adminroot
     * @param string $parentnodename
     * @param bool $hassiteconfig whether the current user has moodle/site:config capability
     */
    public function load_settings(part_of_admin_tree $adminroot, $parentnodename, $hassiteconfig) {
        $ADMIN = $adminroot;

        if (!$this->is_installed_and_upgraded() or !$hassiteconfig or !file_exists($this->full_path('settings.php'))) {
            return;
        }

        $settings = new admin_settingpage($this->get_settings_section_name(), $this->displayname, 'moodle/site:config');
        include($this->full_path('settings.php'));

        if ($settings) {
            $ADMIN->add($parentnodename, $settings);
        }
    }

    /**
     * Get the url.
     *
     * @return moodle_url the manage section.
     */
    public static function get_manage_url() {
        return new moodle_url('/admin/settings.php', ['section' => 'translateengines']);
    }

}
