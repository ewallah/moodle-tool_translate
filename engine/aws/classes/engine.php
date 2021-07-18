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
 * AWS translate engine.
 *
 * @package   translateengine_aws
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace translateengine_aws;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/aws/sdk/aws-autoloader.php');

/**
 * AWS translate engine.
 *
 * @package   translateengine_aws
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class engine extends \tool_translate\engine {

    /** @var \stdClass awsclient */
    protected $awsclient;

    /**
     * Constructor
     *
     * @param course $course
     */
    public function __construct($course) {
        parent::__construct($course);
        $r = get_config('translateengine_aws', 'region');
        $k = get_config('translateengine_aws', 'access_key');
        $s = get_config('translateengine_aws', 'secret_key');
        $arr = ['region' => $r, 'credentials' => ['key' => $k, 'secret' => $s], 'version' => '2017-07-01'];
        if ($r and $k and $s) {
            if (defined('BEHAT_SITE_RUNNING') or PHPUNIT_TEST or CLI_SCRIPT) {
                $mock = new \Aws\MockHandler();
                for ($i = 1; $i < 10000; $i++) {
                    $mock->append(new \Aws\Result(['TranslatedText' => "BEHAT $i"]));
                }
                $arr['handler'] = $mock;
            }
            $this->awsclient = \Aws\Translate\TranslateClient::factory($arr);
        }
    }

    /**
     * Is the translate engine fully configured and ready to use.
     *
     * @return bool if the engine is ready for use
     */
    public function is_configured(): bool {
        return !is_null($this->awsclient);
    }

    /**
     * Supported languges.
     *
     * @return string[] Array of suported source/target languages
     */
    public function supported_langs(): array {
        return ['en', 'fr'];
    }

    /**
     * Translate text.
     *
     * @param string $source The source language
     * @param string $target The target language
     * @param string $txt The text that has to be translated
     * @return string|null The translated text
     */
    public function translatetext(string $source, string $target, string $txt): ?string {
        if ($this->is_configured()) {
            try {
                $arr = $this->awsclient->translateText([
                     'SourceLanguageCode' => $source,
                     'TargetLanguageCode' => $target,
                     'Text' => $txt]);
                return html_entity_decode($arr['TranslatedText']);
            } catch (exception $e) {
                return null;
            }
        }
        return null;
    }
}
