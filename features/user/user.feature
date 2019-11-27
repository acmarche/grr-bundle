Feature: Manage user
  J'ajoute un utilisateur Doe Raoul
  Je change le nom de fred en Simpson
  Je me connecte avec un user créer

  Scenario: New admin user
    Given I am logged in as an admin
    And I am on "/admin/user/"
    Then I should see "Liste des 11 utilisateurs"
    When I follow "Nouvelle utilisateur"
    And I fill in "user_new[name]" with "Doe"
    And I fill in "user_new[first_name]" with "Raoul"
    And I fill in "user_new[email]" with "raoul@domain.com"
    And I fill in "user_new[username]" with "raoul"
    And I fill in "user_new[password]" with "12345"
    And I check "ROLE_GRR_ADMINISTRATOR"
    And I press "Sauvegarder"
    Then I should see "L'utilisateur a bien été ajouté"
    Then I should see "raoul@domain.com"

  Scenario: Edit fred user
    Given I am logged in as an admin
    Given I am on "/admin/user/"
    Then I follow "fred@domain.be"
    Then I follow "Modifier"
    And I fill in "user_advance[name]" with "Simpson"
    And I press "Sauvegarder"
    Then I should see "Simpson"

  Scenario: Test login nouvelle utilisateur
    Given I am logged in as an admin
    And I am on "/admin/user/"
    Then I should see "Liste des 11 utilisateurs"
    When I follow "Nouvelle utilisateur"
    And I fill in "user_new[name]" with "Fargue"
    And I fill in "user_new[first_name]" with "Joseph"
    And I fill in "user_new[email]" with "joseph@domain.com"
    And I fill in "user_new[username]" with "joseph"
    And I fill in "user_new[password]" with "12345"
    And I press "Sauvegarder"
    Then I should see "L'utilisateur a bien été ajouté"
    Then I should see "joseph@domain.com"
    When I am on "/logout/"
    Then I am on "/login"
    And I fill in "username" with "joseph@domain.com"
    And I fill in "password" with "12345"
    And I press "S'identifier"
    Then I should see "joseph@domain.com"
