<?php

namespace pkremer\WebFrontend\Validation;

use pkremer\WebFrontend\Element\AbstractElement;
use pkremer\WebFrontend\Property\Property;
use pkremer\WebFrontend\Schema\HasPropertySchema;
use RuntimeException;

final class ElementNormalizer
{
    public function normalize(AbstractElement $element): void
    {
        if ($element instanceof HasPropertySchema) {
            $this->applySchema($element);
        }

        foreach ($element->getElements() as $child) {
            $this->normalize($child);
        }
    }

    private function applySchema(AbstractElement $element): void
    {
        $schema = [];

        foreach ($element::schema() as $propertySchema) {
            $schema[$propertySchema->name] = $propertySchema;
        }

        $properties = $element->getProperties()->getProperties();

        // A) Unbekannte Properties
        foreach ($properties as $name => $_) {
            if (!isset($schema[$name])) {
                throw new RuntimeException(
                    "Unknown property '{$name}' in " . $element::class
                );
            }
        }

        // B) Defaults + Required
        foreach ($schema as $name => $propertySchema) {
            if (!isset($properties[$name])) {
                if ($propertySchema->required && $propertySchema->default === null) {
                    throw new RuntimeException(
                        "Missing required property '{$name}' in " . $element::class
                    );
                }

                $element->addProperty(
                    new Property($name, $propertySchema->default)
                );
            }
        }

        // C) Typen prüfen (optional, aber empfohlen)
        foreach ($properties as $property) {
            $expected = $schema[$property->getName()]->type;
            $this->assertType($property, $expected, $element);
        }
    }

    private function assertType(Property $property, string $expected, AbstractElement $element): void
    {
        $value = $property->getValue();

        $ok = match ($expected) {
            'string' => is_string($value),
            'int'    => is_int($value),
            'bool'   => is_bool($value),
            'map'    => $value instanceof \pkremer\WebFrontend\Property\PropertyMap,
            default  => true,
        };

        if (!$ok) {
            throw new RuntimeException(
                "Invalid type for property '{$property->getName()}' in " . get_class($element) . ", value is {$value}"
            );
        }
    }
}
