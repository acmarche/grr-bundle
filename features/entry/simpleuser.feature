Feature: Verification des droits pour la gestion des entrées
  Bob est administrateur de l'Esquare
  Bob n'a pas de droits sur Hdv
  Brenda a le droit d'encoder dans Salle Conseil
  Brend n'est pas administrateur ou manager de l'Hdv

  Scenario: Bob ajoute une entrée dans la salle Box
    Given I am logged in as user "bob@domain.be"
    Given I am on homepage
    Then I follow "15"
    And I follow "Ajouter une entrée"
    And I fill in "entry_with_periodicity[name]" with "My reservation"
    And I fill the entry startTime with the date 5/10/2019
    Then I should not see "Hdv"
    And I select "Box" from "entry_with_periodicity_room"
    And I press "Sauvegarder"
    Then I should see "My reservation"
    Then I should see "La réservation a bien été ajoutée"

  Scenario: Bob modifie une entrée dans Esquare/Relax Room
    Given I am logged in as user "bob@domain.be"
    Given I am on the page month view of month 08-2019 and area "Esquare"
    And I follow "Réunion henalux"
    And I should see "Modifier"
    And I should see "Supprimer"
    Then I follow "Modifier"
    And I should not see "Hdv"
    And I fill in "entry[name]" with "Réunion henalux 22"
    And I press "Sauvegarder"
    Then I should see "Réunion henalux 22"

  Scenario: Bob ne peux pas modifier une entrée de l'Hdv
    Given I am logged in as user "bob@domain.be"
    Given I am on the page show entry "Réunion conseillers"
    Then I should not see "Modifier"
    And I should not see "Supprimer"

  Scenario: Brenda ajoute une entrée dans la Salle du Conseil
    Given I am logged in as user "brenda@domain.be"
    Given I am on the page month view of month 10-2019 and area "Hdv"
    Then I follow "15"
    And I follow "Ajouter une entrée"
    And I fill in "entry_with_periodicity[name]" with "My reservation"
    And I fill the entry startTime with the date 5/10/2019
    Then I should not see "Esquare"
    And I select "Salle Conseil" from "entry_with_periodicity_room"
    And I press "Sauvegarder"
    Then I should see "My reservation"
    Then I should see "La réservation a bien été ajoutée"

  Scenario: Brenda ne peux pas modifier une entrée dans Hdv/Cafétaria
    Given I am logged in as user "brenda@domain.be"
    Given I am on the page show entry "Réunion détente"
    Then I should not see "Modifier"
    And I should not see "Supprimer"

  Scenario: Brenda ne peux pas modifier une entrée dans Esquare/Relax Room
    Given I am logged in as user "brenda@domain.be"
    Given I am on the page show entry "Réunion henalux"
    Then I should not see "Modifier"
    And I should not see "Supprimer"
