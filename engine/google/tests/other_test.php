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
 * Other tests for Google translate engine.
 *
 * @package   translateengine_google
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Other tests for Google translate engine.
 *
 * @package   translateengine_google
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class translateengine_google_other_testcase extends advanced_testcase {

    /**
     * Setup testcase.
     */
    public function setUp(): void {
        $this->setAdminUser();
        $this->resetAfterTest();
    }

    /**
     * Test the empty class.
     */
    public function test_notconfigured() {
        $course = $this->getDataGenerator()->create_course();
        $class = new \translateengine_google\engine($course);
        $this->assertInstanceOf('\translateengine_google\engine', $class);
        $this->assertFalse($class->is_configured());
        set_config('googleapikey', 'key', 'translateengine_google');
        $this->assertTrue($class->is_configured());
        // TODO.
        $this->assertSame('boe', $class->translatetext('en', 'fr', 'boe'));
        $this->assertIsArray($class->supported_langs());
    }

    /**
     * Test the class.
     */
    public function test_class() {
        set_config('googleapikey', 'key', 'translateengine_google');
        $course = $this->getDataGenerator()->create_course();
        $class = new \translateengine_google\engine($course);
        $this->assertInstanceOf('\translateengine_google\engine', $class);
        $this->assertTrue($class->is_configured());
        // TODO.
        $this->assertSame('boe', $class->translatetext('en', 'fr', 'boe'));
    }
}