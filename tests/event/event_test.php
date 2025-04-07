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
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_translate\event;

use advanced_testcase;
use context_course;
use context_module;
use moodle_url;

/**
 * Other tests for translate tool.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class event_test extends advanced_testcase {
    /**
     * Test the tool viewed event.
     * @covers \tool_translate\event\tool_viewed
     */
    public function test_tool_viewed(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $event = tool_viewed::create(['context' => $context]);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\tool_translate\event\tool_viewed', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals('Translate page viewed', $event->get_name());
        $this->assertStringContainsString('viewed the tranlation page for the course', $event->get_description());
        $url = new moodle_url('/admin/tool/translate/index.php', ['course' => $course->id]);
        $this->assertEquals($url, $event->get_url());
    }

    /**
     * Test the course translated event.
     * @covers \tool_translate\event\course_translated
     */
    public function test_course_translated(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $event = course_translated::create(['context' => $context]);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\tool_translate\event\course_translated', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals('Course elements translated', $event->get_name());
        $url = new moodle_url('/admin/tool/translate/index.php', ['course' => $course->id]);
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }

    /**
     * Test the section translated event.
     * @covers \tool_translate\event\section_translated
     */
    public function test_section_translated(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $context = context_course::instance($course->id);
        $event = section_translated::create(['context' => $context, 'other' => ['sectionid' => 1]]);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\tool_translate\event\section_translated', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals('Section translated', $event->get_name());
        $url = new moodle_url('/admin/tool/translate/index.php', ['course' => $course->id]);
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }


    /**
     * Test the module translated event.
     * @covers \tool_translate\event\module_translated
     */
    public function test_module_translated(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $lesson = $this->getDataGenerator()->create_module('lesson', ['course' => $course->id]);
        $context = context_module::instance($lesson->cmid);
        $event = module_translated::create(['context' => $context]);

        // Trigger and capture the event.
        $sink = $this->redirectEvents();
        $event->trigger();
        $events = $sink->get_events();
        $event = reset($events);

        $this->assertInstanceOf('\tool_translate\event\module_translated', $event);
        $this->assertEquals($context, $event->get_context());
        $this->assertEquals('Module translated', $event->get_name());
        $url = new moodle_url('/course/modedit.php', ['update' => $context->instanceid]);
        $this->assertEquals($url, $event->get_url());
        $this->assertEventContextNotUsed($event);
    }
}
