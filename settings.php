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
 * Creates a link to the upload form on the settings page.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $url = new moodle_url('/admin/tool/translate/adminmanageplugins.php');
    $ADMIN->add('language', new admin_category('translateengines', new lang_string('settings', 'tool_translate')));
    $ADMIN->add('translateengines', new admin_externalpage('translateengine', new lang_string('engines', 'tool_translate'), $url));
    foreach (core_plugin_manager::instance()->get_plugins_of_type('translateengine') as $plugin) {
        /** @var \tool_log\plugininfo\logstore $plugin */
        $plugin->load_settings($ADMIN, 'translateengines', $hassiteconfig);
    }
}
