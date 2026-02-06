<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Schema\HasPropertySchema;
use pkremer\WebFrontend\Schema\PropertyMapSchema;
use pkremer\WebFrontend\Schema\PropertySchema;

final class HeadingElement extends AbstractElement implements HasPropertySchema
{
    public static function schema(): array
    {
        return [
            new PropertySchema(
                name: 'Image',
                type: 'string',
                default: ""
            ),
            new PropertySchema(
                name: 'Intro',
                type: 'string',
                default: ""
            ),
            new PropertySchema(
                name: 'Main',
                type: 'string',
                required: true,
            ),
            new PropertySchema(
                name: 'Outro',
                type: 'string',
                default: ""
            ),
        ];
    }
}
