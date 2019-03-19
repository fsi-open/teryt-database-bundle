# Teryt Database Bundle

Teryt is Poland territorial division database available at http://eteryt.stat.gov.pl
This bundle adds commands that download files from teryt API, parse xml files and insert data into database.

## Installation

Add to your `composer.json` file following line

```json
"require": {
    "fsi/teryt-database-bundle": "^3.0"
}
```

Register bundles in `AppKernel.php`

```php
public function registerBundles()
{
    return [
        // ...
        new FSi\Bundle\TerytDatabaseBundle\FSiTerytDbBundle(),
        new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
        // ...
    ];
}
```

Add following configuration:

```yaml
fsi_teryt_db:
    api:
        url: "https://uslugaterytws1.stat.gov.pl/wsdl/terytws1.wsdl"
        username: "<your username>"
        password: "<your password>"
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
