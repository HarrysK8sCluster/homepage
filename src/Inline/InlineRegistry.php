<?php

namespace pkremer\WebFrontend\Inline;

use RuntimeException;

final class InlineRegistry
{
    /** @var array<string, InlineElementInterface> */
    private array $elements = [];

    public function register(string $name, InlineElementInterface $element): void
    {
        $this->elements[$name] = $element;
    }

    public function get(string $name): InlineElementInterface
    {
        if (!isset($this->elements[$name])) {
            throw new RuntimeException("Unknown inline element '{$name}'");
        }

        return $this->elements[$name];
    }
}
