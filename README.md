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
interface CmsInterface
class Cms implements CmsInterface
class Command
class CommandHandler
class CreateInstanceCommand
class CreateInstanceCommandHandler
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

Relation "0..1" -- Link

Attribute <|-- ImageAttribute

Attribute "1" - "*" Value
Instance "1" *.. "*" Value
Instance "1" *.. "1" Publication

Instance "1" *-- "*"  Link : children

ImageAttribute -- MediaAdapterInterface

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

interface CmsInterface {
getClass($key): Clazz
createInstance(Instance $instance):void
createInstanceFromArray(array $array):Instance
getInstanceByID(string $id): Instance
getAllInstances(): ?array
getStructure(): Structure
filterInstance(array $instances, function $filterFunction): ?array
}

class Cms {
Storage $storage
InstanceRepositoryInterface $instanceRepository
}

class Command {
__construct(?array $array): Command
getData(): ?array
validate(?array $array): void
}

class CommandHandler {
Cms $cms
__construct(CmsInterface $cms): CommandHandler
__invoke(Command $command): void
Cms(): CmsInterface
}

Command <|-- CreateInstanceCommand
CommandHandler <|-- CreateInstanceCommandHandler
@enduml
```

Estimations:
Hector: 2 meses Core , 1,5 meses Backoffice
Christian: 2 meses Core , 3 meses Backoffice (amb alguna millora i canvis maquetació)
Alvaro: 2,5 meses Core , 2 meses Backoffice
Agus: 3 meses Core , 3 meses Backoffice (amb alguna millora)

4,5 - 5 meses total

TBD:
- max length a nivell d'attribute
- refactoritzar carrega yaml
- components a nivell d'attribute i class: edit=xxx list=xxx
- Valors unique per class e idioma (com niceurl)
- Attributs orderables i/o 

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
