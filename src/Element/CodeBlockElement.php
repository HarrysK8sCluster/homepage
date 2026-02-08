<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Schema\HasPropertySchema;
use pkremer\WebFrontend\Schema\PropertySchema;

final class CodeBlockElement extends AbstractElement implements HasPropertySchema
{
    public static function schema(): array
    {
        return [
            new PropertySchema('Comment', 'string', ''),
            new PropertySchema('Title', 'string', ''),
        ];
    }
}

