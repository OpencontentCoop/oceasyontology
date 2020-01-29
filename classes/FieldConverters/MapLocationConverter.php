<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\AttributeConverters\AddressConverter;
use Opencontent\Easyontology\ConverterHelper;
use Opencontent\Easyontology\Map;

class MapLocationConverter extends AbstractFieldConverter
{
    const SCHEMA_GEO = 'http://schema.org/GeoCoordinates';

    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content)
    {
        $values = null;
        $data = array_shift($dataByLanguage);

        if ($this->rdfRange == AddressConverter::COV_ADDRESS_TYPE) {
            $values = $this->getDoc('address', $this->fieldDefinition['id'] . '-' . $this->fieldDefinition['version']);
        }

        if ($this->rdfRange == self::SCHEMA_GEO) {
            $values = [
                "@type" => ConverterHelper::compactUri(self::SCHEMA_GEO, $this->context),
                ConverterHelper::compactUri("http://schema.org/address", $this->context) => $data['content']['address'],
                ConverterHelper::compactUri("http://schema.org/latitude", $this->context) => $data['content']['latitude'],
                ConverterHelper::compactUri("http://schema.org/longitude", $this->context) => $data['content']['longitude'],
            ];
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