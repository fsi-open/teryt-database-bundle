Feature: Parse streets xml file and import data into database

  Background:
    Given I successfully run console command "doctrine:schema:create"
    And I successfully run console command "doctrine:fixtures:load"

  Scenario: Import streets from xml file
    Given "streets.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <ulic>
      <catalog name="ULIC" type="all" date="2013-10-10">
        <row>
          <WOJ>02</WOJ>
          <POW>23</POW>
          <GMI>09</GMI>
          <RODZ_GMI>2</RODZ_GMI>
          <SYM>0884849</SYM>
          <SYM_UL>10268</SYM_UL>
          <CECHA>ul.</CECHA>
          <NAZWA_1>Księżycowa</NAZWA_1>
          <NAZWA_2>
          </NAZWA_2>
          <STAN_NA>2013-10-10</STAN_NA>
        </row>
        <row>
          <WOJ>02</WOJ>
          <POW>61</POW>
          <GMI>01</GMI>
          <RODZ_GMI>1</RODZ_GMI>
          <SYM>0935802</SYM>
          <SYM_UL>14018</SYM_UL>
          <CECHA>ul.</CECHA>
          <NAZWA_1>Narutowicza</NAZWA_1>
          <NAZWA_2>Gabriela </NAZWA_2>
          <STAN_NA>2013-10-10</STAN_NA>
        </row>
      </catalog>
    </ulic>
    """
    And following province was already imported
      | Code | Name         |
      | 02   | DOLNOŚLĄSKIE |
    And following district was already imported
      | Code | Name        | Province     |
      | 0223 | CZŁUCHOWSKI | DOLNOŚLĄSKIE |
      | 0261 | BUSKI       | DOLNOŚLĄSKIE |
    And following community was already imported
      | Code    | Name         | District    | Community type |
      | 0223091 | ŻORAWINA     | CZŁUCHOWSKI | gmina wiejska  |
      | 0261011 | JELENIA GÓRA | BUSKI       | gmina wiejska  |
    And following places was already imported
      | Identity | Name   | Community    |
      | 0884849  | City 1 | ŻORAWINA     |
      | 0935802  | City 2 | JELENIA GÓRA |
    And there are no streets in database
    When I successfully run console command "teryt:import:streets" with argument "--file=../../../../teryt/streets.xml"
    Then following streets should exist in database
      | Identity | Type | Name        | Additional name | Place  |
      | 10268    | ul.  | Księżycowa  |                 | City 1 |
      | 14018    | ul.  | Narutowicza | Gabriela        | City 2 |

  Scenario: Update street during import
    Given "streets.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <ulic>
      <catalog name="ULIC" type="all" date="2013-10-10">
        <row>
          <WOJ>02</WOJ>
          <POW>61</POW>
          <GMI>01</GMI>
          <RODZ_GMI>1</RODZ_GMI>
          <SYM>0935802</SYM>
          <SYM_UL>14018</SYM_UL>
          <CECHA>ul.</CECHA>
          <NAZWA_1>Narutowicza</NAZWA_1>
          <NAZWA_2>Gabriela </NAZWA_2>
          <STAN_NA>2013-10-10</STAN_NA>
        </row>
      </catalog>
    </ulic>
    """
    And following province was already imported
      | Code | Name         |
      | 02   | DOLNOŚLĄSKIE |
    And following district was already imported
      | Code | Name  | Province     |
      | 0261 | BUSKI | DOLNOŚLĄSKIE |
    And following community was already imported
      | Code    | Name         | District    | Community type |
      | 0261011 | JELENIA GÓRA | BUSKI       | gmina wiejska  |
    And following places was already imported
      | Identity | Name   | Community    |
      | 0935802  | City 1 | JELENIA GÓRA |
    And following streets was already imported
      | Identity | Type | Name        | Additional name | Place  |
      | 14018    | ul.  | NARUTOWICZA | GABRIELA        | City 1 |
    When I successfully run console command "teryt:import:streets" with argument "--file=../../../../teryt/streets.xml"
    Then following streets should exist in database
      | Identity | Type | Name        | Additional name | Place  |
      | 14018    | ul.  | Narutowicza | Gabriela        | City 1 |
