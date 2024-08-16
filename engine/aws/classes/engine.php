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
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace translateengine_aws;

use moodle_exception;
/**
 * AWS translate engine.
 *
 * @package   translateengine_aws
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class engine extends \tool_translate\engine {
    /** @var \stdClass awsclient */
    protected $awsclient;

    /**
     * Rough calculation of price.
     *
     * @param int $letters price per letters
     * @return string
     */
    public function get_price(int $letters): string {
        return format_float(15 / 1000000 * $letters, 5);
    }

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
        if ($r && $k && $s) {
            if (defined('BEHAT_SITE_RUNNING') || PHPUNIT_TEST) {
                $mock = new \Aws\MockHandler();
                for ($i = 1; $i < 1000; $i++) {
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
     * Supported languages.
     *
     * @return string[] Array of suported source/target languages
     */
    public function supported_langs(): array {
        return [
            'afr' => 'af', 'sqi' => 'sq', 'amh' => 'am', 'ara' => 'ar', 'hye' => 'hy', 'aze' => 'az', 'ben' => 'bn', 'bos' => 'bs',
            'bul' => 'bg', 'cat' => 'ca', 'zho' => 'zh', 'hrv' => 'hr', 'ces' => 'cs', 'dan' => 'da', 'nld' => 'nl', 'eng' => 'en',
            'est' => 'et', 'fin' => 'fi', 'fra' => 'fr', 'kat' => 'ka', 'deu' => 'de', 'ell' => 'el', 'guj' => 'gu', 'hat' => 'ht',
            'hau' => 'ha', 'heb' => 'he', 'hin' => 'hi', 'hun' => 'hu', 'isl' => 'is', 'ind' => 'id', 'ipk' => 'ik', 'ita' => 'it',
            'jpn' => 'ja', 'kan' => 'kn', 'kaz' => 'kk', 'kor' => 'ko', 'lav' => 'lv', 'lit' => 'lt', 'mkd' => 'mk', 'msa' => 'ms',
            'mlg' => 'mg', 'mlt' => 'mt', 'mon' => 'mn', 'nor' => 'no', 'pan' => 'pa', 'fas' => 'fa', 'pol' => 'pl', 'por' => 'pt',
            'pus' => 'ps', 'que' => 'qu', 'ron' => 'ro', 'rus' => 'ru', 'sin' => 'si', 'slk' => 'sk', 'slv' => 'sl', 'som' => 'so',
            'spa' => 'es', 'swa' => 'sw', 'swe' => 'sv', 'tam' => 'ta', 'tel' => 'te', 'tha' => 'th', 'tur' => 'tr', 'ukr' => 'uk',
            'urd' => 'ur', 'uzb' => 'uz', 'vie' => 'vi', 'yid' => 'yi', 'mal' => 'ml', 'mar' => 'mr', 'srp' => 'sr', 'tgl' => 'tl',
            'gle' => 'ga',
        ];
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
        if ($this->is_configured() && $this->lang_supported($source) && $this->lang_supported($target)) {
            try {
                $arr = $this->awsclient->translateText(
                    [
                        'SourceLanguageCode' => $source,
                        'TargetLanguageCode' => $target,
                        'Text' => $txt,
                    ]
                );
                return html_entity_decode($arr['TranslatedText']);
            } catch (exception $e) {
                throw new moodle_exception($e->get_message());
            }
        }
        return null;
    }
}
