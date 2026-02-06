<?php

namespace pkremer\WebFrontend\Inline;

final class LinkInline implements InlineElementInterface
{
    public function render(array $args): string
    {
        [$label, $url] = $args + [null, null];

        if (!$label || !$url) {
            return '';
        }

        return sprintf(
            '<a href="%s">%s</a>',
            htmlspecialchars($url, ENT_QUOTES),
            htmlspecialchars($label, ENT_QUOTES)
        );
    }
}
