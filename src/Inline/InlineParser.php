<?php

namespace pkremer\WebFrontend\Inline;

use pkremer\WebFrontend\Element\AbstractElement;
use RuntimeException;

final class InlineParser
{

    private array $elements = [];
    public function __construct() {}

    public function parse(string $html): string
    {
        return preg_replace_callback(
            '/@\(([^|)]+)((?:\|[^)]*)?)\)/',
            function (array $m): string {
                $name = $m[1];
                $args = $m[2] === ''
                    ? []
                    : array_map(
                        'trim',
                        explode('|', ltrim($m[2], '|'))
                    );

                return $this->getElement($name)->render($args);
            },
            $html
        );
    }

    private function getElement(string $name): InlineElementInterface
    {
        if (!isset($this->elements[$name])) {
            $className = __Namespace__ . '\\' . ucfirst($name) . 'Inline';
            if (!class_exists($className)) {
                throw new RuntimeException("Unknown inline element '{$name}'");

            }
            $this->elements[$name] = new $className();
        }
        return $this->elements[$name];
    }
}
