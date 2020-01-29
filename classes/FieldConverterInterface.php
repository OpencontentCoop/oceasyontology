<?php

namespace Opencontent\Easyontology;

interface FieldConverterInterface
{
    /**
     * @param array $fieldDefinition
     * @param string $rdfRange
     * @param \ArrayObject $context
     */
    public function __construct($fieldDefinition, $rdfRange, $context);

    /**
     * @param array $dataByLanguage
     * @param string $mapToUri
     * @param Map $classMap
     * @param array $content
     * @return mixed
     */
    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content);
}