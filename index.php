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
 * Course translate.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$courseid = required_param('course', PARAM_INT);
$course = get_course($courseid);
$context = context_course::instance($courseid);
$action = optional_param('action', '', PARAM_ALPHA);
$title = get_string('pluginname', 'tool_translate');
$source = optional_param('source', $CFG->lang, PARAM_ALPHA);
$target = optional_param('target', current_language(), PARAM_ALPHA);

// Check permissions.
require_login($course);
require_capability('tool/translate:translate', $context);

$url = '/admin/tool/translate/index.php';
$arr = [
    'course' => $course->id,
    'target' => strtolower($source),
    'source' => strtolower($target),
];
$nourl = new \moodle_url($url, $arr);
$out = '';
$PAGE->set_context($context);
$PAGE->set_url($nourl);
$PAGE->set_pagelayout('report');
$PAGE->set_title($title);
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();

$table = new \tool_translate\output\translation_table($course);
if ($action === 'translate') {
    if (confirm_sesskey()) {
        $sectionid = optional_param('sectionid', 0, PARAM_INT);
        core_php_time_limit::raise();
        raise_memory_limit(MEMORY_EXTRA);
        if ($sectionid > 0) {
            // Translate section.
            $out = $table->translate_section($sectionid);
        } else {
            $cmid = optional_param('cmid', 0, PARAM_INT);
            $out = ($cmid > 0) ? $table->translate_module($cmid) : $table->translate_other();
        }
    }
}

if ($out !== '') {
    echo $OUTPUT->notification($out, 'succes');
}
$table->filldata();
echo html_writer::table($table);
echo $OUTPUT->footer($course);

// Trigger a tool viewed event.
$event = \tool_translate\event\tool_viewed::create(['context' => $context]);
$event->trigger();
