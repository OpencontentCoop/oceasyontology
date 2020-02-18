<?php
require 'autoload.php';

$script = eZScript::instance(array(
    'description' => ("Dump data"),
    'use-session' => false,
    'use-modules' => true,
    'use-extensions' => true
));

$script->startup();

$options = $script->getOptions('[message:]',
    '',
    array(
        'message' => 'Dump message',
    )
);
$script->initialize();
$script->setUseDebugAccumulators(true);

$baseDir = 'extension/oceasyontology/data/';

$collections = \Opencontent\Easyontology\MapperRegistry::fetchMapCollectionList();
$ontologies = \Opencontent\Easyontology\MapperRegistry::fetchOntologyCollection();

$directory = $baseDir . time();
eZDir::mkdir($directory, false, true);

if ($options['message']) {
    eZFile::create('message.txt', $directory, $options['message']);
}

eZFile::create('collections.json', $directory, json_encode($collections));
eZFile::create('ontologies.json', $directory, json_encode($ontologies));

$script->shutdown();