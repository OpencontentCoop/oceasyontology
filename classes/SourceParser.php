<?php

namespace Opencontent\Easyontology;


class SourceParser
{
    private $isParsed;

    private $ontology = [];

    private $properties = [];

    private $classes = [];

    private $uri;

    private $format;

    public function __construct($uri, $format = 'rdfxml')
    {
        $this->uri = $uri;
        $this->format = $format;
    }

    public function parse()
    {
        $graph = \EasyRdf\Graph::newAndLoad($this->uri, $this->format);

        /** @var \EasyRdf\Resource $resource */
        foreach ($graph->resources() as $subject => $resource) {
            if (!$resource->isBNode()) {
                if ($resource->hasProperty('rdf:type')
                    && in_array($resource->get('rdf:type'), [
                        'http://www.w3.org/2002/07/owl#DatatypeProperty',
                        'http://www.w3.org/2002/07/owl#ObjectProperty',
                        'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property',
                        'http://www.w3.org/2002/07/owl#Ontology'
                    ])
                ) {
                    $properties = [
                        'uri_basename' => basename($resource->getUri())
                    ];
                    foreach ($resource->properties() as $property) {
                        $properties[$property] = (string)$resource->get($property);
                    }
                    if ((string)$resource->get('rdf:type') == 'http://www.w3.org/2002/07/owl#Ontology' && !$this->ontology){
                        $properties['uri'] = $resource->getUri();
                        $this->ontology = $properties;
                    }else {
                        $this->properties[$resource->getUri()] = $properties;
                    }
                }
                if (($resource->hasProperty('rdf:type') && in_array($resource->get('rdf:type'), ['http://www.w3.org/2002/07/owl#Class'])
                    or $resource->hasProperty('rdfs:subClassOf'))
                ) {
                    $properties = [
                        'uri_basename' => basename($resource->getUri())
                    ];
                    foreach ($resource->properties() as $property) {
                        $properties[$property] = (string)$resource->get($property);
                    }
                    $this->classes[$resource->getUri()] = $properties;
                }
            }
        }

        if (!isset($this->ontology['uri'])){
            $this->ontology['uri'] = $this->uri;
        }
        $this->isParsed = true;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        if (!$this->isParsed){
            $this->parse();
        }
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getOntology()
    {
        if (!$this->isParsed){
            $this->parse();
        }
        return $this->ontology;
    }

    /**
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }


}