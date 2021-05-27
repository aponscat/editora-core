# editora-core

It's all about classes, instances, attributes and of course ... relations!

# Test

composer test tests
composer testwindows tests

# UML
```
@startuml
title Editora - Class Diagram

package "Facades"
{
class Cms
}

package "Structure"
{
class Class
class Attribute
class Relation
class ImageAttribute
class CmsStructure
}

package "Data"
{
class Instance
class RelationInstances
class TranslatableKey
}

package "Values" {
class Value
class ImageValue
class NumberValue
class ReverseValue
}

package "Ports" {
class MediaAdapterInterface
class TranslationsStorageAdapterInterface
class CmsStorageInstanceInterface
}

package "Adapters" #DDDDDD {
class ArrayMediaAdapter implements MediaAdapterInterface
class S3MediaAdapter implements MediaAdapterInterface
class MySQLMediaAdapter implements MediaAdapterInterface
class LocalStorageMediaAdapter implements MediaAdapterInterface
class ArrayTranslationsStorageManager implements TranslationsStorageAdapterInterface
class MySQLTranslationsStorageManager implements TranslationsStorageAdapterInterface
class ArrayStorageAdapter implements CmsStorageInstanceInterface
}

package "Controllers"
{
class BackOfficeController
}

BackOfficeController -- Cms
Cms o-- CmsStructure
Cms o-- CmsStorageInstanceInterface
CmsStructure "1" *-- "*" Class

Class -- Instance
Class "1" *-- "*" Attribute
Class "1" *-- "*"   Relation : children
Attribute "1" *-- "*subattributes" Attribute

Relation "0..1" -- RelationInstances

Attribute <|-- ImageAttribute

Attribute "1" - "*" Value
Instance "1" *.. "*" Value
Instance "1" *-- "*"  RelationInstances : children

ImageAttribute -- MediaAdapterInterface

TranslationsStorageAdapterInterface "1" *.. "*" TranslatableKey

Value *-- Value

Value <|-- ImageValue
Value <|-- NumberValue
Value <|-- ReverseValue

ImageAttribute -- ImageValue

class Instance {
  String key
  Date startPublishingDate
  Date endPublishingDate
  Enum status
  String externalID
  createFromValuesArray(class,values,...)
  createFromJson(class,json,...
  Json getJson(lang)
}

class BackOfficeController {
  listClasses()
  newInstance()
  getInstance($id)
  saveInstance()
}

class Cms {
getClass($key): Clas
putJSONInstance(string $json): string
getInstanceByID(string $id): Instance
getAllInstances()
}
@enduml
```

Estimations:
Hector: 2 meses Core , 1,5 meses Backoffice
Christian: 2 meses Core , 3 meses Backoffice (amb alguna millora i canvis maquetació)
Alvaro: 2,5 meses Core , 2 meses Backoffice
Agus: 3 meses Core , 3 meses Backoffice (amb alguna millora)

4,5 - 5 meses total



src
  - Application
    - use cases
  - Domain
    - CmsStructure
      aqui les classes
      - Contracts (Ports)
      - Services (SubCasos d'us)
    - CmsData
      - aqui les classes
      - Contracts (Ports)
        InstanceRepositoryInterface
          create($instance):void
          read($id):$instance
          update($instance):void
          delete($instance):void
      - Services (Subcasos d'us)
  - Infrastructure
    - Persistence
      - Memory
        InstanceRepository implements InstanceRepositoryInterface	
      - MongoDB
        InstanceRepository implements InstanceRepositoryInterface
      - Eloquent
        InstanceRepository implements InstanceRepositoryInterface
        ValuesRepository
        RelationsRepository
      - Doctrine
