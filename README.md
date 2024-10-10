## Moodle translate ##

Translate a complete course using Neural Machine Engines at a low cost. All parts are handled: modules - course fields - enrolment methods - sections - questions....

## Idea ##

The tool_translate plugin uses the Experimental approach of Search and replace tool. It detects the implemented tables and fields related to a course or module and replaces the (html) text in the database with a translation received from a translation engine.

## Honorable Mention ##

The tool_translate plugin received an Honorable Mention in the [Get Plugged into Education!](https://unite.un.org/news/winners-%E2%80%9Cget-plugged-education%E2%80%9D-hackathon) hackathon,
the first innovation event from the Open Source Software for Sustainable Development Goals (OSS4SDG) initiative organized by the
Office of Information and Communications Technology (OICT) and the European Commission Directorate-General for Informatics (EC DG--DIGIT).

## Currently supported translate engines: ##

  - Google translate
  - AWS translate
  - DeepL

But language pairs can have better translation performance with other engines, so we made it easy to implement your own.

## Warnings ##

 - All translated text has to be reviewed by a native speaker.
 - This plugin translates text, make a backup before you start.
 - This plugin is still in Beta version - do not use it in a production environment.
 - The translation engines are mostly a Paying service.

## Command line tools ##

From the command line, it is possible to translate a complete

 - course: php admin/tool/translate/cli/translate_course.php --engine=aws --courseid=22 --from=en --to=fr
 - plugin: php admin/tool/translate/cli/translate_component.php --engine=aws --component=tool_translate --from=en --to=nl

## Admin tools ##

Check the global documentation about [admin tools](https://docs.moodle.org/400/en/Admin_tools)

## Installation: ##

 1. Unpack the zip file into the admin/tool/ directory. A new directory will be created called translate.
 2. Go to Site administration > Notifications to complete the plugin installation.
 3. Configure at least 1 translation engine

## TODO ##

 - matching question not translated

## Requirements ##

This plugin requires Moodle 4.4+

## Troubleshooting ##

 1. Goto "Administration" > "Language" > "Translation settings", and ensure that at least 1 translation engine is configured.
 2. Goto "Administration" > "Language" > "Translation settings", "Translation engines" and ensure that at least 1 translation engine is visible.
 3. Goto "Administration" > "Language" > "Translation settings", "Translation engines" and order your prefered engines.
 4. Start translating

## Theme support ##

This plugin is developed and tested on Moodle Core's Boost theme and Boost child themes, including Moodle Core's Classic theme.

## Plugin repositories ##

This plugin will be published and regularly updated on [Github](https://github.com/ewallah/moodle-tool_translate)

## Bug and problem reports / Support requests##

This plugin is carefully developed and thoroughly tested, but bugs and problems can always appear.
Please report bugs and problems on [Github](https://github.com/ewallah/moodle-tool_translate/issues)
We will do our best to solve your problems, but please note that due to limited resources we can't always provide per-case support.

## Feature proposals ##
Please issue feature proposals on [Github](https://github.com/ewallah/moodle-tool_translate/issues)
Please create pull requests on [Github](https://github.com/ewallah/moodle-tool_translate/pulls)
We are always interested to read about your feature proposals or even get a pull request from you, but please accept that we can handle your issues only as feature proposals and not as feature requests.

## Todo ##

 - implement Active Custom Translation (Extra parallel data to customize the machine translated output)
 - implement Custom Terminology so specific terms get translated better
 - Moodle 4.00 question handling
 - support translation of Microsoft Word / PDF documents

## Status ##

[![Build Status](https://github.com/ewallah/moodle-tool_translate/workflows/Tests/badge.svg)](https://github.com/ewallah/moodle-tool_translate/actions)

## Copyright ##

iplusacademy.org
