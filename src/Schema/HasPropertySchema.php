<?php

namespace pkremer\WebFrontend\Schema;

interface HasPropertySchema
{
    /** @return PropertySchema[] */
    public static function schema(): array;
}
