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

$tpl->setVariable('class', $contentClass);

try {
    $collection = MapperRegistry::fetchMapCollectionByClassIdentifier($classIdentifier);
    $map = $collection->findMapBySlug($mapSlug);

    $mapArray = $map->jsonSerialize();
    $mapArray['properties'] = $map->getProperties();
    $mapArray['grouped_properties'] = $map->getGroupedProperties();
    $mapArray['classes'] = $map->getClasses();
    $mapArray['flat_mapping'] = $map->getFlatMapping();
    $tpl->setVariable('map', $mapArray);

}catch (Exception $e){
    $tpl->setVariable('error', $e->getMessage());
}

$tpl->setVariable('collection', $collection->jsonSerialize());
$tpl->setVariable('locale', eZLocale::currentLocaleCode());


$Result = array();
$Result['content'] = $tpl->fetch('design:easyontology/mapped.tpl');
$Result['left_menu'] = false;
$Result['path'] = array(
    array('url' => 'easyontology/dashboard', 'text' => 'Easy ontology dashboard'),
    array('url' => false, 'text' => $contentClass->attribute('name'))
);