<?php

namespace pkremer\WebFrontend\Schema;

final class PropertyMapSchema
{
    /** @param PropertySchema[] $properties */
    public function __construct(
        public readonly array $properties
    ) {}
}
