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
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace translateengine_google;

/**
 * Other tests for Google translate engine.
 *
 * @package   translateengine_google
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class other_test extends \advanced_testcase {
    /** @var \stdClass course */
    private $course;

    /**
     * Setup testcase.
     */
    public function setUp(): void {
        parent::setUp();
        $this->setAdminUser();
        $this->resetAfterTest();
        $this->course = $this->getDataGenerator()->create_course();
    }

    /**
     * Test the empty class.
     * @covers \translateengine_google\engine
     */
    public function test_notconfigured(): void {
        $class = new \translateengine_google\engine($this->course);
        $this->assertInstanceOf('\translateengine_google\engine', $class);
        $this->assertFalse($class->is_configured());
        set_config('googleapikey', 'key', 'translateengine_google');
        $this->assertTrue($class->is_configured());
        $this->assertNotEmpty($class->get_price(10));
    }

    /**
     * Test the class.
     * @covers \translateengine_google\engine
     */
    public function test_class(): void {
        set_config('googleapikey', 'key', 'translateengine_google');
        $class = new \translateengine_google\engine($this->course);
        $this->assertInstanceOf('\translateengine_google\engine', $class);
        $this->assertTrue($class->is_configured());
        $this->assertIsArray($class->supported_langs());
        $this->assertSame('Google translate', $class->get_name());
        $langs = $class->supported_langs();
        $this->assertEquals($langs, array_unique($langs));
        $this->assertNotEmpty($class->get_price(10));
        $languages1 = get_string_manager()->get_list_of_languages('en', 'iso6391');
        $languages2 = get_string_manager()->get_list_of_languages('en', 'iso6392');
        foreach ($langs as $key => $value) {
            $this->assertTrue(array_key_exists($value, $languages1));
            $this->assertTrue(array_key_exists($key, $languages2));
        }
        $this->assertSame('Behat', $class->translatetext('en', 'fr', 'boe'));
        $this->assertSame('Behat', $class->translatetext('en', 'en', 'boe'));
    }

    /**
     * Test the errors.
     * @covers \translateengine_google\engine
     */
    public function test_error1(): void {
        set_config('googleapikey', 'key', 'translateengine_google');
        $class = new engine($this->course);
        $this->expectExceptionMessage('language not supported');
        $this->assertSame(null, $class->translatetext('en', 'xx', 'boe'));
    }

    /**
     * Test the errors.
     * @covers \translateengine_google\engine
     */
    public function test_error2(): void {
        set_config('googleapikey', 'key', 'translateengine_google');
        $class = new engine($this->course);
        $this->expectExceptionMessage('language not supported');
        $this->assertSame(null, $class->translatetext('xx', 'en', 'boe'));
    }
}
