Feature: Gestion des droits depuis un utilisateur
  J'ajoute Bob en tant que Administrateur de l'Esquare
  J'ajoute Bob en tant que manager des rooms de l'Esquare

  Scenario: New administrateur area
    Given I am logged in as an admin
    Given I am on "/admin/user/"
    Then I follow "jules@domain.be"
    And I follow "Droits"
    And I follow "Ajouter"
    Then I select "Esquare" from "authorization_user_area"
    And I select "1" from "authorization_user[role]"
    Then I press "Sauvegarder"
    Then I should see "Nouvelle autorisation bien ajoutée"
    And I should see "ADAMS jules"
    And I should see "Esquare"

  Scenario: New room manger
    Given I am logged in as an admin
    Given I am on "/admin/user/"
    Then I follow "jules@domain.be"
    Then I follow "Droits"
    Then I follow "Ajouter"
    When I select "Esquare" from "authorization_user_area"
    When I select "2" from "authorization_user[role]"
    Then I press "Sauvegarder"
    And I should see "Nouvelle autorisation bien ajoutée"
    And I should see "ADAMS jules"
    And I should see "Esquare"
      # Deja les droits
    Then I follow "Ajouter"
    When I select "Esquare" from "authorization_user_area"
    When I select "2" from "authorization_user[role]"
    Then I press "Sauvegarder"
    And I should see "L' autorisation pour Esquare existe déjà pour ADAMS jules"

#  @javascript
# todo probleme rooms not found
#  Scenario: New room administrator Box et Relax Room
#    Given I am logged in as an admin
#    Then show last response
#    Given I am on "/admin/user/"
#    Then I follow "jules@domain.be"
#    And I follow "Droits"
#    And I follow "Ajouter"
#    Then I select "Esquare" from "authorization_user_area"
#    And I select "2" from "authorization_user[role]"
#    And I wait 3 seconds
#    #Then the "authorization_user_rooms" select box should contain "Box"
#    #Then I wait 2 seconds until I see "Box" in the "room-select" element
#    #Then show last response
#    And I select "Box" from "authorization_user_rooms"
#    And I additionally select "Meeting Room" from "authorization_user_rooms"
#    Then I press "Sauvegarder"
#    Then I should see "Nouvelle autorisation bien ajoutée"
#    And I should see "ADAMS jules"
#    And I should see "Box"
#    And I should see "Relax Room"
