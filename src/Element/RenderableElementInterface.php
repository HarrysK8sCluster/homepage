<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Render\RenderContext;

interface RenderableElementInterface
{
    public function render(RenderContext $context): string;
}
