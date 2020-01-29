<?php

/** @var eZModule $module */
$module = $Params["Module"];

\Opencontent\Easyontology\MapperRegistry::removeMapCollection();
//\Opencontent\Easyontology\MapperRegistry::removeOntologyCollection();

return $module->redirectTo('easyontology/dashboard');