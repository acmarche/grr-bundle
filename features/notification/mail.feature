Feature: Notification email
  J'active les notifications

  Background:
    Given I am logged in as an admin
    And I am on "/fr/account/show/"

  Scenario: J'active les notifications
    When I follow "Notifications"
    And I check "Lors de la création"
    And I check "Lors de la modification"
    And I check "Lors de la suppression"
    And I press "Sauvegarder"
    Then I should see "Les notifications ont bien été modifiées"
    Given I am on homepage
    When I follow "15"
    When I follow "Ajouter une réservation"
    And I fill in "entry_with_periodicity[name]" with "My reservation"
    And I select "0" from "entry_with_periodicity_startTime_time_minute"
    And I select "0" from "entry_with_periodicity_startTime_time_minute"
    And I press "Sauvegarder"
    Then the response status code should be 200
    Then I should see "My reservation"
