<?php

use Opencontent\Easyontology\MapperRegistry;
use Opencontent\Easyontology\Ontology;

/** @var eZModule $module */
$module = $Params["Module"];
$http = eZHTTPTool::instance();

if ($http->hasPostVariable('classIdentifier')) {
    $module->redirectTo('easyontology/mapper/' . eZHTTPTool::instance()->postVariable('classIdentifier') . '/new');
}

if ($http->hasPostVariable('source')) {
    $ontology = new Ontology();
    $ontology->setSource($http->postVariable('source'), $http->postVariable('format', 'rdfxml'));
    MapperRegistry::storeOntology($ontology);

    return $module->redirectTo('easyontology/dashboard/');
}

$tpl = eZTemplate::factory();
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
$Result['left_menu'] = false;
$Result['path'] = array(array('url' => false, 'text' => 'Easy ontology dashboard'));