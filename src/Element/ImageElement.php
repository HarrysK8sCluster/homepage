<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Render\RenderContext;
use pkremer\WebFrontend\Schema\HasPropertySchema;
use pkremer\WebFrontend\Schema\PropertySchema;

final class ImageElement extends AbstractElement implements HasPropertySchema
{

    public static function schema(): array
    {
        return [
            new PropertySchema('Href', 'string', null, true),
            new PropertySchema('Description', 'string', null, true),
            new PropertySchema('Float', 'string', null, false),
            new PropertySchema('Width', 'string', null, false),
            new PropertySchema('Height', 'string', null, false),
        ];
    }

    public function validate(): bool
    {
        $value = $this->properties->getProperty('Float')->getValue();
        return in_array($value, ['left', 'right']);
    }

}
