<?php
// This file is part of Moodle - https://moodle.org/
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
 * Moodle CLI script - translate_component.php
 *
 * @package   tool_translate
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

$usage = "Put a one line summary of what the script does here.

Usage:
    # php translate_component.php --engine=aws --component=tool_translate --from=en --to=fr
    # php translate_component.php [--help|-h]

Options:
    -h --help               Print this help.
    --component=<value>     The plugin that has to be translated.
";

list($options, $unrecognised) = cli_get_params([
    'help' => false,
    'engine' => 'aws',
    'component' => null,
    'from' => 'en',
    'to' => null
], [
    'h' => 'help',
    'e' => 'engine',
    'c' => 'component',
    'f' => 'from',
    't' => 'to'
]);

if ($unrecognised) {
    $unrecognised = implode(PHP_EOL . '  ', $unrecognised);
    cli_error(get_string('cliunknowoption', 'core_admin', $unrecognised));
}

if ($options['help']) {
    cli_writeln($usage);
    exit(2);
}


if (empty($options['component'])) {
    cli_error('Missing mandatory argument component.', 2);
}

if (empty($options['to'])) {
    cli_error('Missing mandatory argument to.', 2);
}
$course = get_course(1);
$engine = 'translateengine_' . $options['engine'] . '\engine';
require_once($CFG->dirroot . '/admin/tool/translate/engine/' . $options['engine'] . '/classes/engine.php');
require_once($CFG->dirroot . '/admin/tool/translate/classes/engine.php');
require_once($CFG->dirroot . '/admin/tool/translate/classes/util.php');
$translateengine = new $engine($course);
echo $translateengine->translate_plugin($options['component'], $options['from'], $options['to']);
