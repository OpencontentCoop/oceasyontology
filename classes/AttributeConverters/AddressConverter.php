<?php

namespace Opencontent\Easyontology\AttributeConverters;

use Opencontent\Easyontology\ConverterHelper;
use Opencontent\Easyontology\AbstractConverter;

class AddressConverter extends AbstractConverter
{
    const COV_ADDRESS_TYPE = 'https://w3id.org/italia/onto/CLV/Address';

    private $concept;

    private $id;

    private $isConverted;

    /**
     * @var \eZGmapLocation;
     */
    private $gmapLocation;

    public function __construct($concept, $id)
    {
        $this->concept = $concept;
        $this->id = $id;
        list($id, $version) = explode('-', $this->id, 2);
        /** @var \eZContentObjectAttribute $attribute */
        $attribute = \eZContentObjectAttribute::fetch($id, $version);
        if ($attribute->hasContent() && $attribute->attribute('data_type_string') == \eZGmapLocationType::DATA_TYPE_STRING) {
            $this->gmapLocation = $attribute->content();
        }
        $this->context = new \ArrayObject();
    }

    protected function convert()
    {
        if (!$this->isConverted &&  $this->gmapLocation instanceof \eZGmapLocation) {
            $this->doc = [
                '@id' => ConverterHelper::generateId($this->concept, $this->id),
                '@type' => ConverterHelper::compactUri(self::COV_ADDRESS_TYPE, $this->context)
            ];
            $this->doc[ConverterHelper::compactUri('https://w3id.org/italia/onto/CLV/fullAddress ', $this->context)] = ['@value' =>$this->gmapLocation->attribute('address')];
            $this->doc[ConverterHelper::compactUri('https://w3id.org/italia/onto/CLV/hasGeometry ', $this->context)] = $this->getGeometry();
            $this->isConverted = true;
        }
    }

    private function getGeometry()
    {
        return [
            "@type" => ConverterHelper::compactUri('https://w3id.org/italia/onto/CLV/Geometry', $this->context),
            ConverterHelper::compactUri('https://w3id.org/italia/onto/CLV/hasGeometryType', $this->context) => [
                '@id' => ConverterHelper::compactUri('https://w3id.org/italia/onto/CLV/Point ', $this->context),
                '@type' => ConverterHelper::compactUri('https://w3id.org/italia/onto/CLV/GeometryType', $this->context)
            ],
            ConverterHelper::compactUri('https://w3id.org/italia/onto/CLV/lat', $this->context) => ['@value' =>$this->gmapLocation->attribute('latitude')],
            ConverterHelper::compactUri('https://w3id.org/italia/onto/CLV/long', $this->context) => ['@value' =>$this->gmapLocation->attribute('longitude')],
        ];
    }
}