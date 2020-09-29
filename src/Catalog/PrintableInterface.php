<?php


namespace smn\dbars\Catalog;


interface PrintableInterface
{

    /**
     * Return the printable name of object
     * @return string
     */
    public function toString() : string;

}