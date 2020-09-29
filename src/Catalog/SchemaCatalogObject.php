<?php


namespace smn\dbars\Catalog;


class SchemaCatalogObject extends CatalogObject implements SchemaCatalogObjectInterface
{

    public function __construct(string $name)
    {
        parent::__construct($name, 'schema');
        $this->phs->setPattern('{schema_name}');
        $this->phs->addPlaceHolder('schema_name', function (SchemaCatalogObjectInterface $instance) {
            return $instance->getName();
        }, [$this]);
    }

    public function addTableInstance(TableCatalogObjectInterface $table)
    {
        $this->addChildInstance($table);
    }

    public function addTable(string $table_name)
    {
        $this->addChild($table_name, self::CHILD_TABLE);
    }

    public function getTable(string $table_name): ?TableCatalogObjectInterface
    {
        return $this->getChild($table_name, self::CHILD_TABLE);
    }

    public function hasTable(string $table_name): bool
    {
        return $this->hasChild($table_name, self::CHILD_TABLE);
    }

    public function hasTableInstance(TableCatalogObjectInterface $table): bool
    {
        return $this->hasTableInstance($table);
    }

    public function removeTable(string $table_name)
    {
        $this->removeChild($table_name, self::CHILD_TABLE);
    }

    public function removeTableInstance(TableCatalogObjectInterface $table)
    {
        $this->removeChildInstance($table);
    }
}