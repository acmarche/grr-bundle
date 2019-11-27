Feature: Gestion des types d'entrées
  J'ajoute un type d'entrée dont la lettre est déjà utilisée
  J'ajoute le type d'entrée "MyType"
  Je renomme le type d'entrée "" en ""

  Scenario: Lettre "A" déjà utilisée
    Given I am logged in as an admin
    Given I am on "/admin/entrytype/"
    Then I should see "Types de réservation"
    When I follow "Nouveau type"
    And I fill in "type_entry[name]" with "Racc"
    And I fill in "type_entry[letter]" with "A"
    And I press "Sauvegarder"
    Then I should see "Cette lettre est déjà utilisée"

  Scenario: J'ajoute le type d'entrée "Racc"
    Given I am logged in as an admin
    Given I am on "/admin/entrytype/"
    Then I should see "Types de réservation"
    When I follow "Nouveau type"
    And I fill in "type_entry[name]" with "Racc"
    And I fill in "type_entry[letter]" with "R"
    And I press "Sauvegarder"
    Then I should see "Le type de réservation a bien été ajouté"

  Scenario: Je renomme Bureau en Bureaux
    Given I am logged in as an admin
    Given I am on "/admin/entrytype/"
    Then I should see "Types de réservation"
    When I follow "Bureau"
    When I follow "Modifier"
    And I fill in "type_entry[name]" with "Bureaux"
    And I press "Sauvegarder"
    Then I should see "Bureaux"
