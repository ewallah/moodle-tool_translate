@tool @tool_translate
Feature: Translate settings
  In order to translate
  As an administrator
  I need to be able to configure different engines

  Background:
    Given I log in as "admin"

  Scenario: See all translation settings
    When I navigate to "Language > Translation settings" in site administration
    Then I should see "AWS translate"
    And I should see "Google translate"

  Scenario: List translation engines
    When I navigate to "Language > Translation settings > Translation engines" in site administration
    Then I should see "AWS translate"
    And I should see "Google translate"

  Scenario: Engines cannot be configured with only one language installed
    When I navigate to "Language > Translation settings > AWS translate" in site administration
    Then I should not see "access key"
    And I navigate to "Language > Translation settings > Google translate" in site administration
    Then I should not see "Google API key"

  @lang
  Scenario: Engine configuration can be done when more than one language is installed
    And I navigate to "Language > Language packs" in site administration
    When I set the field "Available language packs" to "en_ar"
    And I press "Install selected language pack(s)"
    Then I should see "Language pack 'en_ar' was successfully installed"
    When I navigate to "Language > Translation settings > AWS translate" in site administration
    Then I should see "access key"
    When I navigate to "Language > Translation settings > Google translate" in site administration
    Then I should see "Google API key"
