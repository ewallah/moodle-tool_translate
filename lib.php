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
 * Library.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This function extends the navigation with the tool items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the tool
 * @param stdClass $context The context of the course
 */
function tool_translate_extend_navigation_course($navigation, $course, $context) {
    global $CFG;
    if (has_capability('tool/translate:translate', $context)) {
        $arr = [
            'course' => $course->id,
            'target' => strtolower(current_language()),
            'source' => strtolower($CFG->lang),
        ];
        $url = new \moodle_url('/admin/tool/translate/index.php', $arr);
        $txt = get_string('translate', 'tool_translate');
        $navigation->add($txt, $url, navigation_node::NODETYPE_LEAF, null, null, new \pix_icon('i/edit', ''));
    }
}
