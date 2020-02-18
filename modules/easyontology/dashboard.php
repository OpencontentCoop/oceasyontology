<?php

use Opencontent\Easyontology\MapperRegistry;
use Opencontent\Easyontology\Ontology;

/** @var eZModule $module */
$module = $Params["Module"];
$http = eZHTTPTool::instance();
$tpl = eZTemplate::factory();

if ($http->hasPostVariable('classIdentifier')) {
    $module->redirectTo('easyontology/mapper/' . eZHTTPTool::instance()->postVariable('classIdentifier') . '/new');
}

if ($http->hasPostVariable('source')) {
    try {
        $ontology = new Ontology();
        $ontology->setSource($http->postVariable('source'), $http->postVariable('format', 'rdfxml'));
        MapperRegistry::storeOntology($ontology);

        return $module->redirectTo('easyontology/dashboard/');
    }catch (Exception $e){
        $tpl->setVariable('error', $e->getMessage());
    }
}

$collections = MapperRegistry::fetchMapCollectionList();
$ontologies = MapperRegistry::fetchOntologyCollection();
$alreadyMapped = [];
foreach ($collections as $collection){
    $alreadyMapped[] = $collection->getClassIdentifier();
}
$tpl->setVariable('already_mapped', $alreadyMapped);
$tpl->setVariable('collections', json_decode(json_encode($collections), true));
$tpl->setVariable('ontologies', json_decode(json_encode($ontologies), true));

$Result = array();
$Result['content'] = $tpl->fetch('design:easyontology/dashboard.tpl');
$Result['path'] = array(array('url' => false, 'text' => 'Easy ontology dashboard'));