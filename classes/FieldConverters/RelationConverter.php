<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\Map;
use Opencontent\Easyontology\MapperRegistry;

class RelationConverter extends AbstractFieldConverter
{
    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content)
    {
        foreach ($dataByLanguage as $locale => $data){
            foreach ($data['content'] as $content) {
                $doc = $this->getDoc($content);
                if ($doc) {
                    $values[$content['id']] = $doc;
                }
            }
        }

        return empty($values) ? false : array_values($values);
    }

    private function getDoc($content)
    {
        try {
            $collection = MapperRegistry::fetchMapCollectionByClassIdentifier($content['classIdentifier']);
            foreach ($collection->getMaps() as $map) {
                if (in_array($this->rdfRange, $map->getFlatMapping()['_class'])) {
                    $converter = \Opencontent\Easyontology\ConverterFactory::factory($map->getSlug(), $content['id']);
                    $newContext = array_merge($this->context->getArrayCopy(), $converter->getContext()->getArrayCopy());
                    $this->context->exchangeArray($newContext);

                    return $converter->getDoc();

                }
            }
        }catch (\Exception $e){
            \eZDebug::writeError($e->getMessage(), __METHOD__ . '#' . $e->getLine());
        }

        return false;
    }

}