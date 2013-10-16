Feature: Download Teryt database xml files

  Scenario: Download teryt streets database
    Given Urls to Teryt database files are available in "http://www.stat.gov.pl/broker/access/prefile/listPreFiles.jspa"
    When I run console command "teryt:download:streets"
    Then "streets.zip" file should be downloaded into "Fixtures/Project/app/teryt" folder