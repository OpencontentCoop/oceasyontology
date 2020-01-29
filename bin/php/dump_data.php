<?php
require 'autoload.php';

$script = eZScript::instance(array(
    'description' => ("Dump data"),
    'use-session' => false,
    'use-modules' => true,
    'use-extensions' => true
));

$script->startup();

$options = $script->getOptions('[class:][attribute:]',
    '',
    array(
        'class' => 'Identificatore della classe',
        'attribute' => "Identificatore dell'attributo"
    )
);
$script->initialize();
$script->setUseDebugAccumulators(true);

$collections = \Opencontent\Easyontology\MapperRegistry::fetchMapCollectionList();
$ontologies = \Opencontent\Easyontology\MapperRegistry::fetchOntologyCollection();

$directory = 'extension/oceasyontology/data/' . time();
eZDir::mkdir($directory, false, true);

eZFile::create('collections.json', $directory, json_encode($collections));
eZFile::create('ontologies.json', $directory, json_encode($ontologies));

$script->shutdown();