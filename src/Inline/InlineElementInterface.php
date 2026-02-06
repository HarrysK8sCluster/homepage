<?php

namespace pkremer\WebFrontend\Inline;

interface InlineElementInterface
{
    /** @param string[] $args */
    public function render(array $args): string;
}
