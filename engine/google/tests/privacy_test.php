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
 * Privacy tests for Google translate engine.
 *
 * @package   translateengine_google
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_privacy\tests\provider_testcase;

/**
 * Privacy tests for google translate engine.
 *
 * @package   translateengine_google
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class translateengine_google_privacy_testcase extends provider_testcase {

    /**
     * Test returning metadata.
     */
    public function test_get_metadata() {
        $this->resetAfterTest(true);
        $collection = new \core_privacy\local\metadata\collection('translateengine_google');
        $reason = \translateengine_google\privacy\provider::get_reason($collection);
        $this->assertEquals($reason, 'privacy:metadata');
        $str = get_string($reason, 'translateengine_google');
        $this->assertStringContainsString('plugin does not store any personal data', $str);
    }
}
