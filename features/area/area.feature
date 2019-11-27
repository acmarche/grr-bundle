Feature: Manage area
  J'ajoute une area avec comme nom Area demo
  Je la renomme en Hdv demon
  Je lui attribute les types de réservations : Cours, réunion

  Scenario: New area
    Given I am logged in as an admin
    And I am on "/admin/area/"
    Then I should see "Les domaines"
    When I follow "Nouveau domaine"
    And I fill in "area[name]" with "Area demo"
    And I press "Sauvegarder"
    Then I should see "Le domaine a bien été ajouté"
    And I save a screenshot in "index.png"

  Scenario: Edit area
    Given I am logged in as an admin
    Given I am on "/admin/area/"
    Then I should see "Les domaines"
    When I follow "Hdv"
    When I follow "Modifier"
    And I fill in "area[name]" with "Hdv demo"
    And I press "Sauvegarder"
    Then I should see "Hdv demo"

  Scenario: Attribution de types d'entrée
    Given I am logged in as an admin
    Given I am on "/admin/area/"
    Then I should see "Les domaines"
    When I follow "Hdv"
    When I follow "Types d'entrée"
    And I check "Cours"
    And I check "Reunion"
    And I press "Sauvegarder"
    Then I should see "Hdv"
