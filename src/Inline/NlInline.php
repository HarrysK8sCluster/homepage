<?php

namespace pkremer\WebFrontend\Inline;

final class NlInline implements InlineElementInterface
{
    public function render(array $args): string
    {
        return '<br />';
    }
}
