<?php

class EasyontologyOperators extends eZTemplateOperator
{
    function operatorList()
    {
        return [
            'easyontology_converter_name',
            'easyontology_to_json',
            'easyontology_links',
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
            ],
            'easyontology_links' => [
                'class_identifier' => ['required' => true, 'type' => 'string'],
                'object_id' => ['required' => true, 'type' => 'integer'],
            ],
        ];
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function modify($tpl, $operatorName, &$operatorParameters, &$rootNamespace, &$currentNamespace, &$operatorValue, &$namedParameters)
    {
        switch ($operatorName) {

            case 'easyontology_links':
                $links = [];

                $classIdentifier = $namedParameters['class_identifier'];
                $id = $namedParameters['object_id'];

                $mapCollection = \Opencontent\Easyontology\MapperRegistry::fetchMapCollectionByClassIdentifier($classIdentifier);
                foreach ($mapCollection->getMaps() as $map){
                    $links[$map->getSlug()] = \Opencontent\Easyontology\ConverterHelper::generateId($map->getSlug(), $id);
                }

                $operatorValue = $links;
                break;

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

