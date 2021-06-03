# editora-core

It's all about classes, instances, attributes and of course ... relations!

# Test

composer test tests
composer testwindows tests

# UML
```
@startuml
title Editora - Class Diagram

package "Application"
{
class Cms
}

package "Structure"
{
class Clazz
class Attribute
class Relation
class ImageAttribute
class Structure
}

package "Data"
{
class Instance
class Link
class Ztatic
class Value
class ImageValue
class NumberValue
class ReverseValue
class Publication
}

package "Contracts" {
class MediaAdapterInterface
class InstanceRepositoryInterface
}


package "Infrastructure\Media" #DDDDDD {
class S3MediaAdapter implements MediaAdapterInterface
class LocalStorageMediaAdapter implements MediaAdapterInterface
}

package "Infrastructure\Memory" #DDDDDD {
class InstanceRepository implements InstanceRepositoryInterface
}


Cms o-- Structure
Cms o-- InstanceRepositoryInterface
Structure "1" *-- "*" Clazz

Clazz -- Instance
Clazz "1" *-- "*" Attribute
Clazz "1" *-- "*"   Relation : children
Attribute "1" *-- "*subattributes" Attribute

Relation "0..1" -- Link

Attribute <|-- ImageAttribute

Attribute "1" - "*" Value
Instance "1" *.. "*" Value
Instance "1" *.. "1" Publication

Instance "1" *-- "*"  Link : children

ImageAttribute -- MediaAdapterInterface

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

class Publication {
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
*- Guardar Link com IDs addRelation ($child, ABOVE|BELOW, $id)
*- separar en StructureTransformers el codi que ara esta a Structure
*- DSL fitxer input de structure
- Passar de jsons a classes d'Structure
*- Editora Database amb yaml
- components a nivell d'attribute i class: edit=xxx list=xxx
- Valors unique per class e idioma (com niceurl)
- Attributs orderables i/o indexables

Estructura:
src
  - Application
    - use cases
  - Domain
    - Structure
      aqui les classes
      - Contracts (Ports)
      - Services (SubCasos d'us)
    - Data
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
