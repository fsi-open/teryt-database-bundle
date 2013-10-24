Feature: Parse territorial division xml file and import data into database

  Background:
    Given I successfully run console command "doctrine:schema:create"
    And I successfully run console command "doctrine:fixtures:load"

  Scenario: Import province from xml file
    Given "territorial-division.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <teryt>
      <catalog name="TERC" type="all" date="2013-01-01">
        <row>
          <col name="WOJ">02</col>
          <col name="POW"/>
          <col name="GMI"/>
          <col name="RODZ"/>
          <col name="NAZWA">DOLNOŚLĄSKIE</col>
          <col name="NAZDOD">województwo</col>
          <col name="STAN_NA">2013-01-01</col>
        </row>
      </catalog>
    </teryt>
    """
    And there are no provinces in database
    When I successfully run console command "teryt:import:territorial-division" with argument "--file=teryt/territorial-division.xml"
    Then following province should exist in database
      | Code | Name         |
      | 02   | DOLNOŚLĄSKIE |

  Scenario: Update province during import
    Given "territorial-division.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <teryt>
      <catalog name="TERC" type="all" date="2013-01-01">
        <row>
          <col name="WOJ">02</col>
          <col name="POW"/>
          <col name="GMI"/>
          <col name="RODZ"/>
          <col name="NAZWA">Dolnośląskie</col>
          <col name="NAZDOD">województwo</col>
          <col name="STAN_NA">2013-01-01</col>
        </row>
      </catalog>
    </teryt>
    """
    And following province was already imported
      | Code | Name         |
      | 02   | DOLNOŚLĄSKIE |
    When I successfully run console command "teryt:import:territorial-division" with argument "--file=teryt/territorial-division.xml"
    Then following province should exist in database
      | Code | Name         |
      | 02   | Dolnośląskie |

  Scenario: Import district from xml file
    Given "territorial-division.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <teryt>
      <catalog name="TERC" type="all" date="2013-01-01">
        <row>
          <col name="WOJ">02</col>
          <col name="POW">01</col>
          <col name="GMI"/>
          <col name="RODZ"/>
          <col name="NAZWA">bolesławiecki</col>
          <col name="NAZDOD">powiat</col>
          <col name="STAN_NA">2013-01-01</col>
        </row>
      </catalog>
    </teryt>
    """
    And there are no districts in database
    And following province was already imported
      | Code | Name         |
      | 02   | DOLNOŚLĄSKIE |
    When I successfully run console command "teryt:import:territorial-division" with argument "--file=teryt/territorial-division.xml"
    Then following district should exist in database
      | Code | Name          | Province     |
      | 0201 | bolesławiecki | DOLNOŚLĄSKIE |

  Scenario: Update district during import
    Given "territorial-division.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <teryt>
      <catalog name="TERC" type="all" date="2013-01-01">
        <row>
          <col name="WOJ">02</col>
          <col name="POW">01</col>
          <col name="GMI"/>
          <col name="RODZ"/>
          <col name="NAZWA">bolesławiecki</col>
          <col name="NAZDOD">powiat</col>
          <col name="STAN_NA">2013-01-01</col>
        </row>
      </catalog>
    </teryt>
    """
    And there are no districts in database
    And following province was already imported
      | Code | Name         |
      | 02   | DOLNOŚLĄSKIE |
    And following district was already imported
      | Code | Name          | Province     |
      | 0201 | BOLESŁAWIECKI | DOLNOŚLĄSKIE |
    When I successfully run console command "teryt:import:territorial-division" with argument "--file=teryt/territorial-division.xml"
    Then following district should exist in database
      | Code | Name          | Province     |
      | 0201 | bolesławiecki | DOLNOŚLĄSKIE |

  Scenario: Import community from xml file
    Given "territorial-division.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <teryt>
      <catalog name="TERC" type="all" date="2013-01-01">
        <row>
          <col name="WOJ">02</col>
          <col name="POW">01</col>
          <col name="GMI">01</col>
          <col name="RODZ">1</col>
          <col name="NAZWA">Bolesławiec</col>
          <col name="NAZDOD">gmina miejska</col>
          <col name="STAN_NA">2013-01-01</col>
        </row>
      </catalog>
    </teryt>
    """
    And there are no communities in database
    And following province was already imported
      | Code | Name         |
      | 02   | DOLNOŚLĄSKIE |
    And following district was already imported
      | Code | Name          | Province     |
      | 0201 | bolesławiecki | DOLNOŚLĄSKIE |
    When I successfully run console command "teryt:import:territorial-division" with argument "--file=teryt/territorial-division.xml"
    Then following communities should exist in database
      | Code    | Name        | District      | Community type |
      | 0201011 | Bolesławiec | bolesławiecki | gmina miejska  |

  Scenario: Update community during import
    Given "territorial-division.xml" file have following content:
    """
    <?xml version="1.0" encoding="UTF-8"?>
    <teryt>
      <catalog name="TERC" type="all" date="2013-01-01">
        <row>
          <col name="WOJ">02</col>
          <col name="POW">01</col>
          <col name="GMI">01</col>
          <col name="RODZ">1</col>
          <col name="NAZWA">BOLESŁAWIEC</col>
          <col name="NAZDOD">gmina miejska</col>
          <col name="STAN_NA">2013-01-01</col>
        </row>
      </catalog>
    </teryt>
    """
    And there are no communities in database
    And following province was already imported
      | Code | Name         |
      | 02   | DOLNOŚLĄSKIE |
    And following district was already imported
      | Code | Name          | Province     |
      | 0201 | bolesławiecki | DOLNOŚLĄSKIE |
    And following community was already imported
      | Code    | Name        | District      | Community type |
      | 0201011 | BOLESŁAWIEC | bolesławiecki | gmina wiejska  |
    When I successfully run console command "teryt:import:territorial-division" with argument "--file=teryt/territorial-division.xml"
    Then following communities should exist in database
      | Code    | Name        | District      | Community type |
      | 0201011 | BOLESŁAWIEC | bolesławiecki | gmina miejska  |

  Scenario: Attempting to import data from non existing xml file
    When I unsuccessfully run console command "teryt:import:territorial-division" with argument "--file=teryt/territorial-division.xml"
    Then I should see "File teryt/territorial-division.xml does not exist" console output