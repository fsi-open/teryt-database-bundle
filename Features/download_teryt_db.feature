Feature: Download Teryt database xml files

  Scenario: Download teryt streets database
    When I successfully run console command "teryt:download:streets"
    Then "streets.zip" file should be downloaded into "teryt" folder

  Scenario: Download teryt places database
    When I successfully run console command "teryt:download:places"
    Then "places.zip" file should be downloaded into "teryt" folder

  Scenario: Download teryt places dictionary database
    When I successfully run console command "teryt:download:places-dictionary"
    Then "places-dictionary.zip" file should be downloaded into "teryt" folder

  Scenario: Download teryt territorial division database
    When I successfully run console command "teryt:download:territorial-division"
    Then "territorial-division.zip" file should be downloaded into "teryt" folder
