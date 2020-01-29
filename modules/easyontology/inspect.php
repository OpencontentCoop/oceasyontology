<?php

use Opencontent\Easyontology\MapperRegistry;
use Opencontent\Easyontology\Ontology;

/** @var eZModule $module */
$module = $Params["Module"];
$slug = $Params["Slug"];
$http = eZHTTPTool::instance();
$tpl = eZTemplate::factory();

try {
    $ontology = MapperRegistry::fetchOntologyBySlug($slug);
    if (!$ontology instanceof Ontology){
        throw new Exception('Not found');
    }

    $graph = \EasyRdf\Graph::newAndLoad($ontology->getSource(), $ontology->getSourceFormat());
    echo $graph->dump();
    eZExecution::cleanExit();


}catch (Exception $e){
    return $module->handleError(eZError::KERNEL_NOT_AVAILABLE, 'kernel');
}