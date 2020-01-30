<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\ConverterHelper;
use Opencontent\Easyontology\Map;

class BinaryfileConverter extends AbstractFieldConverter
{
    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content)
    {
        $values = [];
        foreach ($dataByLanguage as $language => $field) {
            $url = $field['content']['url'];
            if (strpos($url, 'http') === false) {
                \eZURI::transformURI($url, true, 'full');
            }
            $values[] = [
                '@type' => ConverterHelper::compactUri($this->rdfRange, $this->context),
                '@id' => $url,
            ];
        }

        if (count($values) == 1){
            $values = $values[0];
        }

        return $values;
    }

}