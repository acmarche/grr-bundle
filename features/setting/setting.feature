Feature: Gestion des paramètres
  L'utilisateur anonyme ne peut pas y accéder

  Scenario: User access denied
    Given I am logged in as user "bob@domain.be"
    #Then print last response
    Given I am on "/admin/setting/"
    Then the response status code should be 403

  Scenario: Edit settings
    Given I am logged in as an admin
    Given I am on "/admin/setting/"
    Then I should see "Paramètres de Grr"
    When I follow "Editer"
    And I fill in "general_setting[company]" with "Afm"
    And I fill in "general_setting[nb_calendar]" with "1"
    And I press "Sauvegarder"
    Then I should see "Afm"
