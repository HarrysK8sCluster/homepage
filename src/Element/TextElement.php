<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Schema\HasPropertySchema;
use pkremer\WebFrontend\Schema\PropertySchema;

final class TextElement extends AbstractElement implements HasPropertySchema
{

    public static function schema(): array
    {
        return [
            new PropertySchema('Content', 'string', null, true),
        ];
    }

}
