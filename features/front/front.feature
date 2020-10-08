Feature: Front view
  Je test la home page
  Je test la vue par mois
  Je test la vue par semaine

  Background:
    Given I am logged in as an admin
    Given I am on homepage

  Scenario: Homepage
    Then the response status code should be 200
    Then I should see "Aujourd'hui"
    And I should see "Mercredi"

  Scenario: Vue par mois
    Then the response status code should be 200
    And I should see "Réunion a ce jour"
    Then I follow "Réunion a ce jour"
    Then I should see "Location"

  Scenario: Vue par semaine
    When I follow this week
    Then the response status code should be 200
    And I should see "Réunion a ce jour"
    Then I follow "Réunion a ce jour"
    Then I should see "Location"

  Scenario: Vue par jour
    Then I follow this day
    Then the response status code should be 200
    And I should see "Réunion a ce jour"
    Then I follow "Réunion a ce jour"
    Then I should see "Location"
