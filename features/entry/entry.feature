Feature: Manage entries
  J'ajoute une area avec comme nom Area demo
  Je la renomme en Hdv demon
  Je lui attribute les types de réservations : Cours, réunion

  #Scenario: User not login
  #todo comment acceder a entry/new

  Background:
    Given I am logged in as an admin
    Given I am on homepage
    When I follow "15"
    When I follow "Ajouter une entrée"
    And I fill in "entry_with_periodicity[name]" with "My reservation"

  Scenario: Add entry
    And I press "Sauvegarder"
    Then I should see "My reservation"
    Then I should see "08:00"
    Then I should see "08:30"

  Scenario: Add entry with minutes
    And I fill in "entry_with_periodicity[duration][time]" with "90"
    And I select "Minute(s)" from "entry_with_periodicity_duration_unit"
    And I press "Sauvegarder"
    Then I should see "My reservation"
    Then I should see "08:00"
    Then I should see "09:30"

  Scenario: Add entry with float minutes
    And I fill in "entry_with_periodicity[duration][time]" with "35.5"
    And I select "Minute(s)" from "entry_with_periodicity_duration_unit"
    And I press "Sauvegarder"
    Then I should see "Une nombre à virgule n'est autorisé que pour une durée par heure"

  Scenario: Add entry with hours
    And I fill in "entry_with_periodicity[duration][time]" with "2.5"
    And I select "Heure(s)" from "entry_with_periodicity_duration_unit"
    And I press "Sauvegarder"
    Then I should see "My reservation"
    Then I should see "08:00"
    Then I should see "10:30"

  Scenario: Add entry with weeks
    And I select "2" from "entry_with_periodicity_startTime_date_day"
    And I select "9" from "entry_with_periodicity_startTime_date_month"
    And I select "2019" from "entry_with_periodicity_startTime_date_year"
    And I fill in "entry_with_periodicity[duration][time]" with "3"
    And I select "Semaine(s)" from "entry_with_periodicity_duration_unit"
    And I press "Sauvegarder"
    #Then print last response
    Then I should see "My reservation"
    Then I should see "lundi 2 septembre 2019 à 08:00"
    Then I should see "lundi 23 septembre 2019 à 08:00"

  Scenario: Add entry with days
    And I select "2" from "entry_with_periodicity_startTime_date_day"
    And I select "9" from "entry_with_periodicity_startTime_date_month"
    And I select "2019" from "entry_with_periodicity_startTime_date_year"
    And I fill in "entry_with_periodicity[duration][time]" with "3"
    And I select "Jour(s)" from "entry_with_periodicity_duration_unit"
    And I press "Sauvegarder"
    Then I should see "My reservation"
    Then I should see "lundi 2 septembre 2019 à 08:00"
    Then I should see "jeudi 5 septembre 2019 à 08:00"

  Scenario: Add entry overload closing area
    And I select "2" from "entry_with_periodicity_startTime_date_day"
    And I select "9" from "entry_with_periodicity_startTime_date_month"
    And I select "2019" from "entry_with_periodicity_startTime_date_year"
    And I select "20" from "entry_with_periodicity_startTime_time_hour"
    And I select "30" from "entry_with_periodicity_startTime_time_minute"
    And I fill in "entry_with_periodicity[duration][time]" with "3"
    And I select "Heure(s)" from "entry_with_periodicity_duration_unit"
    And I press "Sauvegarder"
    Then I should see "L'heure de fin doit être plus petite que l'heure de fermeture de la salle"

  Scenario: Add entry overload opening area
    And I select "2" from "entry_with_periodicity_startTime_date_day"
    And I select "9" from "entry_with_periodicity_startTime_date_month"
    And I select "2019" from "entry_with_periodicity_startTime_date_year"
    And I select "6" from "entry_with_periodicity_startTime_time_hour"
    And I select "30" from "entry_with_periodicity_startTime_time_minute"
    And I fill in "entry_with_periodicity[duration][time]" with "3"
    And I select "Heure(s)" from "entry_with_periodicity_duration_unit"
    And I press "Sauvegarder"
    Then I should see "L'heure de début doit être plus grande que l'heure d'ouverture de la salle"
