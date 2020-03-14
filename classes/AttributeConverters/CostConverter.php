<?php

namespace Opencontent\Easyontology\AttributeConverters;

use Opencontent\Easyontology\ConverterHelper;
use Opencontent\Easyontology\AbstractConverter;

class CostConverter extends AbstractConverter
{
    const COST_TYPE = 'https://w3id.org/italia/onto/CPSV/Cost';

    private $concept;

    private $id;

    private $isConverted;

    /**
     * @var \eZMatrix;
     */
    private $matrix;

    private $matrixData;

    public function __construct($concept, $id)
    {
        $this->concept = $concept;
        $this->id = $id;
        list($id, $version) = explode('-', $this->id, 2);
        /** @var \eZContentObjectAttribute $attribute */
        $attribute = \eZContentObjectAttribute::fetch($id, $version);
        if ($attribute->hasContent() && $attribute->attribute('data_type_string') == \eZMatrixType::DATA_TYPE_STRING) {
            $this->matrix = $attribute->content();
            $columns = (array) $this->matrix->attribute( 'columns' );
            $rows = (array) $this->matrix->attribute( 'rows' );

            $keys = array();
            foreach( $columns['sequential'] as $column )
            {
                $keys[] = $column['identifier'];
            }
            $this->matrixData = array();
            foreach( $rows['sequential'] as $row )
            {
                $this->matrixData[] = array_combine( $keys, $row['columns'] );
            }
        }
        $this->context = new \ArrayObject();
    }

    //@todo!!!    
    protected function convert()
    {
        if (!$this->isConverted &&  $this->matrix instanceof \eZMatrix) {
            $language = 'it';
            $this->doc = [
                '@id' => ConverterHelper::generateId($this->concept, $this->id),
                '@type' => ConverterHelper::compactUri(self::COST_TYPE, $this->context)
            ];
            $this->doc[ConverterHelper::compactUri('http://data.europa.eu/m8g/value', $this->context)] = [
                '@value' => trim(str_replace('€', '', $this->matrixData[0]['value'])),                 
                "@type" => "xsd:double"
            ];
            if (!empty($this->matrixData[0]['description'])){
                $this->doc[ConverterHelper::compactUri('http://purl.org/dc/terms/description', $this->context)] = [
                    '@value' => $this->matrixData[0]['description'],
                    '@language' => $language,
                ];
            }
            if (!empty($this->matrixData[0]['characteristic'])){
                $this->doc[ConverterHelper::compactUri('https://w3id.org/italia/onto/l0/Characteristic', $this->context)] = [
                    '@value' => $this->matrixData[0]['characteristic'],
                    '@language' => $language,
                ];
            }
            $this->doc[ConverterHelper::compactUri('http://data.europa.eu/m8g/currency', $this->context)] = ['@id' => 'http://publications.europa.eu/resource/authority/currency/EUR']; //$this->matrixData[0]['currency']
            $this->isConverted = true;
        }
    }
}