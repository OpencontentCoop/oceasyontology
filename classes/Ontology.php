<?php

namespace Opencontent\Easyontology;


class Ontology implements \JsonSerializable
{
    private $source;

    private $source_format;

    private $uri;

    private $slug;

    private $ontology = [];

    private $properties = [];

    private $classes = [];

    public static function createFromJsonString($string)
    {
        return self::createFromArray(json_decode($string, true));
    }

    public static function createFromArray(array $data)
    {
        $ontology = new Ontology();
        foreach ($data as $key => $value){
            if (property_exists($ontology, $key)){
                $ontology->{$key} = $value;
            }
        }

        return $ontology;
    }

    /**
     * @return array
     */
    public function getOntology(): array
    {
        return $this->ontology;
    }

    /**
     * @param array $ontology
     */
    public function setOntology(array $ontology)
    {
        $this->ontology = $ontology;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param mixed $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return mixed
     */
    public function getSourceFormat()
    {
        return $this->source_format;
    }

    /**
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param $source
     * @param string $format
     */
    public function setSource($source, $format = 'rdfxml')
    {
        $parser = new SourceParser($source, $format);
        $this->source = $source;
        $this->source_format = $format;

        $this->ontology = $parser->getOntology();
        $this->properties = $parser->getProperties();
        $this->classes = $parser->getClasses();
        if (isset($this->ontology['uri'])) {
            $this->uri = $this->ontology['uri'];
        }else{
            $this->uri = $source;
        }

        $this->slug = MapperRegistry::slugify($this->uri);

//        if (isset($this->ontology['dc:title'])) {
//            $this->name = $this->ontology['dc:title'];
//        }
//        if (isset($this->ontology['dc:identifier'])) {
//            $this->slug = $this->ontology['dc:identifier'];
//        }
//
    }

    public function jsonSerialize()
    {
        return [
            'source' => $this->source,
            'source_format' => $this->source_format,
            'uri' => $this->uri,
            'properties' => $this->properties,
            'classes' => $this->classes,
            'ontology' => $this->ontology,
            'slug' => $this->slug,
        ];
    }
}