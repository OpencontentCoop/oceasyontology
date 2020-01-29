<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\FieldConverterInterface;

abstract class AbstractFieldConverter implements FieldConverterInterface
{
    protected $fieldDefinition;

    protected $rdfRange;

    /**
     * @var \ArrayObject
     */
    protected $context;

    /**
     * AbstractFieldConverter constructor.
     * @param array $fieldDefinition
     * @param string $rdfRange
     * @param \ArrayObject $context
     */
    public function __construct($fieldDefinition, $rdfRange, $context)
    {
        $this->fieldDefinition = $fieldDefinition;
        $this->rdfRange = $rdfRange;
        $this->context = $context;
    }
}