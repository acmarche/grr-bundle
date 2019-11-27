Feature: Edit entries with periodicity

  Background:
    J'encode une réservation au 5 du mois courant à l'annnée courante
    La périodicité est journalière
    Et la date de fin est 3 jours plus tard que la date de début
    Given I am logged in as an admin
    When I follow "15"
    When I follow "Ajouter une entrée"
    And I fill in "entry_with_periodicity[name]" with "Tous les jours pendant 3 jours"
    And I fill the entry startTime with the date 5/10/2019
    And I fill in "entry_with_periodicity[duration][time]" with "3"
    And I select "Heure(s)" from "entry_with_periodicity_duration_unit"
    #1 => Chaque jour
    And I select "1" from "entry_with_periodicity[periodicity][type]"
    And I select "Relax Room" from "entry_with_periodicity_room"
    And I fill the periodicity endTime with the date 8/10/2019
    And I press "Sauvegarder"
    Then I should see "Tous les jours pendant 3 jours"

  Scenario: Edit one entry
    Given I am on the page show entry "Tous les jours pendant 3 jours"
    Then I follow "Modifier la réservation"
    And I fill in "entry[name]" with "Tous les jours pendant 4 jours"
    And I press "Sauvegarder"
    Then I should see "Tous les jours pendant 4 jours"
    Given I am on the page month view of month 10-2019 and area "Esquare"
    Then I should see "Tous les jours pendant 3 jours"
    And I should see "Tous les jours pendant 4 jours"

  Scenario: Edit periodicity remove periodicity
    Given I am on the page show entry "Tous les jours pendant 3 jours"
    Then I follow "Modifier la périodicité"
    And I select "0" from "entry_with_periodicity[periodicity][type]"
    And I press "Sauvegarder"
    Then should not see "Périodicité"
    Given I am on the page month view of month 10-2019 and area "Esquare"
    Then I should see "Tous les jours pendant 3 jours" exactly "1" times

  Scenario: Change end date periodicity
    Given I am on the page show entry "Tous les jours pendant 3 jours"
    Then I follow "Modifier la périodicité"
    And I fill in "entry_with_periodicity[periodicity][endTime][day]" with "10"
    And I press "Sauvegarder"
    Given I am on the page month view of month 10-2019 and area "Esquare"
    Then I should see "Tous les jours pendant 3 jours" exactly "6" times

  Scenario: Change type periodicity
    Je change en tous les ans
    Given I am on the page show entry "Tous les jours pendant 3 jours"
    Then I follow "Modifier la périodicité"
    And I select "4" from "entry_with_periodicity[periodicity][type]"
    And I fill in "entry_with_periodicity[periodicity][endTime][month]" with "10"
    And I fill in "entry_with_periodicity[periodicity][endTime][year]" with "2022"
    And I press "Sauvegarder"
    Then I should see "samedi 5 octobre 2019"
    And I should see "lundi 5 octobre 2020"
    And I should see "mardi 5 octobre 2021"
    And I should see "mercredi 5 octobre 2022"
    Given I am on the page month view of month 10-2019 and area "Esquare"
    Then I should see "Tous les jours pendant 3 jours" exactly "1" times

  Scenario: Change type periodicity to every week
    Given I am on the page show entry "Tous les jours pendant 3 jours"
    Then I follow "Modifier la périodicité"
    And I select "2" from "entry_with_periodicity[periodicity][type]"
    And I check "Lundi"
    And I check "Mardi"
    And I select "1" from "entry_with_periodicity[periodicity][weekRepeat]"
    And I fill in "entry_with_periodicity[periodicity][endTime][day]" with "22"
    And I press "Sauvegarder"
    Then I should see "samedi 5 octobre 2019"
    And I should see "lundi 7 octobre 2019"
    And I should see "mardi 8 octobre 2019"
    And I should see "lundi 14 octobre 2019"
    And I should see "mardi 15 octobre 2019"
    And I should see "lundi 21 octobre 2019"
    And I should see "mardi 22 octobre 2019"
    Given I am on the page month view of month 10-2019 and area "Esquare"
    Then I should see "Tous les jours pendant 3 jours" exactly "7" times
