# Full Concrete Table Inheritance Behavior

## Presentation

`Concrete Table Inheritance` implemented in Doctrine copies only structure of parent table. The replication data is not supported. This behavior allows to have a full copy. 

## Usage

# Schema Definition

To implement it, the extended table need to have a "descendant_column". The inherited tables must have the `Concrete Table Inheritance` option setted.

In the following example, the `article` and `video` tables use this behavior to inherit the columns of their parent table (`content`):

    Content:
      tableName: content
      columns:
        title:
          type: varchar(50)
        descendant_class:
          type: varchar(50)

    Article:
      actAs:
        DataInheritance: ~
      tableName: article
      inheritance:
        extends: Content
        type: concrete
      columns:
        body:
          type: varchar(100)

    Video:
      actAs:
        DataInheritance: ~
      tableName: article
      inheritance:
        extends: Content
        type: concrete
      columns:
        resource_link:
          type: varchar(100)
		  
# Data replication

Every time you save an `Article` or a `Video` object, a copy of the `title` and `category_id` columns is saved in a `Content` object.

## Licence

MIT Licence

## Changelog

 * version 0.1 - 2010-11-31, heristop