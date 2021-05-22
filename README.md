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
class BaseClass
class BaseAttribute
class BaseRelation
class ImageAttribute
class CmsStructure
}

package "Data"
{
class BaseInstance
class BaseRelationInstances
class TranslatableKey
}

package "Values" {
class BaseValue
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
CmsStructure "1" *-- "*" BaseClass

BaseClass -- BaseInstance
BaseClass "1" *-- "*" BaseAttribute
BaseClass "1" *-- "*"   BaseRelation : children
BaseAttribute "1" *-- "*subattributes" BaseAttribute

BaseRelation "0..1" -- BaseRelationInstances

BaseAttribute <|-- ImageAttribute

BaseAttribute "1" - "*" BaseValue
BaseInstance "1" *.. "*" BaseValue
BaseInstance "1" *-- "*"  BaseRelationInstances : children

ImageAttribute -- MediaAdapterInterface

TranslationsStorageAdapterInterface "1" *.. "*" TranslatableKey

BaseValue *-- BaseValue

BaseValue <|-- ImageValue
BaseValue <|-- NumberValue
BaseValue <|-- ReverseValue

ImageAttribute -- ImageValue

class BaseInstance {
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
getClass($key): BaseClass
}
@enduml
```