<?php


namespace smn\dbars\Catalog;


interface TableCatalogObjectInterface extends CatalogObjectInterface
{

    public function addColumn(string $column_name);

    public function addColumnInstance(ColumnCatalogObjectInterface $column);

    public function getColumn(string $column) : ?ColumnCatalogObjectInterface;

    public function hasColumn(string $column) : bool;

    public function hasColumnInstance(ColumnCatalogObjectInterface $column) : bool;

    public function removeColumn(string $column_name);

    public function removeColumnInstance(ColumnCatalogObjectInterface $column);

    public function setSchema(SchemaCatalogObjectInterface $schema);

    public function getSchema() : ?SchemaCatalogObjectInterface;

    public function removeSchema();


}