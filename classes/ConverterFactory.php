<?php

namespace Opencontent\Easyontology;


class ConverterFactory
{
    /**
     * @param $concept
     * @param $id
     * @return ConverterInterface|MapConverter|null
     * @throws \Opencontent\Opendata\Api\Exception\NotFoundException
     * @throws \Exception
     */
    public static function factory($concept, $id)
    {
        $converter = null;

        $ini = \eZINI::instance('easyontology.ini');
        $attributeConverters = $ini->variable('Converters', 'AvailableCustomConverters');
        if (isset($attributeConverters[$concept])){
            $converter = self::instanceCustomConverter($attributeConverters[$concept], $concept, $id);
        }else{
            $object = \eZContentObject::fetch((int)$id);
            if ($object instanceof \eZContentObject){
                $classIdentifier = $object->attribute('class_identifier');
                $collection = MapperRegistry::fetchMapCollectionByClassIdentifier($classIdentifier);
                $map = $collection->findMapBySlug($concept);
                if ($map instanceof Map) {
                    $converter = new MapConverter($map, $concept, $object);
                }else{
                    throw new \Exception("Map {$concept} non found");
                }
            }else{
                throw new \Exception("Object {$id} non found");
            }
        }

        if ($converter instanceof ConverterInterface){
            return $converter;
        }

        throw new \Exception("Can not find converter for {$concept}/{$id}");
    }

    /**
     * @return ConverterInterface
     */
    protected static function instanceCustomConverter($converterClass, $concept, $id)
    {
        return new $converterClass($concept, $id);
    }
}