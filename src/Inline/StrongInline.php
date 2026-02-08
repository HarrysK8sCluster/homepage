<?php

namespace pkremer\WebFrontend\Inline;

final class StrongInline implements InlineElementInterface
{
    public function render(array $args): string
    {
        [$text] = $args + [null];

        if (!$text) {
            return '';
        }

        return sprintf(
            '<strong>%s</strong>',
            htmlspecialchars($text, ENT_QUOTES)
        );
    }
}
