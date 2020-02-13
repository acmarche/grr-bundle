Feature: Gestion des droits depuis une room
  J'ajoute Bob et Lisa en tant que room manager de l'Esquare

  Scenario: New room manager
    Given I am logged in as an admin
    Given I am on "/fr/admin/area/"
    Then I should see "Liste des domaines"
    Then I follow "Esquare"
    Then I follow "Autorisations"
    Then I follow "Ajouter"
    When I select "Box" from "authorization_area_rooms"
    And I additionally select "Meeting Room" from "authorization_area_rooms"
    When I select "ADAMS jules" from "authorization_area_users"
    And I additionally select "ADAMS lisa" from "authorization_area_users"
    Then I press "Sauvegarder"
    And I should see "L'autorisation a bien été ajoutée"
    And I should see "ADAMS lisa"
    And I should see "ADAMS jules"
    And I should see "Box"
    And I should see "Meeting Room"
