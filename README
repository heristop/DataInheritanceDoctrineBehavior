#Full Concrete Table Inheritance Behavior#

The Doctrine Concrete Inheritance copies only structure of parent table. This behavior allows to have a data replication.

To implement it, the extended table need to have a "descendant_column", and the inherited tables need to have the concrete table inheritance setted.

In the following example, the `article` and `video` tables use this behavior to inherit the columns and foreign keys of their parent table, `content`:

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
