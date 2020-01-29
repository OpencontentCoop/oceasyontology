<?php

use Opencontent\Easyontology\MapperRegistry;

/** @var eZModule $module */
$module = $Params["Module"];

/** @var eZModule $module */
$module = $Params["Module"];
$classIdentifier = $Params["ClassIdentifier"];
$mapSlug = $Params["MapSlug"];
$http = eZHTTPTool::instance();


try {
    $collection = MapperRegistry::fetchMapCollectionByClassIdentifier($classIdentifier);
    $map = $collection->findMapBySlug($mapSlug);
    if (!$map){
        throw new Exception('Map not found');
    }

    $collection->removeMap($map);
    MapperRegistry::storeMapCollection($collection);

    if (!$collection->hasMaps()){
        MapperRegistry::removeMapCollection($classIdentifier);
    }

    return $module->redirectTo('easyontology/dashboard');

}catch (Exception $e){
    eZDebug::writeError($e->getMessage(), __FILE__);
    return $module->handleError(eZError::KERNEL_NOT_FOUND, 'kernel');
}

//\Opencontent\Easyontology\MapperRegistry::removeMapCollection();
//\Opencontent\Easyontology\MapperRegistry::removeOntologyCollection();

