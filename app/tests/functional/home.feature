Feature:
    Background:
        Given I am on "/"

    Scenario: Homepage is available
        Then status code should be 200

    Scenario: Root element has flag class
        Then "html" element should have class "nojs"

    @javascript
    Scenario: JS code removes flag class
        Then "html" element should not have class "nojs"