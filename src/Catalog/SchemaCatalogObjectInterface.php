<?php


namespace smn\dbars\Catalog;


interface SchemaCatalogObjectInterface extends CatalogObjectInterface
{


    public function addTableInstance(TableCatalogObjectInterface $table);

    public function addTable(string $table_name);

    public function getTable(string $table_name) : ?TableCatalogObjectInterface;

    public function hasTable(string $table_name) : bool;

    public function hasTableInstance(TableCatalogObjectInterface $table) : bool;

    public function removeTable(string $table_name);

    public function removeTableInstance(TableCatalogObjectInterface $table);

}