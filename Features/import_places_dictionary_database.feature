Feature: Parse places dictionary xml file and import data into database

  Background:
    Given I successfully run console command "doctrine:schema:create"

  Scenario: Import places dictionary from xml file
    Given "places-dictionary.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <simc>
      <catalog name="SIMC" type="all" date="2013-02-28">
        <row>
          <RM>01</RM>
          <NAZWA_RM>wieś          </NAZWA_RM>
          <STAN_NA>2013-02-28</STAN_NA>
        </row>
        <row>
          <RM>02</RM>
          <NAZWA_RM>kolonia</NAZWA_RM>
          <STAN_NA>2013-02-28</STAN_NA>
        </row>
        <row>
          <RM>03</RM>
          <NAZWA_RM>przysiółek</NAZWA_RM>
          <STAN_NA>2013-02-28</STAN_NA>
        </row>
      </catalog>
    </simc>
    """
    And places dictionary table in database is empty
    When I successfully run console command "teryt:import:places-dictionary" with argument "--file=teryt/places-dictionary.xml"
    Then places dictionary table in database should have following records
      | Type | Name       |
      | 01   | wieś       |
      | 02   | kolonia    |
      | 03   | przysiółek |

  Scenario: Update places dictionary during import
    Given "places-dictionary.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <simc>
      <catalog name="SIMC" type="all" date="2013-02-28">
        <row>
          <RM>01</RM>
          <NAZWA_RM>wieś</NAZWA_RM>
          <STAN_NA>2013-02-28</STAN_NA>
        </row>
        <row>
          <RM>02</RM>
          <NAZWA_RM>kolonia</NAZWA_RM>
          <STAN_NA>2013-02-28</STAN_NA>
        </row>
        <row>
          <RM>03</RM>
          <NAZWA_RM>przysiółek</NAZWA_RM>
          <STAN_NA>2013-02-28</STAN_NA>
        </row>
      </catalog>
    </simc>
    """
    And places dictionary table in database is empty
    Then following places dictionary exist in database
      | Type | Name       |
      | 01   | WIEŚ       |
      | 02   | KOLONIA    |
      | 03   | PRZYSIÓŁEK |
    When I successfully run console command "teryt:import:places-dictionary" with argument "--file=teryt/places-dictionary.xml"
    Then places dictionary table in database should have following records
      | Type | Name       |
      | 01   | wieś       |
      | 02   | kolonia    |
      | 03   | przysiółek |
