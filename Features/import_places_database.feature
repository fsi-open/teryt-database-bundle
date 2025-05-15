Feature: Parse places xml file and import data into database

  Background:
    Given I successfully run console command "doctrine:schema:create"

  Scenario: Import places from xml file
    Given "places.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <simc>
      <catalog name="SIMC" type="all" date="2013-03-06">
        <row>
          <WOJ>04</WOJ>
          <POW>11</POW>
          <GMI>05</GMI>
          <RODZ_GMI>5</RODZ_GMI>
          <RM>01</RM>
          <MZ>1</MZ>
          <NAZWA>Rzeczyca</NAZWA>
          <SYM>0867650</SYM>
          <SYMPOD>0867650</SYMPOD>
          <STAN_NA>2013-03-06</STAN_NA>
        </row>
      </catalog>
    </simc>
    """
    And following province was already imported
      | Code | Name               |
      | 04   | KUJAWSKO-POMORSKIE |
    And following district was already imported
      | Code | Name         | Province           |
      | 0411 | RADZIEJOWSKI | KUJAWSKO-POMORSKIE |
    And places table in database is empty
    And there is a place type with type "01" and name "wieś"
    And there is a community in database with code "0411055" and name "Gmina Rzerzyca" in district "RADZIEJOWSKI"
    When I successfully run console command "teryt:import:places" with argument "--file=teryt/places.xml"
    Then places table in database should have following records
      | Identity | Name     | Place type | Community      |
      | 0867650  | Rzeczyca | wieś       | Gmina Rzerzyca |

  Scenario: Update place during import
    Given "places.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <simc>
      <catalog name="SIMC" type="all" date="2013-03-06">
        <row>
          <WOJ>04</WOJ>
          <POW>11</POW>
          <GMI>05</GMI>
          <RODZ_GMI>5</RODZ_GMI>
          <RM>01</RM>
          <MZ>1</MZ>
          <NAZWA>Rzeczyca Górna</NAZWA>
          <SYM>0867643</SYM>
          <SYMPOD>0867643</SYMPOD>
          <STAN_NA>2013-03-06</STAN_NA>
        </row>
        <row>
          <WOJ>04</WOJ>
          <POW>11</POW>
          <GMI>05</GMI>
          <RODZ_GMI>5</RODZ_GMI>
          <RM>01</RM>
          <MZ>1</MZ>
          <NAZWA>Rzeczyca</NAZWA>
          <SYM>0867650</SYM>
          <SYMPOD>0867643</SYMPOD>
          <STAN_NA>2013-03-06</STAN_NA>
        </row>
      </catalog>
    </simc>
    """
    And following province was already imported
      | Code | Name               |
      | 04   | KUJAWSKO-POMORSKIE |
    And following district was already imported
      | Code | Name         | Province           |
      | 0411 | RADZIEJOWSKI | KUJAWSKO-POMORSKIE |
    And places table in database is empty
    And there is a place type with type "01" and name "wieś"
    And there is a community in database with code "0411055" and name "Gmina Rzerzyca" in district "RADZIEJOWSKI"
    Then following place should exist in database
      | Identity | Name           | Place type | Community      |
      | 0867643  | RZECZYCA GÓRNA | wieś       | Gmina Rzerzyca |
      | 0867650  | RZECZYCA       | wieś       | Gmina Rzerzyca |
    When I successfully run console command "teryt:import:places" with argument "--file=teryt/places.xml"
    Then places table in database should have following records
      | Identity | Name           | Parent place   | Place type | Community      |
      | 0867643  | Rzeczyca Górna |                | wieś       | Gmina Rzerzyca |
      | 0867650  | Rzeczyca       | Rzeczyca Górna | wieś       | Gmina Rzerzyca |
