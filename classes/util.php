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
 * This file contains the util class of the translate tool.
 *
 * @package   tool_translate
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_translate;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/admin/tool/customlang/locallib.php');


/**
 * This file contains the util class of the translate tool.
 *
 * @package   tool_translate
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class util extends \tool_customlang_utils {

    /**
     * Writes strings into a local language pack file
     *
     * @param string $component the name of the component
     * @param string $lang
     * @param array $strings
     * @return string filename
     */
    public static function dump_strings($component, $lang, $strings) {
        if (count($strings) > 0) {
            parent::dump_strings($lang, $component, $strings);
            $tmp = parent::get_localpack_location($lang) . '/' . $component . '.php';
            $fd = fopen($tmp, 'r');
            if ($fd != false) {
                $value = fread($fd, filesize($tmp));
                fclose($fd);
                return $value;
            }
        }
        return '';
    }
}
