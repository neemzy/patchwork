Feature:
    Background:
        Given I am on homepage

    Scenario: Static checks
        Then the response status code should be 200
        And "html" element should have class "no-js"

    @javascript
    Scenario: Dynamic checks
        Then "html" element should not have class "no-js"