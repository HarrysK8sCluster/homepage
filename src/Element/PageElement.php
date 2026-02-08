<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Inline\InlineParser;
use pkremer\WebFrontend\Schema\HasPropertySchema;
use pkremer\WebFrontend\Schema\PropertySchema;
use pkremer\WebFrontend\Schema\PropertyMapSchema;
use pkremer\WebFrontend\Render\RenderContext;

final class PageElement extends AbstractElement implements HasPropertySchema
{
    public static function schema(): array
    {
        return [
            new PropertySchema(
                name: 'Template',
                type: 'string',
                required: true
            ),
        ];
    }
    public function render(RenderContext $context, InlineParser $inlineParser): string
    {
        $template = $this->getProperties()
            ->getProperties()['Template']
            ->getValue();

        $html = $inlineParser->parse($context->twig->render(
            "{$context->pageTemplatePath}/{$template}.twig",
            array_merge($this->extractProperties(), $context->vars)
        ));
        $html = str_replace('%content%', $this->renderChildren($context, $inlineParser), $html);

        return $html;
    }


}
