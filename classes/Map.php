<?php

namespace Opencontent\Easyontology;

class Map implements \JsonSerializable
{
    private $name;

    private $slug;

    private $ontologies = [];

    private $mapping = [];

    //private $groups = [];

    private $properties;

    private $classes;

    private $flatMapping;

    public static function createFromArray(array $data)
    {
        $map = new Map();
        foreach ($data as $key => $value) {
            if (property_exists($map, $key)) {
                $map->{$key} = $value;
            }
        }

        return $map;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'ontologies' => $this->ontologies,
            'mapping' => $this->mapping,
            //'groups' => $this->groups,
        ];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @param Ontology $ontology
     * @param bool $asImport
     */
    public function addOntology(Ontology $ontology)
    {
        if (empty($this->ontologies)) {

            $ontologyDescription = $ontology->getOntology();
            if (isset($ontologyDescription['dc:title'])) {
                $this->name = $ontologyDescription['dc:title'];
            }
        }
        $this->ontologies[] = $ontology->getUri();
    }

    /**
     * @return mixed
     */
    public function getOntologies()
    {
        return $this->ontologies;
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @param array $mapping
     */
    public function setMapping(array $mapping)
    {
        $this->mapping = $mapping;
    }

//    public function getGroups(): array
//    {
//        return $this->groups;
//    }

//    public function addGroup($identifier, $group)
//    {
//        $this->groups[$identifier] = $group;
//    }


//    public function setGroups(array $groups)
//    {
//        $this->groups = $groups;
//    }

    public function getFlatMapping()
    {
        if ($this->flatMapping === null) {
            $this->flatMapping = [];
            foreach ($this->mapping as $field => $onto) {
                foreach ($onto as $uris) {
                    foreach ($uris as $uri) {
                        $this->flatMapping[$field][$uri] = $uri;
                    }
                }
            }
        }

        return $this->flatMapping;
    }

    public function hasClassUri($uri)
    {
        $classUriList = $this->getFlatMapping()['_class'];

        return in_array($uri, $classUriList);
    }

    public function getClassUriPrefixList()
    {
        $classUriList = $this->getFlatMapping()['_class'];
        $prefixList = [];
        foreach ($classUriList as $classUri){
            $prefixList[] = ConverterHelper::getUriPrefix($classUri);
        }

        return $prefixList;
    }

    public static function camelize($string, $delimiter = '_')
    {
        $exploded = explode($delimiter, $string);
        $explodedCamelized = array_map('ucwords', $exploded);

        return lcfirst(implode('', $explodedCamelized));
    }

    public function getProperties()
    {
        $properties = $this->getGroupedProperties();
        $flatProperties = [];
        foreach ($properties as $uri => $propertyList) {
            foreach ($propertyList as $propertyName => $propertyValues){
                if (isset($flatProperties[$propertyName])){
                    $flatProperties[$propertyName] = array_merge($flatProperties[$propertyName], $propertyValues);
                }else{
                    $flatProperties[$propertyName] = $propertyValues;
                }
            }
        }

        return $flatProperties;
    }

    public function getGroupedProperties()
    {
        if ($this->properties === null) {
            $this->properties = [];
            foreach ($this->ontologies as $uri) {
                $ontology = MapperRegistry::fetchOntologyByUri($uri);
                if ($ontology) {
                    $properties = $ontology->getProperties();
                    $this->properties[$uri] = $properties;
                }
            }
        }

        return $this->properties;
    }

    public function getClasses()
    {
        if ($this->classes === null) {
            $this->classes = [];
            foreach ($this->ontologies as $uri) {
                $ontology = MapperRegistry::fetchOntologyByUri($uri);
                if ($ontology) {
                    $this->classes[$uri] = $ontology->getClasses();
                }
            }
        }

        return $this->classes;
    }
}