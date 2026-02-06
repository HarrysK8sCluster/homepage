<?php

namespace pkremer\WebFrontend\Inline;

final class MailInline implements InlineElementInterface
{
    public function render(array $args): string
    {
        $address = $args[0] ?? null;
        $enc = filter_var($args[1] ?? false, FILTER_VALIDATE_BOOLEAN);
        if (!$address) {
            return '';
        }

        if (!$enc) {
            return sprintf(
                '<a href="mailto:%1$s">%1$s</a>',
                htmlspecialchars($address, ENT_QUOTES)
            );
        }


        // --- Encryption / Obfuscation ---
        $jsonChars = json_encode(str_split($address), JSON_THROW_ON_ERROR);
        $id = 'mail_' . substr(md5($address . random_int(0, PHP_INT_MAX)), 0, 8);

        return <<<HTML
<span id="$id"></span>
<script>
(function () {
    var chars = $jsonChars;
    var address = chars.join('');
    var el = document.getElementById('$id');
    if (!el) return;

    var a = document.createElement('a');
    a.href = 'mailto:' + address;
    a.textContent = address;
    el.replaceWith(a);
})();
</script>
HTML;
    }
}
