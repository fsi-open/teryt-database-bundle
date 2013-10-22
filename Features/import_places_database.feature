Feature: Parse places xml file and import data into database

  Background:
    Given I successfully run console command "doctrine:schema:create"

  Scenario: Import places dictionary from xml file
    Given "places.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <teryt>
      <catalog name="SIMC" type="all" date="2013-03-06">
        <row>
          <col name="WOJ">04</col>
          <col name="POW">11</col>
          <col name="GMI">05</col>
          <col name="RODZ_GMI">5</col>
          <col name="RM">01</col>
          <col name="MZ">1</col>
          <col name="NAZWA">Rzeczyca</col>
          <col name="SYM">0867650</col>
          <col name="SYMPOD">0867650</col>
          <col name="STAN_NA">2013-03-06</col>
        </row>
      </catalog>
    </teryt>
    """
    And places table in database is empty
    And there is a place type with type "01" and name "wieś"
    And there is a community in database with code "041105" and name "Gmina Rzerzyca"
    When I successfully run console command "teryt:import:places" with argument "--file=teryt/places.xml"
    Then places table in database should have following records
      | Identity | Name     | Place type | Community      |
      | 0867650  | Rzeczyca | wieś       | Gmina Rzerzyca |