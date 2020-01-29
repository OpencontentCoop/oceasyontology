<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\Map;
use Opencontent\Easyontology\MapConverter;
use Opencontent\Easyontology\MapperRegistry;

class RelationConverter extends AbstractFieldConverter
{
    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content)
    {
        foreach ($dataByLanguage as $locale => $data){
            foreach ($data['content'] as $content) {
                $uri = $this->getUri($content);
                if ($uri) {
                    $values[$content['id']] = ['@id' => $uri];
                }
            }
        }

        return empty($values) ? false : array_values($values);
    }

    private function getUri($content)
    {
        $collection = MapperRegistry::fetchMapCollectionByClassIdentifier($content['classIdentifier']);
        foreach ($collection->getMaps() as $map){
            if (in_array($this->rdfRange, $map->getFlatMapping()['_class'])){
                return MapConverter::generateId($map->getSlug(), $content['id']);
            }
        }

        return false;
    }

}