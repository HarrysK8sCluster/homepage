<?php

namespace pkremer\WebFrontend;


use pkremer\WebFrontend\Element\ElementInterface;
use pkremer\WebFrontend\Property\Property;
use pkremer\WebFrontend\Property\PropertyMap;
use RuntimeException;

class ElementFactory
{

    public function create(array $node): ElementInterface
    {
        $element = $this->createElementByType($node['type']);

        $this->mapProperties($element, $node['attributes'] ?? []);
        $this->mapChildren($element, $node['children'] ?? []);

        return $element;
    }

    private function createElementByType(string $type): ElementInterface
    {
        $className = __Namespace__ . '\\Element\\' . ucfirst($type) . 'Element';
        if (!class_exists($className)) {
            throw new RuntimeException("Unknown element type: '{$type}'");

        }
        return new $className();
    }

    private function mapProperties(ElementInterface $element, array $attributes): void
    {
        foreach ($attributes as $name => $value) {
            $element->addProperty(
                new Property($name, $this->mapPropertyValue($value))
            );
        }
    }

    private function mapPropertyValue(mixed $value): null|string|int|float|bool|PropertyMap
    {
        if (!is_array($value)) {
            return $value;
        }

        $map = new PropertyMap();

        foreach ($value as $key => $val) {
            $map->addProperty(
                new Property($key, $this->mapPropertyValue($val))
            );
        }

        return $map;
    }

    private function mapChildren(ElementInterface $parent, array $children): void
    {
        foreach ($children as $childNode) {
            $parent->addElement(
                $this->create($childNode)
            );
        }
    }


}