Feature: Default homepage
    Background:
        Given I am on "/"

    Scenario: Check status code
        Then status code should be 200

    @javascript
    Scenario: Check contents
        Then I should see "Patchwork"