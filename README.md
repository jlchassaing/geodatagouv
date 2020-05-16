# geodatagouv

Geo Data Gouv project

This bundle has two usecases :

- help import a csv location list and give geocoding functionnality based on [geo.api.gouv.fr](https://geo.api.gouv.fr/adresse)
- give a react search form with auto completion also based on geo data gouv

Contents will be imported in eZ Platform contents indexed by solr to help location proximiy search

## installation

This bundle is based on eZ Platform bundle jlchassaing/ezgeodatagouv

This bundle is build for symfony5

This bundle uses [Code Rhapsodie Dataflow Bundle](https://github.com/code-rhapsodie/dataflow-bundle)

composer require jlchassaing/geodatagouv

in bundles.php add

```php
<?php
// config/bundles.php

    return = [
        // ...
        CodeRhapsodie\DataflowBundle\CodeRhapsodieDataflowBundle::class => ['All' => true],
        GeoDataGouvBundle\GeoDataGouvBundle::class => ['All' => true],
        // ...
    ];
}
```

## how to import data

### Set a resource

Resouces are set in a config.yml file

```yaml
ez_geo_data_gouv:
  resources:
    resource_key_name:
      do_geocoding: true|false
      entity: entity_name # dest entity to import data
      language: import_language # should be eng-GB or fre-FR or any other language default is eng-GB
      id_key: csv_uniq_id_key
      name: csv_field_used_for_name
      address:
        longitude: longitude
        latitude: latitude
        address: csv_field_used_for_full_address
      fields: # if using a custom class identifier cet specific fields
        url:
          datatype: ezurl
          value: url
      geocoding_fields:
        columns:
          - commune # csv field to send for geo coding in geo.api.gouv
          - voie
        postcode: code_postal
        citycode: code_commune_insee
        result_columns:
          - result_label # set specific fields for result if not set default are longitude and latidue
```

The settings help set how the csv fields will be used.
The ressoure name must be passed in option to the import script

### running import

The import script alias is 'dtgi'

The options to provide are :

- the csv_source path where the csv source is can be online (for now the file must be semicolon field seperated and quotation marks)
- resource name

```shell script
bin/console code-rhapsodie:dataflow:execute  dtgi  '{"csv-source":"<csvpath>","resource":"<resource_name>"}'
```

## Proximity search

The Rest api returns proximity points

there are 4 ways to call the perform the request :

- /geo_data_gouv/search/{longitude}/{latitude}
- /geo_data_gouv/search/{distance}/{longitude}/{latitude}
- /geo_data_gouv/search/{type}/{longitude}/{latitude}
- /geo_data_gouv/search/{type}/{distance}/{longitude}/{latitude}

The default distance is 5

The query will return a maximum of 5 answers

## Extending to add a custom import manager

The DataGouvImportDataFlowType class can be extended

This could be necessary to execute some changes on the csv data before
creating content.

Create a custom class that extends DataGouvImportDataFlowType and add methods :

```php
...
    public function getLabel(): string
    {
        return "My DataFlow";
    }

    public function getAliases(): iterable
    {
        return ['mydf'];
    }

    protected function addFilterTask(DataflowBuilder $builder)
    {
        $builder->addStep(function ($data){
            /** add here your code and return data or null to invalidate line */
            return $data;
        });
    }
...
```

Once the class is created declare it as a service and add tag _coderhapsodie.dataflow.type_

```yaml
AppBundle\DataFlow\MyDataFlowType:
  tags:
    - { name: coderhapsodie.dataflow.type }
```
