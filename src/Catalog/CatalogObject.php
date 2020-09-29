<?php


namespace smn\dbars\Catalog;


use Exception;
use smn\phs\PlaceHolderSystem;
use smn\phs\PlaceHolderSystemException;

class CatalogObject implements CatalogObjectInterface
{

    const CHILD_TABLE = 'table';
    const CHILD_VIEW = 'view';
    const CHILD_COLUMN = 'column';




    /**
     * PlaceHolderSystem class instance for returning name of the catalog object in a format useful for queries.
     * Es. a column object may render as dbname.table.column_name
     * @var PlaceHolderSystem
     */
    protected PlaceHolderSystem $phs;

    /**
     * Contains the object name
     * @var string
     */
    protected string $name;

    /**
     * Contains the object type
     * @var string
     */
    protected string $type;


    /**
     * Represent the parent object of the instance
     * @var CatalogInterface|null
     */
    protected ?CatalogInterface $parent = null;

    /**
     * List of the children object. For example, a schema object contains many type of children (table, views, trigger, etc)
     * @var CatalogInterface[][]
     */
    protected array $children = [];


    /**
     * CatalogObject constructor. Name and Type are mandatory for a generic catalog object
     * @param string $name The object name
     * @param string $type Type of the object
     * @throws \ReflectionException Throw on PlaceHolderSystem
     * @throws \smn\phs\PlaceHolderSystemException Throw on PlaceHolderSystem.
     */
    public function __construct(string $name, string $type)
    {
        $this->phs = new PlaceHolderSystem();
        $this->setName($name);
        $this->setType($type);

        $this->phs->setPattern('{object_name}');
        $this->phs->addPlaceHolder('object_name', function (CatalogObjectInterface $instance) {
            return $instance->getName();
        }, [$this]);

    }

    /**
     * Setter method for name
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Getter method for name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Setter method for type
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Getter method for type
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }


    /**
     * Inherit from PrintableInterface. This is for print name of the catalogObject
     * @return string
     */
    public function toString(): string
    {
        return $this->phs->render();
    }

    /**
     * Add a new child to the children of this catalog object. If child of the type $type with name $name already exists
     * throw a Exception
     * @param string $name Name of the catalog object to add
     * @param string $type Type of the catalog object to add
     * @throws PlaceHolderSystemException
     * @throws \ReflectionException
     */
    public function addChild(string $name, string $type)
    {
        if ($this->hasChild($name, $type)) {
            throw new Exception('Child already exists');
        }
        $child = new self($name, $type);
        $this->children[$type][$name] = $child;
        $child->parent = $this;
    }

    /**
     * Add a child by the instance. If a child with the same name/type of $catalogObject already exists, throw an Exception.
     * When $catalogObject are added, it loss parent reference and this instance become the new parent reference
     * @param CatalogInterface $catalogObject
     * @throws Exception
     */
    public function addChildInstance(CatalogInterface $catalogObject)
    {
        $name = $catalogObject->getName();
        $type = $catalogObject->getType();
        if ($this->hasChild($name, $type)) {
            throw new Exception('Child already exists: addChildInstance::method');
        }
        $this->children[$type][$name] = $catalogObject;
        $catalogObject->setParentInstance($this);
    }


    /**
     * Return true/false if a child with name/type exists
     * @param string $name
     * @param string $type
     * @return bool
     */
    public function hasChild(string $name, string $type): bool
    {
        if (!$this->hasChildType($type)) {
            return false;
        }
        return array_key_exists($name, $this->children[$type]);
    }

    /**
     * Return true/false if a catalog object is a child of this instance.
     * @param CatalogInterface $catalog
     * @return bool
     */
    public function hasChildInstance(CatalogInterface $catalog): bool
    {

        $type = $catalog->getType();
        if (!$this->hasChildType($type)) {
            return false;
        }
        return in_array($catalog, $this->children[$type], true);
    }

    /**
     * Return true/false if catalog object have at least one child of type $type
     * @param string $type
     * @return bool
     */
    public function hasChildType(string $type): bool
    {
        return (array_key_exists($type, $this->children) && (count($this->children[$type]) > 0));
    }

    /**
     * Return the child catalog object with name $name and type $type.
     * If no object found, return null
     * @param string $name
     * @param string $type
     * @return CatalogInterface|null
     */
    public function getChild(string $name, string $type): ?CatalogInterface
    {
        if (!$this->hasChildType($type)) {
            return null;
        }
        return array_key_exists($name, $this->children[$type]) ? $this->children[$type][$name] : null;
    }


    /**
     * Return a multidimensional array where indexes are type of the catalog object and every index contain an array with all children of those type
     * If type is specified, return a single array with all children of this type. If no children of $type exists, return an empty array
     * @param string $type
     * @return array|CatalogInterface[]|CatalogInterface[][]
     */
    public function getChildren(string $type = ''): array
    {
        if ($type == '') {
            return $this->children;
        }
        if ($this->hasChildType($type)) {
            return $this->children[$type];
        }
        return [];

    }

    /**
     * Remove a child with $name and $type. When a child are removed, it loss parent reference.
     * @param string $name
     * @param string $type
     */
    public function removeChild(string $name, string $type)
    {
        if (($this->hasChildType($type) && (array_key_exists($name, $this->children[$type])))) {
            $child = $this->children[$type][$name];
            unset($this->children[$type][$name]);
            if (count($this->children[$type]) == 0) {
                unset($this->children[$type]);
            }
            $child->removeParent();
        }
    }

    /**
     * Remove a child by instance. Remove are performed only if $catalogObject's parent matches to the instance.
     * @param CatalogInterface $catalogObject
     * @throws Exception
     */
    public function removeChildInstance(CatalogInterface $catalogObject)
    {
        if ($this->hasChildInstance($catalogObject)) {
            unset($this->children[$catalogObject->getType()][$catalogObject->getName()]);
            if (count($this->children[$catalogObject->getType()]) == 0) {
                unset($this->children[$catalogObject->getType()]);
            }
            $catalogObject->removeParent();
        } else {
            throw new Exception('Parent is not the same');
        }
    }

    /**
     * Configure a new parent for this catalog object. If a parent already exists, it will be overwritten with new parent, and the
     * old parent remove child reference of this instance
     * @param string $name Name of the parent
     * @param string $type Type of the object parent
     * @throws PlaceHolderSystemException
     * @throws \ReflectionException
     */
    public function setParent(string $name, string $type)
    {
        $parent = new self($name, $type);
        if ($this->parent !== null) {
            $this->parent->removeChildInstance($this);
        }
        $this->parent = $parent;
        $parent->addChildInstance($this);
    }

    /**
     * Configure a new parent for this catalog object by the instance. Like a setParent() method, if a parent already exists, it will be overwritten with new parent
     * @param CatalogInterface $catalogObject
     * @throws Exception
     */
    public function setParentInstance(CatalogInterface $catalogObject)
    {
        if ($this->parent === $catalogObject) {
            return;
        }
        try {
            $this->parent = $catalogObject;
            if (!$catalogObject->hasChildInstance($this)) {
                $catalogObject->addChildInstance($this);

            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * Return the catalog object parent of the instance. If no parent are configured, return null
     * @return CatalogInterface|null
     */
    public function getParent(): ?CatalogInterface
    {
        return $this->parent;
    }

    /**
     * Remove parent catalog object of the instance. When a parent object are removed, it loss reference with this child
     */
    public function removeParent()
    {
        if ($this->parent->hasChild($this->getName(), $this->getType())) {
            $this->parent->removeChildInstance($this);
        }
        $this->parent = null;
    }
}