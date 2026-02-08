<?php

namespace pkremer\WebFrontend\Highlight;
use GeSHi;

final class Highlighter
{
    /**
     * @param list<string> $lines
     * @return list<string> HTML per line (no trailing newline)
     */
    public function highlightLines(array $lines, string $language): string
    {
        $language = strtolower(trim($language));


        /** @var object $geshi */
        $geshi = new GeSHi(implode("\n", $lines), $language);
        $geshi->enable_classes(true);
        $geshi->enable_line_numbers(\GESHI_NO_LINE_NUMBERS);
        $geshi->set_header_type(\GESHI_HEADER_NONE);

        if (!in_array($language, $geshi->get_supported_languages())) {
            $geshi->set_language_path(APP_ROOT . '/src/Highlight/Languages/');
        }

        $html = $geshi->parse_code();
        if (!is_string($html)) {
            throw new \RuntimeException('GeSHi parse_code did not return a string');
        }
        return str_replace("<br />", "", $html);
    }
}
