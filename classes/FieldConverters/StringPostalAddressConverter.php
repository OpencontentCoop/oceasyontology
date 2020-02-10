<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\ConverterHelper;
use Opencontent\Easyontology\Map;

class StringPostalAddressConverter extends AbstractFieldConverter
{
    const SCHEMA_POSTAL = 'http://schema.org/PostalAddress';

    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content)
    {
        $values = null;
        $data = array_shift($dataByLanguage);

        if ($this->rdfRange == self::SCHEMA_POSTAL) {
            $values = [
                "@type" => ConverterHelper::compactUri(self::SCHEMA_POSTAL, $this->context),
                ConverterHelper::compactUri("http://schema.org/streetAddress", $this->context) => $data['content'],                
            ];
        }

        return $values;
    }
}