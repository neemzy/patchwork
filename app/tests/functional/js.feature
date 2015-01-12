Feature:
    JavaScript support detection

    Background:
        Given I am on homepage

    Scenario: JavaScript is disabled
        Then "html" element should have class "no-js"

    @javascript
    Scenario: JavaScript is enabled
        Then "html" element should not have class "no-js"
