<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Schema\HasPropertySchema;
use pkremer\WebFrontend\Schema\PropertySchema;

final class TagcloudElement extends AbstractElement implements HasPropertySchema
{

    public static function schema(): array
    {
        return [];
    }

}
