Feature: Account
  Je me connecte avec bob
  Je modifie mon profil en changeant mon nom en "Batman"
  Je seléctionne Esquare comme Area par defaut
  Je change le mot de passe en "marge"
  Je test le nouveau mot de passe

  Scenario: Modify account
    Given I am logged in as user "bob@domain.be"
    And I am on "/account/show/"
    Then I should see "Mon profil Adams bob"
    When I follow "Modifier"
    And I fill in "user[name]" with "Batman"
    And I select "Esquare" from "user_area"
    #And I select "Box" from "user_room"
    And I press "Sauvegarder"
    Then I should see "Mon profil Batman bob"
    Then I should see "Esquare"

  Scenario: Modify my password
    Given I am logged in as user "bob@domain.be"
    And I am on "/account/show/"
    Then I should see "Mon profil Adams bob"
    When I follow "Mot de passe"
    Then I should see "Changement du mot de passe pour ADAMS bob"
    And I fill in "user_password[password][first]" with "marge"
    And I fill in "user_password[password][second]" with "marge"
    And I press "Sauvegarder"
    Then I should see "Le mot de passe a bien été changé"

    #Test de connection avec le nouveau mot de passe

    When I am on "/logout"
    Then I am on "/login"
    And I fill in "username" with "bob@domain.be"
    And I fill in "password" with "marge"
    And I press "S'identifier"
    And I am on "/account/show/"
    Then I should see "Mon profil Adams bob"
