Feature: Gestion des paramètres
  L'utilisateur anonyme ne peut pas y accéder

  Scenario: User access denied
    Given I am logged in as user "bob@domain.be"
    #Then print last response
    Given I am on "/fr/admin/setting/"
    Then the response status code should be 403

  Scenario: Edit settings
    Given I am logged in as an admin
    Given I am on "/fr/admin/setting/"
    Then I should see "Paramètres de Grr"
    When I follow "Modifier les paramètres"
    And I fill in "setting[grr_company]" with "Afm"
    And I fill in "setting[grr_nb_calendar]" with "1"
    And I press "Sauvegarder"
    Then I should see "Afm"
