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
 * deepl translate engine.
 *
 * @package   translateengine_deepl
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace translateengine_deepl;

use moodle_exception;

/**
 * deepl translate engine.
 *
 * @package   translateengine_deepl
 * @copyright iplusacademy (www.iplusacademy.org)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class engine extends \tool_translate\engine {
    /**
     * Rough calculation of price.
     *
     * @param int $letters price per letters
     * @return string
     */
    public function get_price(int $letters): string {
        // TODO:  Get price.
        return format_float(0 * $letters, 5);
    }

    /**
     * Is the translate engine fully configured and ready to use.
     *
     * @return bool if the engine is ready for use
     */
    public function is_configured(): bool {
        return get_config('translateengine_deepl', 'api_key') != '';
    }

    /**
     * Supported languages.
     *
     * @return string[] Array of suported source/target languages
     */
    public function supported_langs(): array {
        return [
            'bul' => 'bg', 'cat' => 'ca', 'dan' => 'da', 'deu' => 'de', 'ell' => 'el', 'eng' => 'en', 'spa' => 'es', 'est' => 'et',
            'fin' => 'fi', 'fra' => 'fr', 'hun' => 'hu', 'ita' => 'it', 'lav' => 'lv', 'nld' => 'nl', 'pol' => 'pl', 'ron' => 'ro',
            'rus' => 'ru', 'slk' => 'sk', 'slv' => 'sl', 'swe' => 'sv', 'zho' => 'zh', ];
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
                // Build new curl request.
                $curl = new \curl();
                $params = [
                    'text' => urlencode($txt),
                    'source_lang' => $source,
                    'target_lang' => $target,
                    'preserve_formatting' => 1,
                    'auth_key' => get_config('translateengine_deepl', 'api_key'),
                    'tag_handling' => 'xml',
                    'split_sentences' => 'nonewlines',
                ];
                if (defined('BEHAT_SITE_RUNNING') || PHPUNIT_TEST) {
                    $resp = json_encode(['translations' => [['text' => 'Behat', 'detected_source_language' => $target]]]);
                } else {
                    $resp = $curl->post('https://api.deepl.com/v2/translate?', $params);
                }
                $resp = json_decode($resp);
                // Get the translation and return translation.
                if (!empty($resp->translations[0]->text) && $resp->translations[0]->detected_source_language !== $source) {
                    return html_entity_decode($resp->translations[0]->text);
                } else {
                    return $txt;
                }
            } catch (exception $e) {
                throw new moodle_exception($e->get_message());
            }
        }
        return null;
    }
}
