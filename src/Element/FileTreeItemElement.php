<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Schema\HasPropertySchema;
use pkremer\WebFrontend\Schema\PropertySchema;

final class FileTreeItemElement extends AbstractElement implements HasPropertySchema
{
    public static function schema(): array
    {
        return [
            new PropertySchema('Type', 'string', null, true),
            new PropertySchema('Path', 'string', null, true),
            new PropertySchema('Content', 'string', null, true),
        ];
    }

    public function validate(): bool
    {
        $type = (string) $this->properties->getProperty('Type')->getValue();
        return in_array($type, ['Directory', 'File'], true);
    }

    protected function getVars(): array
    {
        $type = (string) $this->properties->getProperty('Type')->getValue();

        return [
            'Icon' => $type === 'Directory' ? 'folder' : 'file',
        ];
    }
}

