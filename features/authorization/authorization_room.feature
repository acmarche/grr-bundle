Feature: Gestion des droits depuis une room
  J'ajoute Bob et Lisa en tant que room manager de l'Esquare

  Scenario: New room manager
    Given I am logged in as an admin
    Given I am on "/admin/area/"
    Then I should see "Les domaines"
    Then I follow "Esquare"
    Then I follow "Droits"
    Then I follow "Ajouter"
    When I select "Box" from "authorization_area_rooms"
    And I additionally select "Meeting Room" from "authorization_area_rooms"
    When I select "ADAMS jules" from "authorization_area_users"
    And I additionally select "ADAMS lisa" from "authorization_area_users"
    Then I press "Sauvegarder"
    And I should see "Nouvelle autorisation bien ajoutée"
    And I should see "ADAMS lisa"
    And I should see "ADAMS jules"
    And I should see "Box"
    And I should see "Meeting Room"
