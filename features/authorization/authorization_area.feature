Feature: Gestion des droits depuis un  area
  J'ajoute Jules et Lisa en tant que Administrateur de l'Esquare
  J'ajoute Jules et Lisa comme manager des rooms de l'Esquare

  Scenario: New administrateur area
    Given I am logged in as an admin
    Given I am on "/admin/area/"
    Then I should see "Les domaines"
    Then I follow "Esquare"
    Then I follow "Droits"
    Then I follow "Ajouter"
    When I select "1" from "authorization_area[role]"
    When I select "ADAMS jules" from "authorization_area_users"
    And I additionally select "ADAMS lisa" from "authorization_area_users"
    Then I press "Sauvegarder"
    And I should see "Nouvelle autorisation bien ajoutée"
    And I should see "ADAMS lisa"
    And I should see "ADAMS jules"

  Scenario: New room manager
    Given I am logged in as an admin
    Given I am on "/admin/area/"
    Then I should see "Les domaines"
    Then I follow "Esquare"
    Then I follow "Droits"
    Then I follow "Ajouter"
    When I select "2" from "authorization_area[role]"
    When I select "ADAMS jules" from "authorization_area_users"
    And I additionally select "ADAMS lisa" from "authorization_area_users"
    Then I press "Sauvegarder"
    And I should see "Nouvelle autorisation bien ajoutée"
    And I should see "ADAMS lisa"
    And I should see "ADAMS jules"
    #Deja les droits
    When I follow "Ajouter"
    When I select "2" from "authorization_area[role]"
    When I select "ADAMS jules" from "authorization_area_users"
    And I additionally select "ADAMS lisa" from "authorization_area_users"
    Then I press "Sauvegarder"
    And I should see "L' autorisation pour Esquare existe déjà pour ADAMS lisa"
