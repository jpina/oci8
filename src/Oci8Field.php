<?php

namespace Jpina\Oci8;

class Oci8Field implements Oci8FieldInterface
{

    private $name;
    private $precision;
    private $rawType;
    private $scale;
    private $size;
    private $type;
    private $value;


    public function __construct($name, $value, $size, $precision, $scale, $type, $rawType)
    {
        $this->name      = $name;
        $this->precision = $precision;
        $this->rawType   = $rawType;
        $this->scale     = $scale;
        $this->size      = $size;
        $this->type      = $type;
        $this->value     = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrecision()
    {
        return $this->precision;
    }

    public function getRawType()
    {
        return $this->rawType;
    }

    public function getScale()
    {
        return $this->scale;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function isNull()
    {
        return $this->getValue() === null;
    }
}
