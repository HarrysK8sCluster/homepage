<?php

namespace pkremer\WebFrontend\Property;

class PropertyMap implements PropertyMapInterface
{
    public array $properties = [];

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getProperty(string $name): Property
    {
        return $this->properties[$name];
    }

    public function addProperty(Property $property): void
    {
        $this->properties[$property->getName()] = $property;
    }

}