<?php

namespace pkremer\WebFrontend\Render;

use pkremer\WebFrontend\Element\AbstractElement;
use pkremer\WebFrontend\Inline\InlineParser;

final class Renderer
{
    public function __construct(
        private readonly RenderContext $context,
        private readonly InlineParser $inlineParser
    ) {}

    public function render(AbstractElement $element): string
    {
        $html = $element->render($this->context);
        return $this->inlineParser->parse($html);
    }
}
