<?php

class EasyontologyOperators extends eZTemplateOperator
{
    function operatorList()
    {
        return ['easyontology_converter_name'];
    }

    function namedParameterList()
    {
        return [
            'easyontology_converter_name' => [
                'properties' => ['required' => true, 'type' => 'array'],
                'definition' => ['required' => true, 'type' => 'array'],
            ]
        ];
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function modify($tpl, $operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters)
    {
        switch ($operatorName) {
            case 'easyontology_converter_name':
                $properties = $namedParameters['properties'];
                $definition = $namedParameters['definition'];

                $converter = \Opencontent\Easyontology\FieldConverterFactory::factory($properties, $definition);
                $operatorValue = $converter instanceof \Opencontent\Easyontology\FieldConverterInterface ? get_class($converter) : '?';

                break;
        }
    }


}

