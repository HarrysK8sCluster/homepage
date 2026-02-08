<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Highlight\Highlighter;
use pkremer\WebFrontend\Inline\InlineParser;
use pkremer\WebFrontend\Render\RenderContext;
use pkremer\WebFrontend\Schema\HasPropertySchema;
use pkremer\WebFrontend\Schema\PropertySchema;
use RuntimeException;

final class SourceFileElement extends AbstractElement implements HasPropertySchema
{
    public static function schema(): array
    {
        return [
            new PropertySchema('Source', 'string', null, true),
            new PropertySchema('Language', 'string', ''),
            new PropertySchema('Title', 'string', ''),
        ];
    }

    public function render(RenderContext $context, InlineParser $inlineParser): string
    {
        $source = (string) $this->getProperties()->getProperty('Source')->getValue();
        $language = (string) $this->getProperties()->getProperty('Language')->getValue();

        $resolved = $this->resolveProjectPath($source);
        $content = @file_get_contents($resolved);
        if ($content === false) {
            throw new RuntimeException("Unable to read source file '{$source}'");
        }

        $childContext = $context->createChildContext([
            '_sourcefile_source' => $source,
            '_sourcefile_path' => $resolved,
            '_sourcefile_language' => $language,
            '_sourcefile_lines' => explode("\n", $content),
            '_highlighter' => new Highlighter(),
        ]);

        $template = $this->defaultTemplateName();
        $html = $inlineParser->parse($context->twig->render(
            "{$context->elementTemplatePath}/{$template}.twig",
            array_merge($this->extractProperties(), $context->vars)
        ));

        return str_replace('%content%', $this->renderChildren($childContext, $inlineParser), $html);
    }

    private function resolveProjectPath(string $path): string
    {
        $root = APP_ROOT . '/content/source_files';
        if ($root === false) {
            throw new RuntimeException('Unable to resolve project root');
        }
        $root = rtrim($root, '/') . '/';

        $candidate = realpath($root . ltrim($path, "/"));
        if ($candidate === false || !is_file($candidate)) {
            throw new RuntimeException("Unknown source file '{$path}'");
        }

        if (!str_starts_with($candidate . '/', $root)) {
            throw new RuntimeException("Refusing to read path outside project root: '{$path}'");
        }

        return $candidate;
    }


}

