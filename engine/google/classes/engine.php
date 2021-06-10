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

defined('MOODLE_INTERNAL') || die();

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

    /**
     * Is the translate engine fully configured and ready to use.
     *
     * @return bool if the engine is ready for use
     */
    public function is_configured(): bool {
        return (get_config('translateengine_google', 'googleapikey') != '');
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
                // TODO: Configure Google.
                $url = 'https://www.googleapis.com/language/translate/v2?key=';
                $url .= get_config('translateengine_google', 'googleapikey');
                $url .= '&format=html&prettyprint=false&q=';
                $url .= $txt;
                $url .= '&source=' . $source;
                $url .= '&target=' . $target;
                return $txt;
            } catch (exception $e) {
                return null;
            }
        }
        return null;
    }

}