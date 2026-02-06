<?php

namespace pkremer\WebFrontend\Render;

use pkremer\WebFrontend\Element\ElementInterface;
use pkremer\WebFrontend\Inline\InlineParser;

final class Renderer
{
    public function __construct(
        private readonly RenderContext $context,
        private readonly InlineParser $inlineParser
    ) {}

    public function render(ElementInterface $element): string
    {
        $html = $element->render($this->context);
        return $this->inlineParser->parse($html);
    }
}
