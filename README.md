# editora-core

It's all about classes, instances, attributes and of course ... relations!

# Test

composer test tests
composer testwindows tests

# UML
```
@startuml
title Editora - Class Diagram


package "CmsStructure"
{
class Cms
class Clas
class Attribute
class Relation
class ImageAttribute
class CmsStructure
}

package "CmsData"
{
class Instance
class RelationInstances
class TranslatableKey
class Value
class ImageValue
class NumberValue
class ReverseValue
class PublishingInfo
}

package "Contracts" {
class MediaAdapterInterface
class TranslationsStorageAdapterInterface
class InstanceRepositoryInterface
}

package "Infrastructure" #DDDDDD {
class S3MediaAdapter implements MediaAdapterInterface
class LocalStorageMediaAdapter implements MediaAdapterInterface
class ArrayTranslationsStorageManager implements TranslationsStorageAdapterInterface
class MySQLTranslationsStorageManager implements TranslationsStorageAdapterInterface
class ArrayInstanceRepository implements InstanceRepositoryInterface
class EloquentInstanceRepository implements InstanceRepositoryInterface
}


Cms o-- CmsStructure
Cms o-- InstanceRepositoryInterface
CmsStructure "1" *-- "*" Clas

Clas -- Instance
Clas "1" *-- "*" Attribute
Clas "1" *-- "*"   Relation : children
Attribute "1" *-- "*subattributes" Attribute

Relation "0..1" -- RelationInstances

Attribute <|-- ImageAttribute

Attribute "1" - "*" Value
Instance "1" *.. "*" Value
Instance "1" *.. "1" PublishingInfo

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
  String externalID
  createFromValuesArray(class,values,...)
  createFromJson(class,json,...
  Json getJson(lang)
}

class PublishingInfo {
  Date startPublishingDate
  Date endPublishingDate
  Enum status
  fromArray()
  toArray()
  validateStatus()
  isPublished()
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

TBD:
- Guardar relationinstances com IDs addRelation ($child, ABOVE|BELOW, $id)
- Passar de jsons a Structure
- Editora Database amb yaml
- components a nivell d'attribute i class: edit=xxx list=xxx

Estructura:
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
