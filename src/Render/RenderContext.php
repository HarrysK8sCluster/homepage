<?php

namespace pkremer\WebFrontend\Render;

use Twig\Environment;

final class RenderContext
{
    public function __construct(
        public readonly Environment $twig,
        public readonly array $vars,
        public readonly string $elementTemplatePath = 'elements',
        public readonly string $pageTemplatePath = 'page',
    ) {}

    public function createChildContext(array $vars = []): RenderContext
    {
        return new RenderContext(
            $this->twig,
            array_merge($this->vars, $vars),
            $this->elementTemplatePath,
            $this->pageTemplatePath
        );
    }
}
