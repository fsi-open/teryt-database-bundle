Feature: Load community types into database

  Background:
    Given I successfully run console command "doctrine:schema:create"

  Scenario: Load community types into database
    When I successfully run console command "doctrine:fixtures:load"
    Then following community types should exist in database
      | Type | Name                                      |
      | 1    | gmina miejska                             |
      | 2    | gmina wiejska                             |
      | 3    | gmina miejsko-wiejska                     |
      | 4    | miasto w gminie miejsko-wiejskiej         |
      | 5    | obszar wiejski w gminie miejsko-wiejskiej |
      | 8    | dzielnica w m.st. Warszawa                |
      | 9    | delegatura gminy miejskiej                |