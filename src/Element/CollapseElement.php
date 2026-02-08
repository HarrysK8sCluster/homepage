<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Highlight\Highlighter;
use pkremer\WebFrontend\Render\RenderContext;
use pkremer\WebFrontend\Schema\HasPropertySchema;
use pkremer\WebFrontend\Schema\PropertySchema;
use pkremer\WebFrontend\Util\LineRange;
use RuntimeException;

final class CollapseElement extends AbstractElement implements HasPropertySchema
{
    use CodeTrait;
    public static function schema(): array
    {
        return [
            new PropertySchema('LineNumbers', 'string', null, true),
            new PropertySchema('Summary', 'string', '…'),
            new PropertySchema('Ident', 'int', 0),
        ];
    }

}

