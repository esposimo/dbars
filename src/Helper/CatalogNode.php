<?php


namespace smn\dbars\Helper;


class CatalogNode extends \smn\hnp\Node
{

    protected array $unique_properties = ['name','type'];

    public static int $instanceCount = 0;

    public function __construct($value, array $properties = [])
    {
        parent::__construct($value, $properties);
        self::$instanceCount++;
        echo 'Costruito per ' .static::class .PHP_EOL;
    }

    public function __destruct()
    {
        self::$instanceCount--;
        //echo 'Distrutto per ' .static::class .PHP_EOL;
    }


}