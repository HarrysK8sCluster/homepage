<?php

namespace pkremer\WebFrontend\Inline;

final class ButtonInline implements InlineElementInterface
{
    public function render(array $args): string
    {
        [$label, $url, $icon] = $args + [null, null, null];

        if (!$label || !$url) {
            return '';
        }

        if ($icon) {
            $icon = file_get_contents(APP_ROOT . "/html/icons/$icon.svg");
            $label = sprintf('<span class="icon">%s</span> %s', $icon, htmlspecialchars($label, ENT_QUOTES));
        } else {
            $label = htmlspecialchars($label, ENT_QUOTES);
        }

        return sprintf(
            '<a class="btn btn--primary btn--sm" href="%s">%s</a>',
            htmlspecialchars($url, ENT_QUOTES),
            $label
        );
    }
}
