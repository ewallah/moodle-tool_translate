@tool @tool_translate @lang

Feature: Translate a course
  An administrator can translate a course

  Background:
    Given the following config values are set as admin:
      | access_key | fake_access | translateengine_aws |
      | secret_key | fake_secret | translateengine_aws |
      | region     | eu-north-1  | translateengine_aws |
    And the following "courses" exist:
      | fullname | shortname | lang |
      | Course 1 | C1        | fr   |
    And the following "activities" exist:
      | activity | name | intro    | course | idnumber |
      | page     | modA | page     | C1     | modA     |
      | lesson   | modB | less     | C1     | modB     |
      | wiki     | modC | wiki     | C1     | modC     |
      | glossary | modD | glossary | C1     | modD     |
    And I log in as "admin"
    And I navigate to "Language > Language packs" in site administration
    When I set the field "Available language packs" to "en_ar"
    And I press "Install selected language pack(s)"
    Then I should see "Language pack 'en_ar' was successfully installed"

  Scenario: Tranlate course to french
    Given I am on "Course 1" course homepage
    And I navigate to "Translate" in current page administration
    Then I should not see "no engine configured"
    And I should see "AWS translate"
    And I should see "Course" in the ".rowid0" "css_element"
    And I click on "fr" "button" in the ".rowid0" "css_element"
    And I click on "fr" "button" in the ".rowid1" "css_element"
    And I click on "fr" "button" in the ".rowid2" "css_element"
    And I click on "fr" "button" in the ".rowid3" "css_element"
    And I click on "fr" "button" in the ".rowid4" "css_element"
