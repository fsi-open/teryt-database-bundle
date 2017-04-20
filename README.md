# Teryt Database Bundle

Teryt is Poland territorial division database available at http://www.stat.gov.pl/broker/access/prefile/listPreFiles.jspa as a set of XML files.
This bundle adds commands that download, parse and import the XML files from the Teryt page into your database.

## Installation

Add to your `composer.json` file following line

```json
"require": {
    "fsi/teryt-database-bundle": "2.0.*@dev"
}
```

Register bundles in `AppKernel.php`

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

## Download XML files from teryt page

```
$ cd project
$ php app/console teryt:download:territorial-division
$ php app/console teryt:download:places-dictionary
$ php app/console teryt:download:places
$ php app/console teryt:download:streets
```

All above commands have an additional argument ``--target``, that allows you to download files in a place other than
``"%kernel.root_dir%/teryt/`` (default download target folder).

## Import data from XML files to database

First you need to unzip the downloaded .zip files.

```bash
$ cd project/app/teryt
$ unzip territorial-division.zip
$ unzip places-dictionary.zip
$ unzip places.zip
$ unzip streets.zip
```

It is important to execute following commands in the given order:

```bash
$ cd project
$ php app/console doctrine:schema:update --force
$ php app/console doctrine:fixtures:load
$ php app/console teryt:import:territorial-division app/teryt/TERC.xml
$ php app/console teryt:import:places-dictionary app/teryt/WMRODZ.xml
$ php app/console teryt:import:places app/teryt/SIMC.xml
$ php app/console teryt:import:streets app/teryt/ULIC.xml
```
