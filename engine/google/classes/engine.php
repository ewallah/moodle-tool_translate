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
 * Google translate engine.
 *
 * @package   translateengine_google
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace translateengine_google;

use moodle_exception;

/**
 * google translating engine.
 *
 * @package   translateengine_google
 * @copyright 2021 eWallah
 * @author    Renaat Debleu <info@eWallah.net>
 * @author    info@iplusacademy.org
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class engine extends \tool_translate\engine {

    /** @var \stdClass client */
    protected $client;
    /** @var \stdClass service */
    protected $service;

    /**
     * Rough calculation of price.
     *
     * @param int $letters price per letters
     * @return string
     */
    public function get_price(int $letters): string {
         return format_float(20 / 1000000 * $letters, 5);
    }

    /**
     * Constructor
     *
     * @param course $course
     */
    public function __construct($course) {
        global $CFG;
        parent::__construct($course);
        require_once($CFG->libdir . '/google/lib.php');
        $this->client = get_google_client();
        $key = get_config('translateengine_google', 'googleapikey');
        if ($key != '') {
            $this->client->setDeveloperKey($key);
            $this->service = new \Google_Service_Translate($this->client);
        }
    }

    /**
     * Is the translate engine fully configured and ready to use.
     *
     * @return bool if the engine is ready for use
     */
    public function is_configured(): bool {
        return (get_config('translateengine_google', 'googleapikey') != '');
    }

    /**
     * Supported languages.
     *
     * @return string[] Array of suported source/target languages
     */
    public function supported_langs(): array {
        return [
            'afr' => 'af', 'sqi' => 'sq', 'amh' => 'am', 'ara' => 'ar', 'hye' => 'hy', 'aze' => 'az', 'eus' => 'eu', 'bel' => 'be',
            'ben' => 'bn', 'bos' => 'bs', 'bul' => 'bg', 'cat' => 'ca', 'cos' => 'co', 'hrv' => 'hr', 'ces' => 'cs', 'dan' => 'da',
            'nld' => 'nl', 'eng' => 'en', 'epo' => 'eo', 'est' => 'et', 'fin' => 'fi', 'fra' => 'fr', 'glg' => 'gl', 'kat' => 'ka',
            'deu' => 'de', 'ell' => 'el', 'guj' => 'gu', 'hat' => 'ht', 'hau' => 'ha', 'heb' => 'he', 'hin' => 'hi', 'hun' => 'hu',
            'isl' => 'is', 'ibo' => 'ig', 'ind' => 'id', 'ipk' => 'ik', 'ita' => 'it', 'jpn' => 'ja', 'jav' => 'jv', 'kan' => 'kn',
            'kaz' => 'kk', 'kin' => 'rw', 'kor' => 'ko', 'kur' => 'ku', 'kir' => 'ki', 'lao' => 'lo', 'lav' => 'lv', 'lit' => 'lt',
            'ltz' => 'lb', 'mkd' => 'mk', 'msa' => 'ms', 'mlg' => 'mg', 'mri' => 'mi', 'mar' => 'mr', 'mya' => 'my', 'mon' => 'mn',
            'nep' => 'ne', 'nor' => 'no', 'nya' => 'ny', 'ori' => 'or', 'pus' => 'ps', 'fas' => 'fa', 'pol' => 'pl', 'por' => 'pt',
            'pan' => 'pa', 'ron' => 'ro', 'rus' => 'ru', 'sin' => 'si', 'slk' => 'sk', 'slv' => 'sl', 'som' => 'so', 'spa' => 'es',
            'swa' => 'sw', 'swe' => 'sv', 'tam' => 'ta', 'tel' => 'te', 'tha' => 'th', 'tur' => 'tr', 'ukr' => 'uk', 'urd' => 'ur',
            'uzb' => 'uz', 'vie' => 'vi', 'yid' => 'yi', 'mal' => 'ml', 'mar' => 'mr', 'mon' => 'mn', 'srp' => 'sr', 'tgl' => 'tl',
            'gle' => 'ga', 'zul' => 'zu'];
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
        $return = null;
        if ($this->service && $this->lang_supported($source) && $this->lang_supported($target)) {
            try {
                // TODO: Configure Google.
                $url = 'https://www.googleapis.com/language/translate/v2?key=';
                $url .= get_config('translateengine_google', 'googleapikey');
                $url .= '&format=html&prettyprint=false&q=';
                $url .= urlencode($txt);
                $url .= '&source=' . $source;
                $url .= '&target=' . $target;
                $handle = curl_init($url);
                curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                if (defined('BEHAT_SITE_RUNNING') or PHPUNIT_TEST) {
                    $responsecode = 200;
                    $responsed = 'Behat';
                } else {
                    $response = curl_exec($handle);
                    $responsecode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                    $responsed = json_decode($response, true);
                }
                curl_close($handle);
                $return = ($responsecode == 200) ? $responsed : null;
            } catch (exception $e) {
                return null;
            }
        }
        return $return;
    }
}
