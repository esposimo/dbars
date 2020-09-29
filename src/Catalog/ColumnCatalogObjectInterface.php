<?php


namespace smn\dbars\Catalog;


interface ColumnCatalogObjectInterface extends CatalogObjectInterface
{

    public function setTable(string $table_name);

    public function setTableInstance(TableCatalogObjectInterface $table);

    public function getTable() : ?TableCatalogObjectInterface;

}