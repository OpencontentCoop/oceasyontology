<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\FieldConverterFactory;
use Opencontent\Easyontology\Map;

class LiteralConverter extends AbstractFieldConverter
{
    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content)
    {
        $values = [];
        foreach ($dataByLanguage as $language => $field){
            $fieldContent = $field['content'];
            if (!is_array($fieldContent)){
                $values[] = [
                    '@language' => $language,
                    '@value' => $this->cleanValue($fieldContent),
                ];
            }else{
                foreach ($fieldContent as $item){
                    $values[] = [
                        '@language' => $language,
                        '@value' => $this->cleanValue($item),
                    ];
                }
            }
        }

        if (strpos($this->rdfRange, 'schema.org') !== false){
            $values = array_column($values, '@value');
        }

        if (count($values) == 1){
            $values = $values[0];
        }

        return $values;
    }

    private function cleanValue($value)
    {
        $value = strip_tags($value);
        $value = trim($value);

        if ($this->rdfRange == FieldConverterFactory::SCHEMA_BOOLEAN){
            $value = (bool)$value;
        }
        if ($this->rdfRange == FieldConverterFactory::SCHEMA_INTEGER){
            $value = (int)$value;
        }

        return $value;
    }

}