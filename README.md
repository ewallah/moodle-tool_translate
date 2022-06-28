## Moodle translate ##

Translate a complete course using Neural Machine Engines. All parts are handled: modules - course fields - enrolment methods - sections - questions....
Currently 3 engines are supported: DeepL, Google and AWS translate. But language pairs can have better translation performance with other engines, so we made it easy to implement your own.

## Admin tools ##

Check the global documentation about admin tools: https://docs.moodle.org/400/en/Admin_tools

## Installation: ##

 1. Unpack the zip file into the admin/tool/ directory. A new directory will be created called translate.
 2. Go to Site administration > Notifications to complete the plugin installation.
 3. Configure at least 1 translation engine

## Requirements ##

This plugin requires Moodle 3.11+

## Troubleshooting ##

 1. Goto "Administration" > "Language" > "Translation settings", and ensure that at least 1 translation engine is configured.
 2. Goto "Administration" > "Language" > "Translation settings", "Translation engines" and ensure that at least 1 translation engine is visible.
 3. Goto "Administration" > "Language" > "Translation settings", "Translation engines" and order your prefered engines.
 4. Start translating

## Theme support ##

This plugin is developed and tested on Moodle Core's Boost theme and Boost child themes, including Moodle Core's Classic theme.

## Plugin repositories ##

This plugin will be published and regularly updated on Github: https://github.com/ewallah/moodle-tool_translate

## Bug and problem reports / Support requests##

This plugin is still beta software, DO NOT USE IN PRODUCTION.
Please report bugs and problems on Github: https://github.com/ewallah/moodle-tool_translate/issues
We will do our best to solve your problems, but please note that due to limited resources we can't always provide per-case support.

## Feature proposals ##

Please issue feature proposals on Github: https://github.com/ewallah/moodle-tool_translate/issues
Please create pull requests on Github: https://github.com/ewallah/moodle-tool_translate/pulls
We are always interested to read about your feature proposals or even get a pull request from you, but please accept that we can handle your issues only as feature proposals and not as feature requests.

## Todo ##

 - implement Active Custom Translation (Extra parallel data to customize the machine translated output)
 - implement Custom Terminology so specific terms get translated better
 - better Moodle 4.00 question handling

## Status ##

[![Build Status](https://github.com/ewallah/moodle-tool_translate/Tests/badge.svg)](https://github.com/ewallah/moodle-tool_translate/actions)

## Copyright ##

iplusacademy.org

