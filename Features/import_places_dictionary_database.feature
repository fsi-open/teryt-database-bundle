Feature: Parse places dictionary xml file and import data into database

  Background:
    Given I successfully run console command "doctrine:schema:create"

  Scenario: Import places dictionary from xml file
    Given "places-dictionary.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <teryt>
      <catalog name="SIMC" type="all" date="2013-02-28">
        <row>
          <col name="RM">01</col>
          <col name="NAZWA_RM">wieś                    </col>
          <col name="STAN_NA">2013-02-28</col>
        </row>
        <row>
          <col name="RM">02</col>
          <col name="NAZWA_RM">kolonia                 </col>
          <col name="STAN_NA">2013-02-28</col>
        </row>
        <row>
          <col name="RM">03</col>
          <col name="NAZWA_RM">przysiółek              </col>
          <col name="STAN_NA">2013-02-28</col>
        </row>
      </catalog>
    </teryt>
    """
    And places dictionary table in database is empty
    When I successfully run console command "teryt:import:places-dictionary" with argument "--file=teryt/places-dictionary.xml"
    Then places dictionary table in database should have following records
      | Type | Name       |
      | 01   | wieś       |
      | 02   | kolonia    |
      | 03   | przysiółek |