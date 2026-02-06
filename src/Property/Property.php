<?php

namespace pkremer\WebFrontend\Property;

class Property
{

    public function __construct(
        private readonly string $name,
        private readonly null|string|int|float|bool|PropertyMap $value
    ) {}

    public function getName(): string{
        return $this->name;
    }

    public function getValue(): null|string|int|float|bool|PropertyMap{
        return $this->value;
    }


}