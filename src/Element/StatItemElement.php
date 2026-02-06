<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Schema\HasPropertySchema;
use pkremer\WebFrontend\Schema\PropertySchema;

final class StatItemElement extends AbstractElement implements HasPropertySchema
{

    public static function schema(): array
    {
        return [
            new PropertySchema('Name', 'string', null, true),
            new PropertySchema('Value', 'string', null, true),
        ];
    }

}
