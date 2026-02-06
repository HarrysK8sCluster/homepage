<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Schema\HasPropertySchema;
use pkremer\WebFrontend\Schema\PropertySchema;

final class RowElement extends AbstractElement implements HasPropertySchema
{

    protected function getVars(): array {
        return [
            'Count' => count($this->elements)
        ];
    }

    public static function schema(): array
    {
        return [];
    }

}
