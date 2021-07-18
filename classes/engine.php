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
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_translate;

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for translate engines.
 *
 * @package   tool_translate
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class engine {

    /** @var \stdClass course */
    protected $course;

    /** @var \bool counting */
    public $counting = true;

    /**
     * Constructor
     *
     * @param course $course
     */
    public function __construct($course) {
        $this->course = $course;
    }

    /**
     * Is the translate engine fully configured and ready to use.
     *
     * @return bool if the engine is ready for use
     */
    abstract public function is_configured(): bool;

    /**
     * Supported languges.
     *
     * @return string[] Array of suported source/target languages
     */
    public function supported_langs(): array {
        throw new \coding_exception('supported_langs not configured for this engine');
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
     *
     * @return string
     */
    public function translate_other(): string {
        global $DB;
        $id = $this->course->id;
        $context = \context_course::instance($id);
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
        \tool_translate\event\course_translated::create(['context' => $context])->trigger();
        rebuild_course_cache($id);
        return "$s <br/>Course with id $id translated all extra elements.";
    }

    /**
     * Translate section
     *
     * @param int $sectionid
     * @return string
     */
    public function translate_section($sectionid): string {
        $s = $this->add_records('course_sections', 'id', $sectionid);
        if ($this->counting) {
            return $s;
        }
        $context = \context_course::instance($this->course->id);
        \tool_translate\event\module_translated::create(['context' => $context, 'other' => ['sectionid' => $sectionid]])->trigger();
        rebuild_course_cache($this->course->id);
        $courseformat = course_get_format($this->course)->get_format();
        return $s . get_string('sectionname', 'format_' . $courseformat) . " with id $sectionid translated";
    }

    /**
     * Translate module
     *
     * @param int $moduleid
     * @return string
     */
    public function translate_module($moduleid): string {
        global $DB;
        $modinfo = get_fast_modinfo($this->course->id, -1);
        $cm = $modinfo->cms[$moduleid];
        $s = $this->translate_record($cm->modname, $cm->instance);
        $mod = $cm->modname;
        if ($mod == 'choice') {
            $s .= $this->add_records('choice_options', 'choiceid', $cm->instance, ['text']);
        }
        if ($mod == 'checklist') {
            $s .= $this->add_records('checklist_item', 'checklist', $cm->instance, ['displaytext']);
        }
        // TODO: integrate feedback_item with array label and presentation.
        if ($mod == 'glossary') {
            $s .= $this->add_records('glossary_categories', 'glossaryid', $cm->instance);
            $s .= $this->add_records('glossary_entries', 'glossaryid', $cm->instance, ['concept']);
        }
        if ($mod == 'forum') {
            $s .= $this->add_records('forum_discussions', 'forum', $cm->instance);
        }
        if ($mod == 'book') {
            $s .= $this->add_records('book_chapters', 'bookid', $cm->instance);
        }
        if ($mod == 'lesson') {
            $s .= $this->add_records('lesson_pages', 'lessonid', $cm->instance);
            $s .= $this->add_records('lesson_answers', 'lessonid', $cm->instance);
        }
        if ($mod == 'quiz') {
            $s .= $this->add_records('quiz_sections', 'quizid', $cm->instance, ['heading']);
            $s .= $this->add_records('quiz_feedback', 'quizid', $cm->instance);
            $slots = $DB->get_records('quiz_slots', ['quizid' => $cm->instance]);
            foreach ($slots as $slot) {
                 $s .= $this->add_records('question', 'id', $slot->questionid);
                 $s .= $this->add_records('question_answers', 'question', $slot->questionid);
                 $s .= $this->add_records('question_hints', 'questionid', $slot->questionid);
                 $s .= $this->add_records('question_order', 'question', $slot->questionid);
                 $s .= $this->add_records('question_order_sub', 'question', $slot->questionid);
                 $q = \question_bank::load_question($slot->questionid);
                 $qt = get_class($q->qtype);
                 // Brute force collect feedback.
                 $s .= $this->add_records($qt, 'questionid', $slot->questionid);
                 $s .= $this->add_records($qt . '_options' , 'questionid', $slot->questionid);
                 $s .= $this->add_records($qt . '_answers' , 'questionid', $slot->questionid);
                 $s .= $this->add_records($qt . '_subquestions' , 'questionid', $slot->questionid);
            }
        }
        if ($this->counting) {
            return $s;
        }
        $context = \context_module::instance($cm->id);
        \tool_translate\event\module_translated::create(['context' => $context])->trigger();
        rebuild_course_cache($this->course->id);
        $cm = $modinfo->cms[$moduleid];
        $url = \html_writer::link($cm->url, $cm->get_formatted_name());
        return "$s<br/>Module with id $moduleid translated<br/>$url<br/>";
    }


    /**
     * Translate record
     *
     * @param string $tablename
     * @param string $fieldname
     * @param int $id
     * @param array $extra
     * @return string
     */
    private function add_records($tablename, $fieldname, $id, $extra = []) {
        global $DB;
        $s = '';
        $dbman = $DB->get_manager();
        if ($dbman->table_exists($tablename)) {
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
     * @param string $table
     * @param int $id
     * @param array $fields
     * @return string
     */
    private function translate_record($table, $id, $fields = []) {
        global $DB;
        $s = '';
        if ($record = $DB->get_record($table, ['id' => $id])) {
            $dbman = $DB->get_manager();
            $ref = new \ReflectionObject($record);
            $updatetime = false;
            $properties = $ref->getProperties();
            foreach ($properties as $prop) {
                if ($prop->name === 'displayformat' or $prop->name === 'approvaldisplayformat') {
                    continue;
                }
                if ($prop->name === 'name') {
                    $fields[] = 'name';
                }
                if ($prop->name === 'answertext') {
                    $fields[] = 'answertext';
                }
                if ($prop->name === 'title') {
                    $fields[] = 'title';
                }
                if ($prop->name === 'timemodified') {
                    $updatetime = true;
                }
                $x = stripos($prop->name, 'format');
                if ( $x > 1) {
                    $fields[] = substr($prop->name, 0, $x);
                }
            }
            foreach ($fields as $field) {
                if ($dbman->field_exists($table, $field)) {
                    $task = $record->{$field};
                    $len = strlen($task);
                    if ($len > 0) {
                        $result = $task;
                        if (!$this->counting) {
                            // TODO: What if max lenght > result.
                            $result = $this->translatetext('en', 'fr', $task);
                            if (!is_null($result) && $task != $result) {
                                $DB->set_field($table, $field, $result, ['id' => $id]);
                                if ($updatetime) {
                                    $DB->set_field($table, 'timemodified', time(), ['id' => $id]);
                                }
                            }
                        }
                        $s .= "<br />$result";
                    }
                } else {
                    return "$field with id $id not found in table $table";
                }
            }
        }
        return $s;
    }
}
