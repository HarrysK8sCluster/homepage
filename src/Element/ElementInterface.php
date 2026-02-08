<?php

namespace pkremer\WebFrontend\Element;

use pkremer\WebFrontend\Property\PropertyMapInterface;

interface ElementInterface extends PropertyMapInterface
{
    public function addElement(ElementInterface $element): void;

    public function validate(): bool;

}