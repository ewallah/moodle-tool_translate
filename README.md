# Translate tool
What if you have excellent content but your students do not have a clue about what you are talking?

Translating a course is a difficult process. We tried a translation bureau, but this was a difficult import/export exercise, incredibly expensive and still "All text had to be reviewed by a native speaker."

So we moved to Neural Machine translations to have our content available to students who do not speak our language. With this plugin, the results were the same: "All text has to be reviewed by a native speaker.", but this at a fraction of the cost (the AWS free tier translate engine even let you translate 2 million characters free/month).
This plugin helps admins translate a course or course module using Neural Machine Engines at a minimum cost. All parts of a course are handled: course fields - enrolment methods - sections - questions....

# Currently supported translate engines:
  - Google
  - AWS
  - Deepl

# Idea
The tool_translate plugin uses the Experimental approach of Search and replace tool. It detects the implemented tables and fields related to a course or module and replaces the (html) text in the database with a translation received from a translation engine.

# Warnings
 - All translated text has to be reviewed by a native speaker.
 - This plugin translates text, make a backup before you start.
 - This plugin is still in Beta version - do not use it in production environment.
 - The translation engines are a Paying service.

# Admin tools
Check the global documentation about admin tools:  https://docs.moodle.org/400/en/Admin_tools

# Requirements
This plugin requires Moodle 3.11+

# Installation
Install the plugin like any other plugin to folder /admin/tool/translate
See https://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins

# Initial Configuration
This plugin does needs at least one translation engine to be configured after installation.

# Theme support
This plugin is developed and tested on Moodle Core's Boost theme and Boost child themes, including Moodle Core's Classic theme.

# Plugin repositories
This plugin will be published and regularly updated on Github: https://github.com/ewallah/moodle-tool_translate

# Bug and problem reports / Support requests
This plugin is carefully developed and thoroughly tested, but bugs and problems can always appear.
Please report bugs and problems on Github: https://github.com/ewallah/moodle-tool_translate/issues
We will do our best to solve your problems, but please note that due to limited resources we can't always provide per-case support.

# Feature proposals
Please issue feature proposals on Github: https://github.com/ewallah/moodle-tool_translate/issues
Please create pull requests on Github: https://github.com/ewallah/moodle-tool_translate/pulls
We are always interested to read about your feature proposals or even get a pull request from you, but please accept that we can handle your issues only as feature proposals and not as feature requests.

# Moodle release support
This plugin requires Moodle 3.11+

# TODO
  - Implement list of terms ()
  - Translate in background (overnight)
  - Translate plugins
  - Better command line support
  - Add translating engines
  - Translate Microsoft Word documents - PDF documents


# Status
[![Build Status](https://github.com/ewallah/moodle-tool_translate/workflows/Tests/badge.svg)](https://github.com/ewallah/moodle-tool_translate/actions)

# Copyright
eWallah.net
