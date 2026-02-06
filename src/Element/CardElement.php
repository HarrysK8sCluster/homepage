<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Schema\HasPropertySchema;
use pkremer\WebFrontend\Schema\PropertySchema;

final class CardElement extends AbstractElement implements HasPropertySchema
{

    public static function schema(): array
    {
        return [
            new PropertySchema('Heading', 'string', '', true),
            new PropertySchema('Image', 'string', null),
            new PropertySchema('Footer', 'string', null),
        ];
    }



}
