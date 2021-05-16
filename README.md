# editora-core

It's all about classes, instances, attributes and of course ... relations!

# Test

composer test tests
composer testwindows tests

# UML
```
@startuml
title Editora - Class Diagram

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

package "Adapters" {
class ArrayMediaAdapter implements MediaAdapterInterface
class ArrayTranslationsStorageManager implements TranslationsStorageAdapterInterface
}

CmsStructure "1" *-- "*" BaseAttribute
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
@enduml
```