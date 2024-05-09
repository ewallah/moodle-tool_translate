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
 * This file contains the classes for the admin settings of the translate tool.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_translate;

use context_system;
use flexible_table;
use html_writer;
use moodle_url;
use pix_icon;

/**
 * Manage engine plugins
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin_manager {
    /** @var moodle_url pageurl */
    private $pageurl;

    /**
     * Constructor
     */
    public function __construct() {
        global $CFG;
        require_once($CFG->dirroot . '/lib/adminlib.php');
        $this->pageurl = new moodle_url('/admin/tool/translate/adminmanageplugins.php');
    }

    /**
     * This is the entry point for this controller class.
     *
     * @param string $action - The action to perform
     * @param string $plugin - Optional name of a plugin type to perform the action on
     * @return None
     */
    public function execute($action, $plugin) {
        global $OUTPUT;
        $this->check_permissions();
        if ($plugin != null) {
            switch ($action) {
                case 'hide':
                    $this->hide_plugin($plugin);
                    break;
                case 'moveup':
                    $this->move_plugin($plugin, 'up');
                    break;
                case 'movedown':
                    $this->move_plugin($plugin, 'down');
                    break;
                case 'show':
                    $this->show_plugin($plugin);
                    break;
            }
        } else {
            echo $OUTPUT->header();
            echo $OUTPUT->heading(get_string('manageengines', 'tool_translate'));
            $this->view_plugins_table();
            echo $OUTPUT->footer();
        }
    }

    /**
     * Write the HTML for the submission plugins table.
     *
     * @return None
     */
    private function view_plugins_table() {
        global $OUTPUT, $CFG;
        require_once($CFG->libdir . '/tablelib.php');

        // Set up the table.
        $table = new flexible_table('pluginsadminttable');
        $table->define_baseurl($this->pageurl);
        $table->define_columns(['pluginname', 'version', 'languages', 'hideshow', 'order', 'settings', 'uninstall']);
        $table->define_headers([get_string('name'), get_string('version'), get_string('supportedlangs', 'tool_translate'),
            get_string('hideshow', 'tool_translate'), get_string('order'), get_string('settings'),
            get_string('uninstallplugin', 'core_admin'), ]);
        $table->set_attribute('id', 'plugins');
        $table->set_attribute('class', 'admintable generaltable');
        $table->setup();

        $plugins = $this->get_sorted_plugins_list();
        $s = get_string('settings');
        foreach ($plugins as $idx => $plugin) {
            $row = [];
            $class = '';
            $sub = 'translateengine_' . $plugin;

            $row[] = get_string('pluginname', $sub);
            $row[] = get_config($sub, 'version');

            $engine = '\translateengine_' . $plugin . '\engine';
            $engine = new $engine(null);
            $alllangs = [];
            foreach (array_keys($engine->supported_langs()) as $key) {
                $alllangs[] = get_string($key, 'core_iso6392');
            }
            $row[] = implode('; ', $alllangs);

            $visible = !get_config($sub, 'disabled');

            if ($visible) {
                $row[] = $this->format_icon_link('hide', $plugin, 'i/hide', get_string('disable'));
            } else {
                $row[] = $this->format_icon_link('show', $plugin, 'i/show', get_string('enable'));
                $class = 'dimmed_text';
            }

            $movelinks = '';
            if (!$idx == 0) {
                $movelinks .= $this->format_icon_link('moveup', $plugin, 't/up', get_string('up'));
            } else {
                $movelinks .= $OUTPUT->spacer(['width' => 16]);
            }
            if ($idx != count($plugins) - 1) {
                $movelinks .= $this->format_icon_link('movedown', $plugin, 't/down', get_string('down'));
            }
            $row[] = $movelinks;

            $exists = $row[1] != '' && file_exists($CFG->dirroot . "/admin/tool/translate/engine/$plugin/settings.php");
            $url = new moodle_url('/admin/settings.php', ['section' => $sub . '_settings']);
            $row[] = $exists ? html_writer::link($url, $s) : '&nbsp;';

            $row[] = $this->format_icon_link('delete', $plugin, 'i/trash', get_string('uninstallplugin', 'core_admin'));
            $table->add_data($row, $class);
        }
        $table->finish_output();
    }

    /**
     * Check this user has permission to edit the list of installed plugins
     *
     * @return None
     */
    private function check_permissions() {
        require_login();
        $systemcontext = context_system::instance();
        require_capability('moodle/site:config', $systemcontext);
    }

    /**
     * Hide this plugin.
     *
     * @param string $plugin - The plugin to hide
     */
    public function hide_plugin($plugin) {
        set_config('disabled', 1, 'translateengine_' . $plugin);
        \core_plugin_manager::reset_caches();
        PHPUNIT_TEST ? mtrace('Not redirected') : redirect($this->pageurl);
    }

    /**
     * Show this plugin.
     *
     * @param string $plugin - The plugin to show
     */
    public function show_plugin($plugin) {
        set_config('disabled', 0, 'translateengine_' . $plugin);
        \core_plugin_manager::reset_caches();
        PHPUNIT_TEST ? mtrace('Not redirected') : redirect($this->pageurl);
    }

    /**
     * Return a list of plugins sorted by the order defined in the admin interface
     *
     * @return array The list of plugins
     */
    public function get_sorted_plugins_list() {
        $names = \core_component::get_plugin_list('translateengine');
        $result = [];
        foreach ($names as $name => $location) {
            if (file_exists($location)) {
                $idx = get_config('translateengine_' . $name, 'sortorder');
                if (!$idx) {
                    $idx = 0;
                }
                while (array_key_exists($idx, $result)) {
                    $idx += 1;
                }
                $result[$idx] = $name;
            }
        }
        ksort($result);
        return $result;
    }

    /**
     * Return the first enabled engine
     *
     * @param stdClass $course
     * @return \tool/translate/engine A translation engine
     */
    public function get_enabled_plugin($course) {
        $names = $this->get_sorted_plugins_list();
        foreach ($names as $name) {
            $engine = '\translateengine_' . $name . '\engine';
            $engine = new $engine($course);
            if ($engine->is_configured()) {
                return $engine;
            }
        }
        return new \translateengine_aws\engine($course);
    }

    /**
     * Util function for writing an action icon link
     *
     * @param string $action URL parameter to include in the link
     * @param string $plugin URL parameter to include in the link
     * @param string $icon The key to the icon to use (e.g. 't/up')
     * @param string $alt The string description of the link used as the title and alt text
     * @return string The icon/link
     */
    private function format_icon_link($action, $plugin, $icon, $alt) {
        global $OUTPUT;
        $url = $this->pageurl;
        if ($action === 'delete') {
            $url = \core_plugin_manager::instance()->get_uninstall_url('translateengine_' . $plugin, 'manage');
            return ($url) ? html_writer::link($url, get_string('uninstallplugin', 'core_admin')) : '&nbsp;';
        }

        return $OUTPUT->action_icon(
            new moodle_url($url, ['action' => $action, 'plugin' => $plugin, 'sesskey' => sesskey()]),
            new pix_icon($icon, $alt, 'moodle', ['title' => $alt]),
            null,
            ['title' => $alt]
        ) . ' ';
    }

    /**
     * Change the order of this plugin.
     *
     * @param string $plugintomove - The plugin to move
     * @param string $dir - up or down
     * @return string The next page to display
     */
    public function move_plugin($plugintomove, $dir) {
        $plugins = $this->get_sorted_plugins_list();
        $currentindex = 0;
        $plugins = array_values($plugins);
        foreach ($plugins as $key => $plugin) {
            if ($plugin == $plugintomove) {
                $currentindex = $key;
                break;
            }
        }
        if ($dir == 'up') {
            if ($currentindex > 0) {
                $tempplugin = $plugins[$currentindex - 1];
                $plugins[$currentindex - 1] = $plugins[$currentindex];
                $plugins[$currentindex] = $tempplugin;
            }
        } else if ($dir == 'down') {
            if ($currentindex < (count($plugins) - 1)) {
                $tempplugin = $plugins[$currentindex + 1];
                $plugins[$currentindex + 1] = $plugins[$currentindex];
                $plugins[$currentindex] = $tempplugin;
            }
        }

        // Save the new normal order.
        foreach ($plugins as $key => $plugin) {
            set_config('sortorder', $key, 'translateengine_' . $plugin);
        }
        PHPUNIT_TEST ? mtrace('Not redirected') : redirect($this->pageurl);
    }
}
