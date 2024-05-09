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
 * The translate module translated event.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_translate\event;

use moodle_url;

/**
 * The translate module translated event.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class module_translated extends \core\event\base {
    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventmoduletranslated', 'tool_translate');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $cm = get_coursemodule_from_id(null, $this->contextinstanceid, $this->courseid, false, MUST_EXIST);
        return "The user with id '$this->userid' translated the $cm->modname with id $cm->id.";
    }

    /**
     * Returns relevant URL.
     *
     * @return moodle_url
     */
    public function get_url() {
        $cm = get_coursemodule_from_id(null, $this->contextinstanceid, $this->courseid, false, MUST_EXIST);
        return new moodle_url('/course/modedit.php', ['update' => $cm->id]);
    }
}
