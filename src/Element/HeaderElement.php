<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Schema\HasPropertySchema;
use pkremer\WebFrontend\Schema\PropertySchema;

final class HeaderElement extends AbstractElement implements HasPropertySchema
{

    public static function schema(): array
    {
        return [
            new PropertySchema('Type', 'string', null, true),
            new PropertySchema('Content', 'string', null, true),
        ];
    }

    public function validate(): bool
    {
        $value = $this->properties->getProperty('Type')->getValue();
        return in_array($value, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']);
    }

}
