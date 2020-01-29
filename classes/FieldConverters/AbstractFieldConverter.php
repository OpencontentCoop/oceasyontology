<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\FieldConverterInterface;

abstract class AbstractFieldConverter implements FieldConverterInterface
{
    protected $fieldDefinition;

    protected $rdfRange;

    public function __construct($fieldDefinition, $rdfRange)
    {
        $this->fieldDefinition = $fieldDefinition;
        $this->rdfRange = $rdfRange;
    }
}