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
 * Other tests for AWS translate engine.
 *
 * @package   translateengine_aws
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Other tests for AWS translate engine.
 *
 * @package   translateengine_aws
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class translateengine_aws_other_testcase extends advanced_testcase {

    /**
     * Setup testcase.
     */
    public function setUp(): void {
        $this->setAdminUser();
        $this->resetAfterTest();
        set_config('region', 'eu-west-3', 'translateengine_aws');
        set_config('access_key', 'key', 'translateengine_aws');
        set_config('secret_key', 'secret', 'translateengine_aws');
    }

    /**
     * Test the empty class.
     */
    public function test_notconfigured() {
        $course = $this->getDataGenerator()->create_course();
        set_config('access_key', '', 'translateengine_aws');
        $class = new \translateengine_aws\engine($course);
        $this->assertInstanceOf('\translateengine_aws\engine', $class);
        $this->assertFalse($class->is_configured());
        $this->assertSame(null, $class->translatetext('en', 'fr', 'boe'));
        $this->assertIsArray($class->supported_langs());
        // TODO: get price from https://pricing.us-east-1.amazonaws.com/offers/v1.0//translate/current/index.json.
    }

    /**
     * Test the class.
     */
    public function test_class() {
        $course = $this->getDataGenerator()->create_course();
        $class = new \translateengine_aws\engine($course);
        $this->assertInstanceOf('\translateengine_aws\engine', $class);
        $this->assertTrue($class->is_configured());
        $this->assertSame('BEHAT 1', $class->translatetext('en', 'fr', 'boe'));
    }
}