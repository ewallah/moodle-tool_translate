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
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_translate\output;

use \core\output\notification;
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
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class translation_table extends html_table {

    /** @var stdClass course */
    protected $course;
    /** @var stdClass engine */
    protected $engine;
    /** @var int words */
    protected $words = 0;
    /** @var int letters */
    protected $letters = 0;
    /** @var int counter */
    protected $counter = 0;


    /**
     * Constructor
     *
     * @param course $course
     */
    public function __construct($course) {
        global $CFG, $OUTPUT;
        parent::__construct('translate');
        $this->course = $course;
        $this->caption = get_string('pluginname', 'tool_translate');
        $this->head = ['', '', '' , 'Words', 'Price'];
        $this->colclasses = ['mdl-right', 'mdl-left', 'mdl-left', 'mdl-right', 'mdl-right'];
        $pluginmanager = new \tool_translate\plugin_manager();
        $this->engine = $pluginmanager->get_enabled_plugin($course);
        if ($CFG->lang != current_language()) {
            $this->engine->sourcelang = $CFG->lang;
            $this->engine->targetlang = current_language();
        }
        if (!$this->engine->is_configured()) {
             $notify = new notification('No engine configured', notification::NOTIFY_ERROR);
        } else {
             $notify = new notification($this->engine->get_name() . ' translation', notification::NOTIFY_WARNING);
        }
        echo $OUTPUT->render($notify);
    }

    /**
     * Fill the data
     *
     */
    public function filldata() {
        rebuild_course_cache($this->course->id, true);
        get_fast_modinfo($this->course, -1, true);

        $courseformat = course_get_format($this->course);
        $modinfo = get_fast_modinfo($this->course->id, -1);
        $this->addrow('', get_string('course'), $this->engine->translate_other(), []);
        $sections = $modinfo->get_section_info_all();
        $modinfosections = $modinfo->get_sections();
        $options = new stdClass();
        $options->noclean = true;
        $options->overflowdiv = true;
        foreach ($sections as $key => $section) {
            if (isset($modinfosections[$section->section])) {
                $url = new moodle_url('/course/editsection.php', ['id' => $section->id]);
                $url = html_writer::link($url, $courseformat->get_section_name($key));
                $this->addrow('', $url, $this->engine->translate_section($section->id), ['sectionid' => $section->id]);
                foreach ($modinfosections[$key] as $cmid) {
                    $cm = $modinfo->cms[$cmid];
                    $icon = html_writer::empty_tag('img', ['src' => $cm->get_icon_url(), 'class' => 'icon']);
                    $url = html_writer::link($cm->url, $cm->get_formatted_name());
                    $this->addrow($icon, $url, $this->engine->translate_module($cmid), ['cmid' => $cmid]);
                }
            }
        }
        $this->data[] = new html_table_row(['', '', get_string('total'), $this->words, $this->engine->get_price($this->letters)]);
    }

    /**
     * Add a row
     *
     * @param string $icon
     * @param string $text
     * @param string $translation
     * @param array $params
     */
    private function addrow($icon, $text, $translation = '', $params = []) {
        $words = count_words($translation);
        $letters = count_letters($translation);
        $calc = $this->engine->get_price($letters);
        $row = new html_table_row([$this->ibutton($params), $icon, $text, $words, $calc]);
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
            $params['target'] = current_language();
            $cell = $OUTPUT->single_button(new moodle_url('/admin/tool/translate/index.php', $params), current_language());
        }
        return new html_table_cell($cell);
    }

    /**
     * Translate all other fields
     *
     * @return string
     */
    public function translate_other(): string {
        return $this->engine->translate_other();
    }

    /**
     * Translate section
     *
     * @param int $sectionid
     * @return string
     */
    public function translate_section($sectionid): string {
        return $this->engine->translate_section($sectionid);
    }

    /**
     * Translate module
     *
     * @param int $moduleid
     * @return string
     */
    public function translate_module($moduleid): string {
        return $this->engine->translate_section($moduleid);
    }
}
