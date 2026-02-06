<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Property\Property;
use pkremer\WebFrontend\Property\PropertyMap;
use pkremer\WebFrontend\Render\RenderContext;

abstract class AbstractElement implements ElementInterface, RenderableElementInterface
{
    protected PropertyMap $properties;
    protected array $elements = [];

    public function __construct()
    {
        $this->properties = new PropertyMap();
    }

    public function addProperty(Property $property): void
    {
        $this->properties->addProperty($property);
    }

    public function getProperties(): PropertyMap
    {
        return $this->properties;
    }

    public function addElement(ElementInterface $element): void
    {
        $this->elements[] = $element;
    }

    public function getElements(): array
    {
        return $this->elements;
    }

    protected function getVars(): array {
        return [];
    }

    public function render(RenderContext $context): string
    {
        $vars = $this->getVars();
        $template = $this->defaultTemplateName();
        $html = $context->twig->render(
            "{$context->elementTemplatePath}/{$template}.twig",
            array_merge($this->extractProperties(), $vars, $context->vars)
        );
        $html = str_replace('%content%', $this->renderChildren($context), $html);
        return $html;
    }

    protected function defaultTemplateName(): string
    {
        return strtolower(
            str_replace('Element', '', (new \ReflectionClass($this))->getShortName())
        );
    }

    protected function renderChildren(RenderContext $context): string
    {
        $html = '';

        foreach ($this->elements as $child) {
            $html .= $child->render($context);
        }

        return $html;
    }

    protected function extractProperties(): array
    {
        $vars = [];

        foreach ($this->properties->getProperties() as $property) {
            $vars[$property->getName()] = $this->mapPropertyValue(
                $property->getValue()
            );
        }

        return $vars;
    }

    private function mapPropertyValue(mixed $value): mixed
    {
        if ($value instanceof PropertyMap) {
            $out = [];
            foreach ($value->getProperties() as $property) {
                $out[$property->getName()] = $this->mapPropertyValue(
                    $property->getValue()
                );
            }
            return $out;
        }

        return $value;
    }

}
