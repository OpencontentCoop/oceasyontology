<?php

namespace Opencontent\Easyontology;

class MapperRegistry
{
    const MAP_PREFIX = 'eo_map_';

    const ONTOLOGY_PREFIX = 'eo_ontology_';

    /**
     * @return MapCollection[]
     */
    public static function fetchMapCollectionList()
    {
        $collections = [];
        $db = \eZDB::instance();
        $rows = $db->arrayQuery('SELECT * FROM ezsite_data WHERE name LIKE \'' . self::MAP_PREFIX . '%\'');
        $siteDataList = \eZPersistentObject::handleRows($rows, 'eZSiteData', true);
        foreach ($siteDataList as $siteData) {
            $collections[] = MapCollection::createFromJsonString($siteData->attribute('value'));
        }

        return $collections;
    }

    public static function removeMapCollection($classIdentifier = null)
    {
        $siteDataList = [];
        if (!$classIdentifier) {
            $db = \eZDB::instance();
            $db->arrayQuery('DELETE FROM ezsite_data WHERE name LIKE \'' . self::MAP_PREFIX . '%\'');
        }else{
            $siteData = \eZSiteData::fetchByName(self::MAP_PREFIX . $classIdentifier);
            if ($siteData) {
                $siteDataList = [$siteData];
            }
        }

        foreach ($siteDataList as $siteData) {
            $siteData->remove();
        }
    }

    /**
     * @param $classIdentifier
     * @return MapCollection
     */
    public static function fetchMapCollectionByClassIdentifier($classIdentifier)
    {
        $siteData = \eZSiteData::fetchByName(self::MAP_PREFIX . $classIdentifier);
        if ($siteData) {
            $collection = MapCollection::createFromJsonString($siteData->attribute('value'));
        } else {
            $collection = MapCollection::createFromArray([
                'classIdentifier' => $classIdentifier,
                'maps' => []
            ]);
        }

        return $collection;
    }

    public static function storeMapCollection(MapCollection $collection)
    {
        $siteData = \eZSiteData::fetchByName(self::MAP_PREFIX . $collection->getClassIdentifier());
        if (!$siteData) {
            $siteData = new \eZSiteData(array(
                'name' => self::MAP_PREFIX . $collection->getClassIdentifier(),
                'value' => ''
            ));
        }
        $siteData->setAttribute('value', json_encode($collection));
        $siteData->store();
    }

    public static function fetchOntologyCollection()
    {
        $collections = [];
        $db = \eZDB::instance();
        $rows = $db->arrayQuery('SELECT * FROM ezsite_data WHERE name LIKE \'' . self::ONTOLOGY_PREFIX . '%\'');
        $siteDataList = \eZPersistentObject::handleRows($rows, 'eZSiteData', true);
        foreach ($siteDataList as $siteData) {
            $collections[] = Ontology::createFromJsonString($siteData->attribute('value'));
        }

        return $collections;
    }

    public static function removeOntologyCollection()
    {
        $db = \eZDB::instance();
        $db->arrayQuery('DELETE FROM ezsite_data WHERE name LIKE \'' . self::ONTOLOGY_PREFIX . '%\'');
    }

    public static function fetchOntologyByUri($uri)
    {
        $slug = self::slugify($uri);

        return self::fetchOntologyBySlug($slug);
    }

    public static function fetchOntologyBySlug($slug)
    {
        $siteData = \eZSiteData::fetchByName(self::ONTOLOGY_PREFIX . $slug);
        if ($siteData){
            return Ontology::createFromJsonString($siteData->attribute('value'));
        }

        return false;
    }

    public static function storeOntology(Ontology $ontology)
    {
        $siteData = \eZSiteData::fetchByName(self::ONTOLOGY_PREFIX . $ontology->getSlug());
        if (!$siteData) {
            $siteData = new \eZSiteData(array(
                'name' => self::ONTOLOGY_PREFIX . $ontology->getSlug(),
                'value' => ''
            ));
        }
        $siteData->setAttribute('value', json_encode($ontology));
        $siteData->store();
    }

    public static function slugify($string)
    {
        //$trans = \eZCharTransform::instance();
        //return $trans->transformByGroup($string, 'identifier');

        return md5($string);
    }
}