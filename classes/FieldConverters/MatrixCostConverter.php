<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\AttributeConverters\CostConverter;
use Opencontent\Easyontology\ConverterHelper;
use Opencontent\Easyontology\Map;

class MatrixCostConverter extends AbstractFieldConverter
{

    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content)
    {
        $values = null;
        $data = array_shift($dataByLanguage);

        if ($this->rdfRange == CostConverter::COST_TYPE) {
            $values = $this->getDoc('cost', $this->fieldDefinition['id'] . '-' . $this->fieldDefinition['version']);
        }

        return $values;
    }

    private function getDoc($concept, $id)
    {
        try {
            $converter = \Opencontent\Easyontology\ConverterFactory::factory($concept, $id);
            $newContext = array_merge($this->context->getArrayCopy(), $converter->getContext()->getArrayCopy());
            $this->context->exchangeArray($newContext);

            return $converter->getDoc();

        } catch (\Exception $e) {
            \eZDebug::writeError($e->getMessage(), __METHOD__ . '#' . $e->getLine());
        }

        return false;
    }

}