<?php

use Opencontent\Easyontology\MapperRegistry;
use Opencontent\Easyontology\Map;

/** @var eZModule $module */
$module = $Params["Module"];
$classIdentifier = $Params["ClassIdentifier"];
$mapSlug = $Params["MapSlug"];
$http = eZHTTPTool::instance();
$tpl = eZTemplate::factory();

$contentClass = eZContentClass::fetchByIdentifier($classIdentifier);
if (!$contentClass instanceof eZContentClass) {
    return $Module->handleError(eZError::KERNEL_NOT_FOUND, 'kernel');
}

$newSlug = false;
if ($http->hasPostVariable('slug') && $http->postVariable('slug') !== 'new') {
    $newSlug = eZCharTransform::instance()->transformByGroup($http->postVariable('slug'), 'urlalias');
}

$newMapping = false;
if ($http->hasPostVariable('mapping')) {
    $mapping = (array)$http->postVariable('mapping');
    foreach ($mapping as $field => $uri){
        if (empty($uri)){
            unset($mapping[$field]);
        }
    }
    $newMapping = $mapping;
}
$addOntology = false;
if ($http->hasPostVariable('ontology')) {
    $addOntology = MapperRegistry::fetchOntologyBySlug($http->postVariable('ontology'));
}

try {
    $collection = MapperRegistry::fetchMapCollectionByClassIdentifier($classIdentifier);
    if ($mapSlug !== 'new') {
        $map = $collection->findMapBySlug($mapSlug);
        if (!$map instanceof Map) {
            return $module->handleError(eZError::KERNEL_NOT_FOUND, 'kernel');
        }
    } else {
        $map = new Map();
        $map->setSlug('new');
    }

    if ($newSlug && $newSlug != $map->getSlug()){
        if ($collection->hasMap($map)){
            $map = $collection->renameMap($map, $newSlug);
            $map->setSlug($newSlug);
        }else {
            $map->setSlug($newSlug);
            $collection->addMap($map);
        }
    }

    if ($newMapping){
        $map->setMapping($mapping);
    }
    if ($addOntology){
        $map->addOntology($addOntology);
    }

    if ($newSlug || $newMapping || $addOntology){
        if (!$collection->hasMap($map)){
            $map->setSlug('temp-' . rand(1,10));
            $collection->addMap($map);
        }
        MapperRegistry::storeMapCollection($collection);
        if ($http->hasPostVariable('Store')) {
            return $module->redirectTo('easyontology/mapped/' . $classIdentifier . '/' . $map->getSlug());
        }else{
            return $module->redirectTo('easyontology/mapper/' . $classIdentifier . '/' . $map->getSlug());
        }
    }

    if (empty($map->getMapping())){

        $mapping = [];
        $mapProperties = array_keys($map->getProperties());
        $mapGroupedProperties = $map->getGroupedProperties();
        foreach ($collection->getClassSchema()['fields'] as $field){
            $identifier = $field['identifier'];
            $identifierCamelized = Map::camelize($identifier);
            foreach ($mapGroupedProperties as $onto => $mapProperties) {
                foreach ($mapProperties as $uri => $mapProperty) {
                    if (basename($uri) == $identifierCamelized) {
                        $mapping[$identifier][$onto][] = $uri;
                    }
                }
            }
        }

        $map->setMapping($mapping);
    }

    $tpl->setVariable('class', $contentClass);
    $tpl->setVariable('collection', $collection->jsonSerialize());
    $tpl->setVariable('ontologies', json_decode(json_encode(MapperRegistry::fetchOntologyCollection()), true));

    $mapArray = $map->jsonSerialize();
    $mapArray['properties'] = $map->getProperties();
    $mapArray['grouped_properties'] = $map->getGroupedProperties();
    $mapArray['classes'] = $map->getClasses();
    $tpl->setVariable('map', $mapArray);

}catch (Exception $e){
    $tpl->setVariable('error', $e->getMessage());
}

$tpl->setVariable('locale', eZLocale::currentLocaleCode());


$Result = array();
$Result['content'] = $tpl->fetch('design:easyontology/mapper.tpl');
$Result['left_menu'] = false;
$Result['path'] = array(
    array('url' => 'easyontology/dashboard', 'text' => 'Easy ontology dashboard'),
    array('url' => false, 'text' => $contentClass->attribute('name') . ' mapper')
);
