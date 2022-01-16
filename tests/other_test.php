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
 * Other tests for translate tool.
 *
 * @package   tool_translate
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_translate;

use advanced_testcase;
use context_course;
use context_module;
use lang_string;
use moodle_url;
use stdClass;

/**
 * Other tests for translate tool.
 *
 * @package   tool_translate
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class other_test extends advanced_testcase {

    /**
     * Test the submodule translateengine.
     */
    public function test_translate_engine() {
        global $CFG;
        require_once($CFG->dirroot . '/lib/adminlib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $translateengine = new plugininfo\translateengine();
        $this->assertTrue($translateengine->is_uninstall_allowed());
        $this->assertTrue($translateengine->is_enabled());
        $this->assertEquals('translateengine_', $translateengine->get_settings_section_name());
        $category = new \admin_category('translateengines', new lang_string('settings', 'tool_translate'));
        $translateengine->load_settings($category, 'aws', true);
    }


    /**
     * Test the plugin manager.
     */
    public function test_plugin_manager() {
        global $CFG;
        require_once($CFG->dirroot . '/lib/adminlib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $pluginmanager = new plugin_manager();
        $pluginmanager->get_sorted_plugins_list();
        ob_start();
        \phpunit_util::call_internal_method($pluginmanager, 'view_plugins_table', [], 'tool_translate\plugin_manager');
        $out = ob_get_contents();
        ob_end_clean();
        $this->assertStringContainsString('aws', $out);
        ob_start();
        $pluginmanager->execute('hide', 'aws');
        $pluginmanager->execute(null, null);
        $out = ob_get_contents();
        ob_end_clean();
        $this->assertStringContainsString('aws', $out);
        ob_start();
        $pluginmanager->execute(null, 'aws');
        $pluginmanager->execute('hide', 'aws');
        $pluginmanager->execute('show', 'aws');
        $pluginmanager->execute('movedown', 'aws');
        $pluginmanager->execute('movedown', 'aws');
        $pluginmanager->execute('movedown', 'aws');
        $pluginmanager->execute('moveup', 'aws');
        $pluginmanager->execute('moveup', 'aws');
        $pluginmanager->execute('moveup', 'aws');
        $pluginmanager->show_plugin('aws');
        ob_end_clean();
    }

    /**
     * Test an engine.
     */
    public function test_engine() {
        global $CFG, $DB, $USER;
        require_once($CFG->dirroot . '/lib/adminlib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $fordb = new stdClass();
        $fordb->id = null;
        $fordb->name = "Test badge with 'apostrophe' and other friends &(";
        $fordb->description = "Testing badges";
        $fordb->timecreated = time();
        $fordb->timemodified = time();
        $fordb->usercreated = $USER->id;
        $fordb->usermodified = $USER->id;
        $fordb->issuername = "Test issuer";
        $fordb->issuerurl = "http://issuer-url.domain.co.nz";
        $fordb->issuercontact = "issuer@example.com";
        $fordb->expiredate = null;
        $fordb->expireperiod = null;
        $fordb->type = BADGE_TYPE_COURSE;
        $fordb->version = 1;
        $fordb->language = 'en';
        $fordb->courseid = $course->id;
        $fordb->messagesubject = "Test message subject";
        $fordb->message = "Test message body";
        $fordb->attachment = 1;
        $fordb->notification = 0;
        $fordb->imageauthorname = "Image Author 1";
        $fordb->imageauthoremail = "author@example.com";
        $fordb->imageauthorurl = "http://author-url.example.com";
        $fordb->imagecaption = "Test caption image";
        $fordb->status = BADGE_STATUS_INACTIVE;
        $DB->insert_record('badge', $fordb, true);

        $lesson = $generator->create_module('lesson', ['course' => $course->id]);
        $lessongenerator = $generator->get_plugin_generator('mod_lesson');
        $lessongenerator->create_content($lesson);
        $lessongenerator->create_question_truefalse($lesson);
        $page = $generator->create_module('page', ['course' => $course->id]);
        $book = $generator->create_module('book', ['course' => $course->id]);
        $feedback = $generator->create_module('feedback', ['course' => $course->id]);
        $fg = $generator->get_plugin_generator('mod_feedback');
        $fg->create_item_numeric($feedback);
        $fg->create_item_multichoice($feedback);
        $choice = $generator->create_module('choice', ['course' => $course->id]);
        $forum = $generator->create_module('forum', ['course' => $course->id]);
        $glossary = $generator->create_module('glossary', ['course' => $course->id]);
        $glossarygenerator = $generator->get_plugin_generator('mod_glossary');
        $glossarygenerator->create_content($glossary);
        $glossarygenerator->create_content($glossary, ['concept' => 'Custom concept']);
        $quizgen = $generator->get_plugin_generator('mod_quiz');
        $quiz = $quizgen->create_instance(['course' => $course->id]);
        $questiongenerator = $generator->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $q = $questiongenerator->create_question('essay', 'plain', ['category' => $cat->id]);
        quiz_add_quiz_question($q->id, $quiz, 0 , 10);

        $engine = new \translateengine_aws\engine($course);
        $this->assertFalse($engine->is_configured());
        $this->assertIsArray($engine->supported_langs());
        $this->assertStringContainsString('Not configured', $engine->translatetext('en', 'nl', 'boe'));
        $this->assertStringNotContainsString('Course  with id', $engine->translate_other());
        $this->assertEquals('', $engine->translate_section(1));
        $this->assertStringNotContainsString('Module with id', $engine->translate_module($book->cmid));
        $engine->counting = false;
        $this->assertStringContainsString('Course with id', $engine->translate_other());
        $this->assertEquals('Topic with id 1 translated', $engine->translate_section(1));
        $this->assertStringContainsString('Module with id', $engine->translate_module($book->cmid));
        $this->assertStringContainsString('Module with id', $engine->translate_module($lesson->cmid));
        $this->assertStringContainsString('Module with id', $engine->translate_module($page->cmid));
        $this->assertStringContainsString('Module with id', $engine->translate_module($feedback->cmid));
        $this->assertStringContainsString('Module with id', $engine->translate_module($choice->cmid));
        $this->assertStringContainsString('Module with id', $engine->translate_module($forum->cmid));
        $this->assertStringContainsString('Module with id', $engine->translate_module($glossary->cmid));
        $this->assertStringContainsString('Module with id', $engine->translate_module($quiz->cmid));
    }

    /**
     * Test an abstract engine.
     */
    public function test_abstract_engine() {
        global $CFG;
        require_once($CFG->dirroot . '/lib/adminlib.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $engine = new \translateengine_aws\engine($course);
        $reflection = new \ReflectionClass('\tool_translate\engine');
        $this->assertTrue($reflection->isAbstract());
        $method = $reflection->getMethod('supported_langs');
        $this->expectExceptionMessage('supported_langs not configured for this engine');
        $method->invoke($engine);
    }

    /**
     * Test the plugin translation.
     */
    public function test_plugin_tranalate() {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $engine = new \translateengine_aws\engine($course);
        $out = $engine->translate_plugin('tool_translate', 'en', 'fr');
        $this->assertStringContainsString('tool_translate', $out);
        $out = \phpunit_util::call_internal_method(
            $engine,
            'dump_strings',
            ['fr', 'tool_translate', ['a' => 'boe']],
            'tool_translate\engine');
        $this->assertStringContainsString('Automatic translated strings (fr) for tool_translate', $out);
        $this->expectExceptionMessage('Plugin not found');
        $out = $engine->translate_plugin('tool_translatefake', 'en', 'fr');
    }
}
