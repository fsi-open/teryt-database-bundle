Feature: Parse streets xml file and import data into database

  Background:
    Given I successfully run console command "doctrine:schema:create"
    And I successfully run console command "doctrine:fixtures:load"

  Scenario: Import streets from xml file
    Given "streets.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <teryt>
      <catalog name="ULIC" type="all" date="2013-10-10">
        <row>
          <col name="WOJ">02</col>
          <col name="POW">23</col>
          <col name="GMI">09</col>
          <col name="RODZ_GMI">2</col>
          <col name="SYM">0884849</col>
          <col name="SYM_UL">10268</col>
          <col name="CECHA">ul.</col>
          <col name="NAZWA_1">Księżycowa</col>
          <col name="NAZWA_2"/>
          <col name="STAN_NA">2013-10-10</col>
        </row>
        <row>
          <col name="WOJ">02</col>
          <col name="POW">61</col>
          <col name="GMI">01</col>
          <col name="RODZ_GMI">1</col>
          <col name="SYM">0935802</col>
          <col name="SYM_UL">14018</col>
          <col name="CECHA">ul.</col>
          <col name="NAZWA_1">Narutowicza</col>
          <col name="NAZWA_2">Gabriela </col>
          <col name="STAN_NA">2013-10-10</col>
        </row>
      </catalog>
    </teryt>
    """
    And there are no provinces in database
    And following places was already imported
      | Identity | Name   |
      | 0884849  | City 1 |
      | 0935802  | City 2 |
    When I successfully run console command "teryt:import:streets" with argument "--file=teryt/streets.xml"
    Then following streets should exist in database
      | Identity | Type | Name        | Additional name | Place  |
      | 10268    | ul.  | Księżycowa  |                 | City 1 |
      | 14018    | ul.  | Narutowicza | Gabriela        | City 2 |
