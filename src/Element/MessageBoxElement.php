<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Schema\HasPropertySchema;
use pkremer\WebFrontend\Schema\PropertySchema;

final class MessageBoxElement extends AbstractElement implements HasPropertySchema
{

    public static function schema(): array
    {
        return [
            new PropertySchema('Type', 'string', null, true),
            new PropertySchema('Header', 'string', null, true),
        ];
    }



}
