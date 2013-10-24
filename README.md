#Teryt Database Bundle

Teryt is Poland territorial division database available at http://www.stat.gov.pl/broker/access/prefile/listPreFiles.jspa as a xml files.
This bundle adds commands that download files from teryt page, parse xml files and insert data into database.

## Installation

Add to your composer.json file following line

```
"require": {
    "fsi/teryt-database-bundle": "1.0.*@dev"
}
```

Register bundles in AppKernel.php

```php
public function registerBundles()
{
    return array(
        // ...
        new FSi\Bundle\TerytDatabaseBundle\FSiTerytDbBundle(),
        new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
        // ...
    );
}
```

From now commands should be available in your application.

## Download xml files from teryt page

```
$ cd project
$ php app/console teryt:download:territorial-division
$ php app/console teryt:download:places-dictionary
$ php app/console teryt:download:places
$ php app/console teryt:download:streets
```

All above commands have additional argument ``--target``. This allows you to download files in other place than
``"%kernel.root_dir%/teryt/`` (default download target folder).

## Import data from xml files to database

First you need to unzip downloaded .zip files.

```
$ cd project/app/teryt
$ unzip territorial-division.zip
$ unzip places-dictionary.zip
$ unzip places.zip
$ unzip streets.zip
```

It is important to execute following commands in given order:

```
$ cd project
$ php app/console doctrine:schema:update --force
$ php app/console doctrine:fixtures:load
$ php app/console teryt:import:territorial-division teryt/TERC.xml
$ php app/console teryt:import:places-dictionary teryt/WMRODZ.xml
$ php app/console teryt:import:places teryt/SIMC.xml
$ php app/console teryt:import:streets teryt/ULIC.xml
```
