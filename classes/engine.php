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
 * Base class for translate engines.
 *
 * All translate engines must extend this class.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_translate;

use context_course;
use context_module;
use html_writer;
use moodle_exception;
use stdClass;

/**
 * Base class for translate engines.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class engine {
    /** @var stdClass course */
    protected $course;

    /** @var bool counting */
    public $counting = true;

    /** @var string targetlang */
    public $targetlang;

    /** @var string sourcelang */
    public $sourcelang;

    /**
     * Constructor
     *
     * @param course $course Course
     */
    public function __construct($course) {
        $this->course = $course;
    }

    /**
     * The name of this engine.
     */
    public function get_name(): string {
        $classname = str_ireplace('\engine', '', get_class($this));
        return get_string('pluginname', $classname);
    }

    /**
     * Is the translate engine fully configured and ready to use.
     */
    abstract public function is_configured(): bool;

    /**
     * Rough calculation of price.
     *
     * @param int $letters Count of letters
     */
    abstract public function get_price(int $letters): string;

    /**
     * Supported languages.
     *
     * @return string[] Array of suported source/target languages
     */
    public function supported_langs(): array {
        throw new moodle_exception('supported_langs not configured for this engine');
    }

    /**
     * Is language supported.
     * @param string $lang Language code
     */
    public function lang_supported(string $lang): bool {
        $values = array_values($this->supported_langs());
        if (!in_array($lang, $values, true)) {
            throw new moodle_exception('language not supported');
        }
        return true;
    }

    /**
     * Translate text.
     *
     * @param string $source The source language
     * @param string $target The target language
     * @param string $txt The text that has to be translated
     * @return string|null Translated text or nothing
     */
    abstract public function translatetext(string $source, string $target, string $txt): ?string;

    /**
     * Translate other
     */
    public function translate_other(): string {
        global $CFG, $DB;
        require_once($CFG->libdir . '/badgeslib.php');
        $id = $this->course->id;
        $context = context_course::instance($id);
        $s = $this->add_records('enrol', 'courseid', $id);
        $s .= $this->add_records('course', 'id', $id);
        $s .= $this->add_records('customfield_data', 'contextid', $context->id);
        // Badges.
        $s .= $this->add_records('badge', 'courseid', $id, ['description', 'message', 'messagesubject']);
        $badges = $DB->get_records('badge', ['type' => BADGE_TYPE_COURSE, 'courseid' => $id]);
        foreach ($badges as $badge) {
            $s .= $this->add_records('badge_criteria', 'badgeid', $badge->id);
            $s .= $this->add_records('badge_endorsement', 'badgeid', $badge->id, ['claimcomment']);
        }
        $s .= $this->add_records('certfificate', 'course', $id, ['customtext']);
        $s .= $this->add_records('grade_categories', 'courseid', $id, ['fullname']);
        $s .= $this->add_records('grade_items', 'courseid', $id, ['itemname']);
        $s .= $this->add_records('grade_outcomes', 'courseid', $id, ['shortname', 'fullname']);
        $s .= $this->add_records('groupings', 'courseid', $id);
        $s .= $this->add_records('groups', 'courseid', $id);
        $s .= $this->add_records('role_names', 'contextid', $context->id);
        $s .= $this->add_records('scales', 'courseid', $id, ['scale']);
        // Notes.
        $s .= $this->add_records('post', 'courseid', $id, ['content']);
        if ($this->counting) {
            return $s;
        }
        $event = event\course_translated::create(['context' => $context]);
        $event->trigger();
        rebuild_course_cache($id);
        return "{$s} <br/>Course with id {$id} translated all extra elements.";
    }

    /**
     * Translate section
     *
     * @param int $sectionid Section id
     */
    public function translate_section(int $sectionid): string {
        $s = $this->add_records('course_sections', 'id', $sectionid);

        if ($this->counting) {
            return $s;
        }

        $context = context_course::instance($this->course->id);
        $event = event\module_translated::create(['context' => $context, 'other' => ['sectionid' => $sectionid]]);
        $event->trigger();
        rebuild_course_cache($this->course->id);
        $courseformat = course_get_format($this->course)->get_format();
        return $s . get_string('sectionname', 'format_' . $courseformat) . " with id {$sectionid} translated";
    }

    /**
     * Translate module
     *
     * @param int $moduleid Modulle id
     */
    public function translate_module(int $moduleid): string {
        global $CFG;
        require_once($CFG->libdir . '/questionlib.php');
        $modinfo = get_fast_modinfo($this->course->id, -1);
        $cm = $modinfo->cms[$moduleid];
        $context = context_module::instance($cm->id);
        $s = $this->translate_record($cm->modname, $cm->instance);
        switch ($cm->modname) {
            case 'book':
                $s .= $this->add_records('book_chapters', 'bookid', $cm->instance);
                break;
            case 'checklist':
                $s .= $this->add_records('checklist_item', 'checklist', $cm->instance, ['displaytext']);
                break;
            case 'choice':
                $s .= $this->add_records('choice_options', 'choiceid', $cm->instance, ['text']);
                break;
            case 'feedback':
                $s .= $this->add_records('feedback_item', 'feedback', $cm->instance, ['label', 'presentation']);
                break;
            case 'forum':
                $s .= $this->add_records('forum_discussions', 'forum', $cm->instance);
                break;
            case 'glossary':
                $s .= $this->add_records('glossary_categories', 'glossaryid', $cm->instance);
                $s .= $this->add_records('glossary_entries', 'glossaryid', $cm->instance, ['concept']);
                break;
            case 'lesson':
                $s .= $this->add_records('lesson_pages', 'lessonid', $cm->instance);
                $s .= $this->add_records('lesson_answers', 'lessonid', $cm->instance);
                break;
            case 'quiz':
                $s .= $this->add_records('quiz_sections', 'quizid', $cm->instance, ['heading']);
                $s .= $this->add_records('quiz_feedback', 'quizid', $cm->instance);
                $questions = \mod_quiz\question\bank\qbank_helper::get_question_structure($cm->instance, $context);
                foreach ($questions as $question) {
                    if ($question->questionid) {
                        $sid = $question->questionid;
                        $s .= $this->add_records('question', 'id', $sid);
                        $s .= $this->add_records('question_answers', 'question', $sid);
                        $s .= $this->add_records('question_hints', 'questionid', $sid);
                        $s .= $this->add_records('question_order', 'question', $sid);
                        $s .= $this->add_records('question_order_sub', 'question', $sid);
                        $q = \question_bank::load_question($sid);
                        $qt = $q->qtype::class;
                        // Brute force collect feedback.
                        $s .= $this->add_records($qt, 'questionid', $sid);
                        $s .= $this->add_records($qt . '_options', 'questionid', $sid);
                        $s .= $this->add_records($qt . '_answers', 'questionid', $sid);
                        $s .= $this->add_records($qt . '_subquestions', 'questionid', $sid);
                        $qt = str_ireplace('qtype', 'question', $qt);
                        $s .= $this->add_records($qt, 'questionid', $sid);
                        $s .= $this->add_records($qt, 'question', $sid);
                        $s .= $this->add_records($qt . '_options', 'questionid', $sid);
                        $s .= $this->add_records($qt . '_answers', 'questionid', $sid);
                        $s .= $this->add_records($qt . '_subquestions', 'questionid', $sid);
                    }
                }
                break;
        }
        if ($this->counting) {
            return $s;
        }
        $event = event\module_translated::create(['context' => $context]);
        $event->trigger();
        rebuild_course_cache($this->course->id);
        $cm = $modinfo->cms[$moduleid];
        $url = html_writer::link($cm->url, $cm->get_formatted_name());
        return "{$s}<br/>Module with id {$moduleid} translated<br/>{$url}<br/>";
    }

    /**
     * Add record
     *
     * @param string $tablename Table name
     * @param string $fieldname Field name
     * @param int $id Id
     * @param array $extra Extra
     */
    private function add_records(string $tablename, string $fieldname, int $id, array $extra = []): string {
        global $DB;
        $s = '';
        $dbman = $DB->get_manager();
        if ($dbman->table_exists($tablename) && $dbman->field_exists($tablename, $fieldname)) {
            $items = $DB->get_records($tablename, [$fieldname => $id]);
            foreach ($items as $item) {
                $s .= $this->translate_record($tablename, $item->id, $extra);
            }
        }

        return $s;
    }

    /**
     * Translate record
     *
     * @param string $table Table
     * @param int $id Id
     * @param array $fields Fields
     */
    private function translate_record(string $table, int $id, array $fields = []): string {
        global $DB;
        if (!$this->counting && (is_null($this->targetlang) || is_null($this->sourcelang))) {
            throw new moodle_exception('Language not specified');
        }

        $s = [];
        if ($record = $DB->get_record($table, ['id' => $id])) {
            $dbman = $DB->get_manager();
            $ref = new \ReflectionObject($record);
            $updatetime = false;
            $properties = $ref->getProperties();
            $skipped = ['displayformat', 'approvaldisplayformat'];
            $handled = ['name', 'answertext', 'title'];
            foreach ($properties as $prop) {
                if (in_array($prop->name, $skipped, true)) {
                    continue;
                }
                if (in_array($prop->name, $handled, true)) {
                    $fields[] = $prop->name;
                }
                $x = stripos($prop->name, 'format');
                if ($x > 1) {
                    $fields[] = substr($prop->name, 0, $x);
                }
                if ($prop->name === 'timemodified') {
                    $updatetime = true;
                }
            }
            foreach ($fields as $field) {
                if ($dbman->field_exists($table, $field)) {
                    $task = $record->{$field};
                    if (strlen($task ?? '') > 0) {
                        $result = $task;
                        if (!$this->counting) {
                            // TODO: What if max lenght > result.
                            $result = $this->translatetext($this->sourcelang, $this->targetlang, $task);
                            if (!is_null($result) && $task != $result) {
                                $DB->set_field($table, $field, $result, ['id' => $id]);
                                if ($updatetime) {
                                    $DB->set_field($table, 'timemodified', time(), ['id' => $id]);
                                }
                            }
                        }
                        $s[] = $result;
                    }
                }
            }
        }
        return implode('<br />', $s);
    }


    /**
     * Translate a plugin
     *
     * @param string $component Component
     * @param string $fromlanguage From
     * @param string $tolanguage  To
     */
    public function translate_plugin(string $component, string $fromlanguage, string $tolanguage): string {
        global $CFG;
        require_once($CFG->dirroot . '/admin/tool/customlang/locallib.php');
        $done = [];
        $components = \tool_customlang_utils::list_components();
        if (!array_key_exists($component, $components)) {
             throw new moodle_exception('Plugin not found');
        }
        $sm = get_string_manager();
        $entries = $sm->load_component_strings($component, $fromlanguage);
        foreach ($entries as $key => $value) {
            $s = $this->translatetext($fromlanguage, $tolanguage, $value);
            if ($s != $value) {
                $done[$key] = $s;
            }
        }
        return self::dump_strings($tolanguage, $component, $done);
    }

    /**
     * Writes strings into a local language pack file
     *
     * @param string $lang the language
     * @param string $component the name of the component
     * @param array $strings strings
     */
    protected static function dump_strings(string $lang, string $component, array $strings): string {
        $admin = fullname(get_admin());
        $year = date("Y");
        $str = "<?php

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
 * Automatic translated strings ({$lang}) for {$component}
 *
 * @package   {$component}
 * @copyright {$year} {$admin}
 * @author    tool_translate
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

";

        foreach ($strings as $stringid => $text) {
            $str .= '$string[\'' . $stringid .  "'] =  " . var_export($text, true) . ';
';
        }
        return $str;
    }
}
