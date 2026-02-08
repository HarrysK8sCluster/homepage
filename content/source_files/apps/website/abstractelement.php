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

    public function addElement(ElementInterface $element): void
    {
        $this->elements[] = $element;
    }

    public function render(RenderContext $context): string
    {
        $template = $this->defaultTemplateName();
        $html = $context->twig->render(
            "{$context->elementTemplatePath}/{$template}.twig",
            array_merge($this->extractProperties(), $context->vars)
        );

        // %content% ist der Slot fuer gerenderte Kind-Elemente
        return str_replace('%content%', $this->renderChildren($context), $html);
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
}

