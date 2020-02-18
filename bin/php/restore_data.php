<?php
require 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance(array(
    'description' => ("Restore data"),
    'use-session' => false,
    'use-modules' => true,
    'use-extensions' => true
));

$script->startup();

$options = $script->getOptions('[id:][list]',
    '',
    array(
        'id' => 'Restore dump id',
        'list' => "List dump id"
    )
);
$script->initialize();
$script->setUseDebugAccumulators(true);

$baseDir = 'extension/oceasyontology/data/';

if ($options['list']) {

    $items = eZDir::findSubitems($baseDir, 'd');
    rsort($items);
    foreach ($items as $item){
        $cli->warning("dump_id $item");
        $cli->output('Date: ' . date('r', $item));
        if (file_exists($baseDir . $item . '/message.txt')){
            $cli->output('    ' . file_get_contents($baseDir . $item . '/message.txt'));
        }
        $cli->output();
    }

}elseif ($options['id']) {
    $directory = $baseDir . $options['id'];
    if (is_dir($directory)) {
        $collections = json_decode(file_get_contents($directory . '/collections.json'), true);
        $ontologies = json_decode(file_get_contents($directory . '/ontologies.json'), true);

        foreach ($ontologies as $ontology) {
            $ontologyObj = \Opencontent\Easyontology\Ontology::createFromArray($ontology);
            $cli->output("Import ontology " . $ontologyObj->getUri());
            \Opencontent\Easyontology\MapperRegistry::storeOntology($ontologyObj);
        }

        foreach ($collections as $collection) {
            $collectionObj = \Opencontent\Easyontology\MapCollection::createFromArray($collection);
            $cli->output("Import map for " . $collectionObj->getClassIdentifier());
            \Opencontent\Easyontology\MapperRegistry::storeMapCollection($collectionObj);
        }
    }
}


$script->shutdown();