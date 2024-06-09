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
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_translate\output;

use advanced_testcase;
use html_writer;

/**
 * Table tests for translate tool.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \tool_translate\translation_table
 */
final class table_test extends advanced_testcase {
    /**
     * Setup testcase.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
    }

    /**
     * Test the library.
     * @covers \tool_translate\output\translation_table
     */
    public function test_table(): void {
        $gen = $this->getDataGenerator();
        $course = $gen->create_course(['lang' => 'fr']);
        $gen->create_module(
            'page',
            ['course' => $course->id,
            'name' => 'Lesson',
            'content' => ' before [after] already',
            ]
        );
        $gen->create_module('book', ['course' => $course->id]);
        $lesson = $gen->create_module('lesson', ['course' => $course->id]);
        $lessongenerator = $gen->get_plugin_generator('mod_lesson');
        $lessongenerator->create_content($lesson);
        $lessongenerator->create_question_truefalse($lesson);
        $feedback = $gen->create_module('feedback', ['course' => $course->id]);
        $fg = $gen->get_plugin_generator('mod_feedback');
        $fg->create_item_numeric($feedback);
        $fg->create_item_multichoice($feedback);
        $glossary = $gen->create_module('glossary', ['course' => $course->id]);
        $glossarygenerator = $gen->get_plugin_generator('mod_glossary');
        $glossarygenerator->create_content($glossary);
        $glossarygenerator->create_content($glossary, ['concept' => 'Custom concept']);
        $gen->create_module('choice', ['course' => $course->id]);
        $gen->create_module('forum', ['course' => $course->id]);
        $gen->create_module('resource', ['course' => $course->id]);

        ob_start();
        $table = new translation_table($course);
        $table->filldata();
        $out = html_writer::table($table);
        ob_end_clean();
        $this->assertStringContainsString($lesson->name, $out);
        set_config('region', 'eu-west-3', 'translateengine_aws');
        set_config('access_key', 'key', 'translateengine_aws');
        set_config('secret_key', 'secret', 'translateengine_aws');
        ob_start();
        $table = new translation_table($course);
        $table->filldata();
        $out = html_writer::table($table);
        ob_end_clean();
        $this->assertStringContainsString($lesson->name, $out);
        $table->translate_all('fr', 'en');
        $table->translate_module($glossary->cmid);
        $table->translate_other();
        $table->translate_section(1);
    }
}
