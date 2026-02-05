<?php

namespace pkremer\WebFrontend\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class SvgExtension extends AbstractExtension
{

    public function __construct(
        private readonly string $projectDir,
        private readonly string $svgDir = 'html/icons',
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('svg', [$this, 'renderSvg'], ['is_safe' => ['html']]),
        ];
    }

    public function renderSvg(string $name): Markup
    {
        $filename = str_ends_with($name, '.svg') ? $name : $name . '.svg';
        $path = rtrim($this->projectDir . '/' . trim($this->svgDir, '/'), '/')
            . '/' . ltrim($filename, '/');
        if (!is_file($path)) {
            return new Markup(sprintf('<!-- svg not found: %s -->', htmlspecialchars($filename)), 'UTF-8');
        }
        $svg = file_get_contents($path);
        if ($svg === false) {
            return new Markup(sprintf('<!-- could not read svg: %s -->', htmlspecialchars($filename)), 'UTF-8');
        }
        return new Markup($svg, 'UTF-8');
    }

}