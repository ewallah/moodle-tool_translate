<?php
// This file is part of Moodle - http://moodle.org/
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
 * Translation of modules.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_translate\output;

use core\output\notification;
use context_course;
use context_module;
use html_table;
use html_table_cell;
use html_table_row;
use html_writer;
use moodle_url;
use stdClass;


/**
 * Translation of modules.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class translation_table extends html_table {
    /** @var stdClass course */
    protected $course;
    /** @var stdClass engine */
    public $engine;
    /** @var int words */
    protected $words = 0;
    /** @var int letters */
    public $letters = 0;
    /** @var int counter */
    protected $counter = 0;
    /** @var string source */
    protected $source = 'en';
    /** @var string target */
    protected $target = 'fr';


    /**
     * Constructor
     *
     * @param course $course
     */
    public function __construct($course) {
        global $CFG, $OUTPUT;
        parent::__construct('translate');
        $this->source = optional_param('source', $CFG->lang, PARAM_ALPHA);
        $this->source = strtolower($this->source);
        $this->target = optional_param('target', current_language(), PARAM_ALPHA);
        $this->target = strtolower($this->target);
        $this->course = $course;
        $this->caption = get_string('pluginname', 'tool_translate');
        $this->head = ['', '', '', get_string('words', 'tool_translate'), get_string('price', 'tool_translate')];
        $this->colclasses = ['mdl-left', 'mdl-left', 'mdl-right', 'mdl-right'];
        $pluginmanager = new \tool_translate\plugin_manager();
        $this->engine = $pluginmanager->get_enabled_plugin($course);
        if (!$this->engine->is_configured()) {
             $notify = new notification(get_string('noengine', 'tool_translate'), notification::NOTIFY_ERROR);
        } else {
             $notify = new notification($this->engine->get_name(), notification::NOTIFY_WARNING);
        }
        echo $OUTPUT->render($notify);
    }

    /**
     * Fill the data
     *
     */
    public function filldata() {
        global $OUTPUT;
        rebuild_course_cache($this->course->id, true);
        $icon = $OUTPUT->pix_icon('i/course', '', 'moodle', ['class' => 'icon']);
        $this->addrow($icon, get_string('course'), true, $this->engine->translate_other(), 'course', $this->course->id);
        get_fast_modinfo($this->course, -1, true);
        $spacer = $OUTPUT->pix_icon('spacer', '', 'moodle', ['class' => 'icon']);
        $courseformat = course_get_format($this->course);
        $modinfo = get_fast_modinfo($this->course->id, -1);
        $sections = $modinfo->get_section_info_all();
        $modinfosections = $modinfo->get_sections();
        $options = new stdClass();
        $options->noclean = true;
        $options->overflowdiv = true;
        $files = [];
        foreach ($sections as $key => $section) {
            $secid = $section->section;
            if (isset($modinfosections[$secid])) {
                $url = new moodle_url('/course/editsection.php', ['id' => $secid]);
                $url = html_writer::link($url, $courseformat->get_section_name($key));
                $icon = $OUTPUT->pix_icon('i/section', '', 'moodle', ['class' => 'icon']);
                $this->addrow($icon, $url, true, $this->engine->translate_section($section->id), 'section', $secid);
                foreach ($modinfosections[$key] as $cmid) {
                    $cm = $modinfo->cms[$cmid];
                    $icon = html_writer::empty_tag('img', ['src' => $cm->get_icon_url(), 'class' => 'icon']);
                    $url = html_writer::link($cm->url, $cm->get_formatted_name());
                    $this->addrow($spacer . $icon, $url, true, $this->engine->translate_module($cmid), 'module', $cmid);
                    if ($cm->modname == 'resource') {
                        $files[] = new html_table_row(['', $cm->get_formatted_name(), 0, 0]);
                    }
                }
            }
        }
        $icon = $OUTPUT->pix_icon('i/folder', '', 'moodle', ['class' => 'icon']);
        $this->addrow($icon, get_string('files'), false);
        // TODO: Translate PDF - DOC - $this->data[] = $files;.
        $this->data[] = new html_table_row(['', get_string('total'), $this->words, $this->engine->get_price($this->letters)]);
    }

    /**
     * Add a row
     *
     * @param string $icon
     * @param string $text
     * @param boolean $enabled
     * @param string $translation
     * @param string $id
     * @param string $value
     */
    private function addrow($icon, $text, $enabled = true, $translation = '', $id = '', $value = '') {
        $words = count_words($translation);
        $letters = count_letters($translation);
        $calc = $this->engine->get_price($letters);
        $cell = html_writer::checkbox($id, $letters, $enabled, null, ['id' => $id . $value]);
        $row = new html_table_row([$cell, $icon . ' ' . $text, $this->ibutton([]), $words, $calc]);
        $row->attributes['class'] = 'rowid' . $this->counter++;
        $this->data[] = $row;
        $this->words += $words;
        $this->letters += $letters;
    }

    /**
     * Create a ibutton
     *
     * @param array $params
     * @param string $action defaults to translate
     * @return html_table_cell
     */
    private function ibutton($params, $action = 'translate') {
        global $OUTPUT;
        $cell = '';
        if ($this->engine->is_configured()) {
            $params['course'] = $this->course->id;
            $params['action'] = $action;
            $params['source'] = $this->source;
            $params['target'] = $this->target;
            $cell = $OUTPUT->single_button(new moodle_url('/admin/tool/translate/index.php', $params), $this->target);
        }
        return new html_table_cell($cell);
    }

    /**
     * Translate all other fields
     *
     * @return string
     */
    public function translate_other(): string {
        $this->engine->counting = false;
        $this->engine->targetlang = $this->target;
        $this->engine->sourcelang = $this->source;
        $s = $this->engine->translate_other();
        $this->engine->counting = true;
        return $s;
    }

    /**
     * Translate section
     *
     * @param int $sectionid
     * @return string
     */
    public function translate_section($sectionid): string {
        $this->engine->counting = false;
        $this->engine->targetlang = $this->target;
        $this->engine->sourcelang = $this->source;
        $s = $this->engine->translate_section($sectionid);
        $this->engine->counting = true;
        return $s;
    }

    /**
     * Translate module
     *
     * @param int $moduleid
     * @return string
     */
    public function translate_module($moduleid): string {
        $this->engine->counting = false;
        $this->engine->targetlang = $this->target;
        $this->engine->sourcelang = $this->source;
        $s = $this->engine->translate_module($moduleid);
        $this->engine->counting = true;
        return $s;
    }

    /**
     * Translate full course
     *
     * @param string $sourcelang
     * @param string $targetlang
     * @return string
     */
    public function translate_all($sourcelang, $targetlang): string {
        $this->engine->counting = false;
        $this->engine->targetlang = $targetlang;
        $this->engine->sourcelang = $sourcelang;
        $s = $this->engine->translate_other();
        $modinfo = get_fast_modinfo($this->course->id, -1);
        $sections = $modinfo->get_section_info_all();
        $modinfosections = $modinfo->get_sections();
        foreach ($sections as $key => $section) {
            if (isset($modinfosections[$section->section])) {
                $s .= $this->engine->translate_section($section->id);
                foreach ($modinfosections[$key] as $cmid) {
                    $s .= $this->engine->translate_module($cmid);
                }
            }
        }
        $this->engine->counting = true;
        return $s;
    }
}
