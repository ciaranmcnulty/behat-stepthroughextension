Feature: Pausing after specs
  In order to debug failing scenarios more easile
  As a developer
  I should be able to pause after each step in a spec

  Background:
    Given I have the configuration:
      """
      default:
        extensions:
          Cjm\Behat\StepThroughExtension: ~
      """
    And I have the feature:
      """
      Feature: Multi-step feature

      Scenario:
        Given I have a step
        When I have another step
        Then I should have a step
      """
    And I have the context:
      """
      <?php

      use Behat\Behat\Context\Context;

      class FeatureContext implements Context
      {
          /**
           * @Given I (should) have a(nother) step
           */
          function passingStep()
          {}
      }

      """

  Scenario: Pausing after first step
    When I run behat with the "--step-through" option
    Then Output should end with:
      """
        Scenario:                   # features/feature.feature:3
          Given I have a step       # FeatureContext::passingStep()
        [Paused: press enter to continue]
      """
    And The process should not have ended

  Scenario: Continuing to the next step
    When I run behat with the "--step-through" option and press enter
    Then Output should end with:
      """
        Scenario:                   # features/feature.feature:3
          Given I have a step       # FeatureContext::passingStep()
        [Paused: press enter to continue]
          When I have another step  # FeatureContext::passingStep()
        [Paused: press enter to continue]
      """
    And The process should not have ended