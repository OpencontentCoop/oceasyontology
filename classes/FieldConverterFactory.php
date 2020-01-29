<?php

namespace Opencontent\Easyontology;


use Opencontent\Easyontology\FieldConverters\LiteralConverter;

class FieldConverterFactory
{

    const RDF_LITERAL = "http://www.w3.org/2000/01/rdf-schema#Literal";

    const SCHEMA_TEXT = "http://schema.org/Text";

    const SCHEMA_INTEGER = "http://schema.org/Integer";

    const SCHEMA_BOOLEAN = "http://schema.org/Boolean";

    const SCHEMA_PROPERTY = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property';

    const OWL_DATATYPE_PROPERTY = "http://www.w3.org/2002/07/owl#DatatypeProperty";

    const OWL_OBJECT_PROPERTY = "http://www.w3.org/2002/07/owl#ObjectProperty";

    /**
     * @param $properties
     * @param $fieldDefinition
     * @return FieldConverterInterface|bool
     */
    public static function factory($properties, $fieldDefinition)
    {
        $rdfRange = false;
        if (isset($properties['schema:rangeIncludes'])) {
            $rdfRange = $properties['schema:rangeIncludes'];
        }
        if (isset($properties['rdfs:range'])) {
            $rdfRange = $properties['rdfs:range'];
        }

        if (!isset($properties['rdf:type'])
            || (isset($properties['rdf:type']) && $properties['rdf:type'] == self::OWL_DATATYPE_PROPERTY)
            || ($rdfRange == self::RDF_LITERAL)
            || ($rdfRange == self::SCHEMA_TEXT)
            || ($rdfRange == self::SCHEMA_INTEGER)
            || ($rdfRange == self::SCHEMA_BOOLEAN)
        ) {
            return new LiteralConverter($fieldDefinition, $rdfRange);
        }

        if (isset($properties['rdf:type'])
            && (($rdfRange && $properties['rdf:type'] == self::OWL_OBJECT_PROPERTY)
                || ($rdfRange && $properties['rdf:type'] == self::SCHEMA_PROPERTY))
        ) {
            $ini = \eZINI::instance('easyontology.ini');
            $fieldConverters = $ini->variable('Converters', 'FieldConverters');

            $fieldDatatype = $fieldDefinition['datatype'];
            $className = false;

            if (isset($fieldConverters["{$fieldDatatype}-{$rdfRange}"])) {
                $className = $fieldConverters["{$fieldDatatype}-{$rdfRange}"];
            }

            if (isset($fieldConverters["{$fieldDatatype}-*"])) {
                $className = $fieldConverters["{$fieldDatatype}-*"];
            }

            if ($className) {
                return new $className($fieldDefinition, $rdfRange);
            }
        }

        return false;
    }
}