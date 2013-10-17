Feature: Download Teryt database xml files

  Background:
    Given Urls to Teryt database files are available at "http://www.stat.gov.pl/broker/access/prefile/listPreFiles.jspa"

  Scenario: Download teryt streets database
    When I run console command "teryt:download:streets"
    Then "streets.zip" file should be downloaded into "Fixtures/Project/app/teryt" folder
    And I should see "100/100 [============================] 100%" output at console

  Scenario: Download teryt places database
    When I run console command "teryt:download:places"
    Then "places.zip" file should be downloaded into "Fixtures/Project/app/teryt" folder
    And I should see "100/100 [============================] 100%" output at console

  Scenario: Download teryt places dictionary database
    When I run console command "teryt:download:places-dictionary"
    Then "places-dictionary.zip" file should be downloaded into "Fixtures/Project/app/teryt" folder
    And I should see "100/100 [============================] 100%" output at console

  Scenario: Download teryt territorial division database
    When I run console command "teryt:download:territorial-division"
    Then "territorial-division.zip" file should be downloaded into "Fixtures/Project/app/teryt" folder
    And I should see "100/100 [============================] 100%" output at console