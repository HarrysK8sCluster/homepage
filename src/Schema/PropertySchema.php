<?php

namespace pkremer\WebFrontend\Schema;

final class PropertySchema
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly mixed $default = null,
        public readonly bool $required = false,
        public readonly ?PropertyMapSchema $mapSchema = null
    ) {}
}
