<?php


namespace vektah\parser_combinator\language\css\selectors;

use vektah\parser_combinator\language\css\CssObject;

class AttributeSelector extends Selector
{
    /** @var string */
    private $namespace;

    /** @var string */
    private $name;

    /** @var string */
    private $comparator;

    /** @var string */
    private $value;

    public function __construct($name, $comparator = null, $value = null, $namespace = null)
    {
        $this->comparator = $comparator;
        $this->name = $name;
        $this->namespace = $namespace;
        $this->value = $value;
    }

    /**
     * @param string $comparator
     */
    public function setComparator($comparator)
    {
        $this->comparator = $comparator;
    }

    /**
     * @return string
     */
    public function getComparator()
    {
        return $this->comparator;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function toCss() {
        $namespace = $this->namespace ? '|' . $this->namespace : '';
        $value = $this->value ? "'$this->value'" : '';
        return "[{$namespace}{$this->name}{$this->comparator}{$value}]";
    }

    public function __toString() {

        $string = "Attribute($this->name";
        if ($this->comparator && $this->value) {
            $string .= "$this->comparator'$this->value'";
        }

        if ($this->namespace) {
            $string .= ", $this->namespace";
        }

        return $string . ')';
    }

    /**
     * @return CssObject
     */
    public function define()
    {
        $object = new CssObject();

        if ($this->comparator && $this->value) {
            $object->attributes[$this->name] = $this->value;
        } else {
            $object->attributes[$this->name] = true;
        }

        return $object;
    }

    public function matchesObject(CssObject $object)
    {
        if (!isset($object->attributes[$this->name])) {
            return false;
        }
        if ($this->comparator && $this->value) {

            switch ($this->comparator) {
                case '=':
                    return $object->attributes[$this->name] === $this->value;
                case '~=':
                    foreach (preg_split('/\s+/', $object->attributes[$this->name]) as $value) {
                        if ($value === $this->value) {
                            return true;
                        }
                    }
                    return false;
                case '|=':
                    if ($object->attributes[$this->name] === $this->value) {
                        return true;
                    }

                    return substr($object->attributes[$this->name], 0, strlen($this->value) + 1) === "$this->value-";
            }
        }

        return true;
    }
}
