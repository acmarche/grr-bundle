Feature: Manage room
  J'ajoute la room "Room demo" dans l'area "Hdv"
  Je renomme "Salle Conseil" en "Salle Conseil Demo"

  Scenario: New room
    Given I am logged in as an admin
    And I am on "/admin/area/"
    #Then print last response
    Given I am on "/admin/area/"
    Then I should see "Les domaines"
    When I follow "Hdv"
    When I follow "Nouvelle ressource"
    And I fill in "room[name]" with "Room demo"
    And I press "Sauvegarder"
    Then I should see "La ressource a bien été ajoutée"

  Scenario: Edit room
    Given I am logged in as an admin
    Given I am on "/admin/area/"
    Then I should see "Les domaines"
    When I follow "Hdv"
    When I follow "Salle Conseil"
    When I follow "Modifier"
    And I fill in "room[name]" with "Salle Conseil demo"
    And I press "Sauvegarder"
    Then I should see "Salle Conseil demo"
