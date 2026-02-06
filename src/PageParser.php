<?php

namespace pkremer\WebFrontend;

use RuntimeException;

class PageParser
{
    private const INDENT_SIZE = 4;

    private array $lines = [];
    private int $lineNumber = 0;

    public function parse(string $input): array
    {
        $this->lines = preg_split("/\r?\n/", $input);
        $this->lineNumber = 0;

        $root = $this->createNode('Page');

        $this->parseBlock($root, 0);

        return $root;
    }

    private function parseBlock(array &$parent, int $baseIndent): void
    {
        while ($this->hasMoreLines()) {
            if ($this->isEmptyLine()) {
                $this->advanceLine();
                continue;
            }

            $indent = $this->currentIndent();

            if ($indent < $baseIndent) {
                return;
            }

            if ($indent > $baseIndent) {
                $this->error('Unexpected indentation');
            }

            if ($this->isChildNode()) {
                $parent['children'][] = $this->parseChildNode($indent);
                continue;
            }

            if ($this->isAttributeLine()) {
                $this->parseAttribute($parent, $indent);
                continue;
            }

            $this->error('Syntax error');
        }
    }

    private function parseChildNode(int $indent): array
    {
        if (!preg_match('/^- ([A-Za-z0-9_]+):$/', trim($this->currentLine()), $m)) {
            $this->error('Invalid object declaration');
        }

        $node = $this->createNode($m[1]);
        $this->advanceLine();

        $this->parseBlock($node, $indent + 1);

        return $node;
    }

    private function parseAttribute(array &$parent, int $indent): void
    {
        preg_match('/^([A-Za-z0-9_]+):(?:\s*(.*))?$/', trim($this->currentLine()), $m);

        $key = $m[1];
        $value = $m[2] ?? null;

        // Multiline block
        if ($value === '|') {
            $parent['attributes'][$key] = $this->parseMultiline($indent);
            return;
        }

        // Object attribute
        if ($value === null || $value === '') {
            $this->advanceLine();
            $parent['attributes'][$key] = $this->parseAttributeObject($indent + 1);
            return;
        }

        // Scalar
        $parent['attributes'][$key] = $this->parseValue($value);
        $this->advanceLine();
    }

    private function parseAttributeObject(int $baseIndent): array
    {
        $result = [];

        while ($this->hasMoreLines()) {
            if ($this->isEmptyLine()) {
                $this->advanceLine();
                continue;
            }

            $indent = $this->currentIndent();

            if ($indent < $baseIndent) {
                return $result;
            }

            if ($indent > $baseIndent) {
                $this->error('Invalid indentation inside attribute object');
            }

            if ($this->isChildNode()) {
                $this->error('Child objects not allowed inside attribute objects');
            }

            if (!preg_match('/^([A-Za-z0-9_]+):\s*(.*)$/', trim($this->currentLine()), $m)) {
                $this->error('Invalid attribute in attribute object');
            }

            $key = $m[1];
            $value = $m[2];

            if ($value === '|') {
                $result[$key] = $this->parseMultiline($indent);
            } else {
                $result[$key] = $this->parseValue($value);
                $this->advanceLine();
            }
        }

        return $result;
    }

    /* =========================
     * Value parsing
     * ========================= */

    private function parseMultiline(int $baseIndent): string
    {
        $this->advanceLine();
        $buffer = [];

        while ($this->hasMoreLines()) {
            if ($this->isEmptyLine()) {
                $buffer[] = '';
                $this->advanceLine();
                continue;
            }

            if ($this->currentIndent() <= $baseIndent) {
                break;
            }

            $buffer[] = substr(
                $this->currentLine(),
                ($baseIndent + 1) * self::INDENT_SIZE
            );

            $this->advanceLine();
        }

        return rtrim(implode("\n", $buffer));
    }

    private function parseValue(string $value): null|int|float|bool|string
    {
        $value = trim($value);
        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            return substr($value, 1, -1);
        }
        if (strtolower($value) === 'null') {
            return null;
        }
        if (strtolower($value) === 'true') {
            return true;
        }
        if (strtolower($value) === 'false') {
            return false;
        }
        if (is_numeric($value)) {
            if (strpos($value, '.') !== false) {
                return (float) $value;
            } else {
                return (int) $value;
            }
        }
        return $value;
    }

    /* =========================
     * Helpers
     * ========================= */

    private function createNode(string $type): array
    {
        return [
            'type' => $type,
            'attributes' => [],
            'children' => []
        ];
    }

    private function currentLine(): string
    {
        return $this->lines[$this->lineNumber];
    }

    private function advanceLine(): void
    {
        $this->lineNumber++;
    }

    private function hasMoreLines(): bool
    {
        return $this->lineNumber < count($this->lines);
    }

    private function isEmptyLine(): bool
    {
        return trim($this->currentLine()) === '';
    }

    private function isChildNode(): bool
    {
        return str_starts_with(trim($this->currentLine()), '- ');
    }

    private function isAttributeLine(): bool
    {
        return preg_match('/^[A-Za-z0-9_]+:/', trim($this->currentLine())) === 1;
    }

    private function currentIndent(): int
    {
        preg_match('/^(\s*)/', $this->currentLine(), $m);
        return intdiv(strlen($m[1]), self::INDENT_SIZE);
    }

    private function error(string $message): never
    {
        throw new RuntimeException(
            "{$message} at line {$this->lineNumber}"
        );
    }
}
