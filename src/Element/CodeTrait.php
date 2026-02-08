<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Inline\InlineParser;
use pkremer\WebFrontend\Render\RenderContext;

trait CodeTrait
{

    public function render(RenderContext $context, InlineParser $inlineParser): string
    {
        list($start, $end) = explode(':', $this->properties->getProperty('LineNumbers')->getValue());
        $start = (int) $start;
        $end = (int) $end;

        $lines = $context->vars['_sourcefile_lines'] ?? null;
        $language = $context->vars['_sourcefile_language'] ?? null;
        $slice = array_slice($lines, $start - 1, $end - $start + 1);

        $highlighter = $context->vars['_highlighter'] ?? null;
        $out = $highlighter->highlightLines($slice, $language);


        $template = $this->defaultTemplateName();
        return $context->twig->render(
            "{$context->elementTemplatePath}/{$template}.twig",
            array_merge($this->extractProperties(), [
                'FirstLine' => $start,
                'LastLine' => $end,
                'Out' => $out,
                'Language' => $language,
            ], $context->vars)
        );
    }
}