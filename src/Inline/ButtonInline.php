<?php

namespace pkremer\WebFrontend\Inline;

final class ButtonInline implements InlineElementInterface
{
    public function render(array $args): string
    {
        [$label, $url] = $args + [null, null];

        if (!$label || !$url) {
            return '';
        }

        return sprintf(
            '<a class="btn btn--primary btn--sm" href="%s">%s</a>',
            htmlspecialchars($url, ENT_QUOTES),
            htmlspecialchars($label, ENT_QUOTES)
        );
    }
}
