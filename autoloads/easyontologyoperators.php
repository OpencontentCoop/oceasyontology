<?php

class EasyontologyOperators extends eZTemplateOperator
{
    function operatorList()
    {
        return [
            'easyontology_converter_name',
            'easyontology_to_json',
        ];
    }

    function namedParameterList()
    {
        return [
            'easyontology_converter_name' => [
                'properties' => ['required' => true, 'type' => 'array'],
                'definition' => ['required' => true, 'type' => 'array'],
            ],
            'easyontology_to_json' => [
                'concept' => ['required' => true, 'type' => 'string'],                
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
            
            case 'easyontology_to_json':
                $id = (int)$operatorValue;
                $concept = $namedParameters['concept'];
                try {
                    $converter = \Opencontent\Easyontology\ConverterFactory::factory($concept, $id);
                    $operatorValue = json_encode($converter->jsonSerialize());
                } catch (Exception $e) {                    
                    eZDebug::writeError($e->getMessage());
                    $operatorValue = false;
                }

                break;

            case 'easyontology_converter_name':
                $properties = $namedParameters['properties'];
                $definition = $namedParameters['definition'];

                $converter = \Opencontent\Easyontology\FieldConverterFactory::factory($properties, $definition, new ArrayObject());
                $operatorValue = $converter instanceof \Opencontent\Easyontology\FieldConverterInterface ? get_class($converter) : '?';

                break;
        }
    }


}

