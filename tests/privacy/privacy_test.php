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
 * Privacy tests for translate tool.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_translate\privacy;

use core_privacy\tests\provider_testcase;
use core_privacy\local\metadata\collection;
use tool_translate\privacy\provider;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Privacy tests for translate tool.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(provider::class)]
final class privacy_test extends provider_testcase {
    /**
     * Test returning metadata.
     */
    public function test_get_metadata(): void {
        $this->resetAfterTest(true);
        $collection = new collection('tool_translate');
        $reason = provider::get_reason($collection);
        $this->assertEquals($reason, 'privacy:metadata');
        $str = get_string($reason, 'tool_translate');
        $this->assertStringContainsString('plugin does not store any personal data', $str);
    }
}
