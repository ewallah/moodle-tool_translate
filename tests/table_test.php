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
 * Table tests for translate tool.
 *
 * @package   tool_translate
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Table tests for translate tool.
 *
 * @package   tool_translate
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \tool_translate\translation_table
 */
class tool_translate_table_testcase extends advanced_testcase {

    /**
     * Setup testcase.
     */
    public function setUp(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Test the library.
     */
    public function test_table() {
        $generator = $this->getDataGenerator();
        $course = $generator->create_course(['lang' => 'fr']);
        $generator->create_module('page',
           ['course' => $course->id,
            'name' => 'Lesson',
            'content' => ' before [after] already']);
        $generator->create_module('book', ['course' => $course->id]);
        $lesson = $generator->create_module('lesson', ['course' => $course->id]);
        $lessongenerator = $generator->get_plugin_generator('mod_lesson');
        $lessongenerator->create_content($lesson);
        $lessongenerator->create_question_truefalse($lesson);
        $feedback = $generator->create_module('feedback', ['course' => $course->id]);
        $fg = $generator->get_plugin_generator('mod_feedback');
        $fg->create_item_numeric($feedback);
        $fg->create_item_multichoice($feedback);
        $glossary = $generator->create_module('glossary', ['course' => $course->id]);
        $glossarygenerator = $generator->get_plugin_generator('mod_glossary');
        $glossarygenerator->create_content($glossary);
        $glossarygenerator->create_content($glossary, ['concept' => 'Custom concept']);
        $generator->create_module('choice', ['course' => $course->id]);
        $generator->create_module('forum', ['course' => $course->id]);

        $table = new \tool_translate\translation_table($course);
        $table->filldata();
        $out = \html_writer::table($table);
        $this->assertStringContainsString($lesson->name, $out);
    }
}
