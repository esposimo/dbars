<?php


namespace smn\dbars\Catalog;


interface CatalogInterface
{

    public function setName(string $name): void;


    public function getName(): string;


    public function setType(string $type): void;


    public function getType(): string;


    public function addChild(string $name, string $type);

    public function addChildInstance(CatalogInterface $catalogObject);

    public function hasChild(string $name, string $type): bool;

    public function hasChildType(string $type): bool;

    public function getChild(string $name, string $type): ?CatalogInterface;

    public function getChildren(string $type = ''): array;

    public function removeChild(string $name, string $type);

    public function removeChildInstance(CatalogInterface $catalogObject);

    public function setParent(string $name, string $type);

    public function setParentInstance(CatalogInterface $catalogObject);

    public function getParent(): ?CatalogInterface;

    public function removeParent();


    public function hasChildInstance(CatalogInterface $catalog): bool;


}