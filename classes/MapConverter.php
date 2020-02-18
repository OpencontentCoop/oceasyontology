<?php

namespace Opencontent\Easyontology;

use Opencontent\Opendata\Api\Values\Content;
use eZContentObject;

class MapConverter extends AbstractConverter
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
        foreach ($this->content->data as $language => $fields) {
            $locale = explode('-', \eZLocale::instance($language)->httpLocaleCode());
            $locale = substr(strtolower($locale[0]), 0, 2);
            foreach ($fields as $field) {
                list($classIdentifier, $fieldIdentifier) = explode('/', $field['identifier']);
                if (!empty($field['content'])) {
                    $this->contentData[$fieldIdentifier][$locale] = $field;
                }
            }
        }

        $this->context = new \ArrayObject();
    }

    /**
     * @throws \Exception
     */
    protected function convert()
    {
        if (!$this->isConverted) {

            $this->doc['@id'] = $this->getId();
            $this->doc['@type'] = $this->getType();
            $this->convertFields();

            $this->isConverted = true;
        }
    }

    private function getId()
    {
        return ConverterHelper::generateId($this->concept, $this->content->metadata->id);
    }

    /**
     * @return array|mixed
     * @throws \Exception
     */
    private function getType()
    {
        if (isset($this->map->getFlatMapping()['_class'])) {
            $mappedTypes = array_values($this->map->getFlatMapping()['_class']);
            $types = [];
            foreach ($mappedTypes as $type){
                $types[] = ConverterHelper::compactUri($type, $this->context);
            }

            if (count($types) == 1) {
                return array_shift($types);
            }

            return $types;
        }

        throw new \Exception("Type not found for {$this->concept}/{$this->content->metadata->id}");
    }

    private function convertFields()
    {
        $flatMapping = $this->map->getFlatMapping();
        foreach ($this->contentData as $fieldIdentifier => $dataByLanguage){
            if (isset($flatMapping[$fieldIdentifier])){
                foreach ($flatMapping[$fieldIdentifier] as $uri){
                    $convertedField = $this->convertField($dataByLanguage, $uri);
                    if ($convertedField) {
                        $this->doc[ConverterHelper::compactUri($uri, $this->context)] = $convertedField;
                    }
                }
            }
        }

        $currentOrganizationDataByLanguage = $this->getCurrentOrganizationDataByLanguage();
        if ($currentOrganizationDataByLanguage) {
            if (isset($flatMapping['_current_organization'])) {
                foreach ($flatMapping['_current_organization'] as $uri) {
                    $convertedField = $this->convertField($currentOrganizationDataByLanguage, $uri);
                    if ($convertedField) {
                        $this->doc[ConverterHelper::compactUri($uri, $this->context)] = $convertedField;
                    }
                }
            }
        }
    }

    private function getCurrentOrganizationDataByLanguage()
    {
        $data = [];
        $homepage = \eZContentObjectTreeNode::fetch((int)\eZINI::instance('content.ini')->variable('NodeSettings', 'RootNode'));
        if ($homepage instanceof \eZContentObjectTreeNode){
            $homeContent = Content::createFromEzContentObject($homepage->object());
            foreach ($homeContent->data as $language => $fields){
                $locale = explode('-', \eZLocale::instance($language)->httpLocaleCode());
                $locale = substr(strtolower($locale[0]), 0, 2);
                foreach ($fields as $field) {
                    list($classIdentifier, $fieldIdentifier) = explode('/', $field['identifier']);
                    if ($fieldIdentifier == 'current_organization') {
                        if (!empty($field['content'])) {
                            $data[$locale] = $field;
                        }
                    }
                }
            }
        }

        return $data;
    }

    private function convertField($dataByLanguage, $uri)
    {
        $properties = isset($this->map->getProperties()[$uri]) ? $this->map->getProperties()[$uri] : [];
        $fieldDefinition = $this->getFieldDefinition($dataByLanguage);

        $fieldConverter = FieldConverterFactory::factory($properties, $fieldDefinition, $this->context);
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