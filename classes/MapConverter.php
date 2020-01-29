<?php

namespace Opencontent\Easyontology;

use Opencontent\Easyontology\FieldConverters\LiteralConverter;
use Opencontent\Opendata\Api\Values\Content;
use eZContentObject;

class MapConverter implements ConverterInterface, \JsonSerializable
{
    /**
     * @var Map
     */
    private $map;

    /**
     * @var Content
     */
    private $content;

    private $contentData;

    private $concept;

    private $doc = [];

    private $context;

    private $isConverted;

    public static $debugMode = false;

    public function __construct(Map $map, $concept, $content)
    {
        $this->map = $map;
        $this->concept = $concept;
        if ($content instanceof eZContentObject) {
            $content = Content::createFromEzContentObject($content);
        }
        $this->content = $content;
        $this->contentData = [];
        foreach ($this->content->data as $language => $fields){
            $locale = explode('-', \eZLocale::instance($language)->httpLocaleCode());
            $locale = substr(strtolower($locale[0]),0,2);
            foreach ($fields as $field){
                list($classIdentifier, $fieldIdentifier) = explode('/', $field['identifier']);
                if (!empty($field['content'])){
                    $this->contentData[$fieldIdentifier][$locale] = $field;
                }
            }
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getDoc()
    {
        $this->convert();
        return $this->doc;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getContext()
    {
        $this->convert();
        return $this->context;
    }

    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function jsonSerialize()
    {
        $data = [];
        $context = $this->getContext();
        if ($context) {
            $data['@context'] = $context;
        }

        $data = array_merge($data, $this->getDoc());
        return $data;
    }

    /**
     * @throws \Exception
     */
    private function convert()
    {
        //echo '<pre>';print_r([$this->contentData]);die();
        if (!$this->isConverted) {

            $this->doc['@id'] = $this->getId();
            $this->doc['@type'] = $this->getType();
            $this->convertFields();

            $this->isConverted = true;
        }
    }

    private function getId()
    {
        return self::generateId($this->concept, $this->content->metadata->id);
    }

    public static function generateId($concept, $id)
    {
        return rtrim(\eZSys::serverURL(), '/') . '/onto/data/' . $concept . '/' . $id;
    }

    /**
     * @return array|mixed
     * @throws \Exception
     */
    private function getType()
    {
        if (isset($this->map->getFlatMapping()['_class'])) {
            $types = array_values($this->map->getFlatMapping()['_class']);
            if (count($types) == 1) {
                return array_shift($types);
            }

            return $types;
        }

        throw new \Exception("Type not found for {$this->concept}/{$this->content->metadata->id}");
    }

    private function convertFields()
    {
        foreach ($this->contentData as $fieldIdentifier => $dataByLanguage){
            if (isset($this->map->getFlatMapping()[$fieldIdentifier])){
                foreach ($this->map->getFlatMapping()[$fieldIdentifier] as $uri){
                    $convertedField = $this->convertField($dataByLanguage, $uri);
                    if ($convertedField)
                        $this->doc[$uri] = $convertedField;
                }
            }
        }
    }

    private function convertField($dataByLanguage, $uri)
    {
        $properties = isset($this->map->getProperties()[$uri]) ? $this->map->getProperties()[$uri] : [];
        $fieldDefinition = $this->getFieldDefinition($dataByLanguage);

        $fieldConverter = FieldConverterFactory::factory($properties, $fieldDefinition);
        if ($fieldConverter instanceof FieldConverterInterface){
            return self::$debugMode ? null : $fieldConverter->convert($dataByLanguage, $uri, $this->map, $this->content);
        }

        return self::$debugMode ? ['properties' => $properties, 'field' => $dataByLanguage] : null;

    }

    private function getFieldDefinition($dataByLanguage)
    {
        $fieldDefinition = array_shift($dataByLanguage);

        return [
            'id' => $fieldDefinition['id'],
            'version' => $fieldDefinition['version'],
            'identifier' => $fieldDefinition['identifier'],
            'datatype' => $fieldDefinition['datatype'],
        ];
    }
}