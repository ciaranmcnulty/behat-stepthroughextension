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
        When I have a failing step
        Then I should have a skipped step
      """
    And I have the context:
      """
      <?php

      use Behat\Behat\Context\Context;

      class FeatureContext implements Context
      {
          /**
           * @Given I have a step
           */
          function passingStep()
          {}

          /**
           * @When I have a failing step
           */
          function failingStep()
          {
            throw new Exception('Error');
          }

          /**
           * @Then I should have a skipped step
           */
          function skippedStep()
          {}

      }

      """

  Scenario: Pausing after first step
    When I run behat with the "--step-through" option
    Then I should see:
      """
      Feature: Multi-step feature

        Scenario:                   # features/feature.feature:3
          Given I have a step       # FeatureContext::passingStep()
        [Paused: press enter to continue]
      """
    And The process should not have ended

  Scenario: Continuing to the next step
    When I run behat with the "--step-through" option
    And I press enter
    Then I should see:
      """
      Feature: Multi-step feature

        Scenario:                           # features/feature.feature:3
          Given I have a step               # FeatureContext::passingStep()
        [Paused: press enter to continue]
          When I have a failing step        # FeatureContext::failingStep()
            Error (Exception)
        [Paused: press enter to continue]
      """
    And The process should not have ended

  Scenario: Skipping failing scenarios after failure
    When I run behat with the "--step-through" option
    And I press enter 2 times
    Then I should see:
      """
      Feature: Multi-step feature

        Scenario:                           # features/feature.feature:3
          Given I have a step               # FeatureContext::passingStep()
        [Paused: press enter to continue]
          When I have a failing step        # FeatureContext::failingStep()
            Error (Exception)
        [Paused: press enter to continue]
          Then I should have a skipped step # FeatureContext::skippedStep()

      --- Failed scenarios:

          features/feature.feature:3

      1 scenario (1 failed)
      3 steps (1 passed, 1 failed, 1 skipped)
      0m0.04s (9.41Mb)
      """
    And The process should have ended