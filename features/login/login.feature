Feature: Authentication
  In order to gain access to the site management area
  As an admin
  I need to be able to login and logout

  Scenario: Logging in
    Given I am on "/fr/admin/"
    Then I should be on "/fr/login"
    And I fill in "username" with "grr@domain.be"
    And I fill in "password" with "homer"
    And I press "S'identifier"
    Then I should be on "/fr/admin/"
    Then I should see "Gestion des paramètres"

  Scenario: Bad login
    Given I am on "/fr/login"
    And I fill in "username" with "grr@domain.be"
    And I fill in "password" with "123456"
    And I press "S'identifier"
    #Then print last response
    Then I should see "Identifiants invalides"
