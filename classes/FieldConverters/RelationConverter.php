<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\ConverterHelper;
use Opencontent\Easyontology\Map;
use Opencontent\Easyontology\MapperRegistry;

class RelationConverter extends AbstractFieldConverter
{
    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content)
    {
        $values = [];
        foreach ($dataByLanguage as $locale => $data){
            foreach ($data['content'] as $content) {
                if (isset($values[$content['id']])){
                    continue;
                }
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
        $map = $this->getMap($content['classIdentifier']);
        if ($map instanceof Map) {
            $converter = \Opencontent\Easyontology\ConverterFactory::factory($map->getSlug(), $content['id']);
            $newContext = array_merge($this->context->getArrayCopy(), $converter->getContext()->getArrayCopy());
            $this->context->exchangeArray($newContext);

            return $converter->getDoc();
        }

        return false;
    }

    /**
     * Predilige le mappature con prefisso uguale al range richiesto
     * @param $classIdentifier
     * @return bool|Map
     */
    private function getMap($classIdentifier)
    {
        $currentPrefix = ConverterHelper::getUriPrefix($this->rdfRange);
        $mapCompareList = [];
        try {
            $collection = MapperRegistry::fetchMapCollectionByClassIdentifier($classIdentifier);
            foreach ($collection->getMaps() as $map) {
                if ($map->hasClassUri($this->rdfRange)) {
                    $prefixes = $map->getClassUriPrefixList();
                    $match = 0;
                    foreach ($prefixes as $prefix){
                        if ($prefix == $currentPrefix){
                            $match++;
                        }else{
                            $match--;
                        }
                    }
                    $mapCompareList[] = [
                        'map' => $map,
                        'match_prefix' => $match
                    ];
                }
            }
        }catch (\Exception $e){
            \eZDebug::writeError($e->getMessage(), __METHOD__ . '#' . $e->getLine());
        }

        if (empty($mapCompareList)){
            return false;
        }

        if (count($mapCompareList) == 1){
            return $mapCompareList[0]['map'];
        }

        usort($mapCompareList, function($a, $b) {
            return $a['match_prefix'] < $b['match_prefix'] ? 1 : -1;
        });

        return $mapCompareList[0]['map'];
    }

}