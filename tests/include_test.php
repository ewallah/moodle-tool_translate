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
 * Include tests for translate tool.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_translate;

/**
 * Include tests for translate tool.
 *
 * @package   tool_translate
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class include_test extends \advanced_testcase {
    /**
     * Test the adminmanageplugins.
     * @coversNothing
     */
    public function test_adminmanagepluginss(): void {
        global $CFG, $PAGE;
        require_once($CFG->dirroot . '/admin/tool/translate/db/access.php');
        $this->resetAfterTest();
        $this->setAdminUser();
        $PAGE->get_renderer('core');
        $_POST['plugin'] = 'aws';
        $_POST['sesskey'] = sesskey();
        include($CFG->dirroot . '/admin/tool/translate/adminmanageplugins.php');
    }

    /**
     * Test the access.
     * @coversNothing
     */
    public function test_access(): void {
        global $CFG;
        include($CFG->dirroot . '/admin/tool/translate/db/access.php');
    }

    /**
     * Test the version.
     * @coversNothing
     */
    public function test_version(): void {
        global $CFG;
        require_once($CFG->dirroot . '/admin/tool/translate/version.php');
    }
}
