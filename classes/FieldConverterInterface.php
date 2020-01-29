<?php

namespace Opencontent\Easyontology;

interface FieldConverterInterface
{
    public function __construct($fieldDefinition, $rdfRange);

    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content);
}