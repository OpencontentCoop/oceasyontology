<?php

/** @var eZModule $module */
$module = $Params["Module"];
$concept = $Params["Concept"];
$id = $Params["ID"];

try{
    $converter = \Opencontent\Easyontology\ConverterFactory::factory($concept, $id);
    header( 'HTTP/1.1 200 OK' );
    $data = $converter->jsonSerialize();
}catch (Exception $e){
    header( 'HTTP/1.1 500 Internal Server Error' );
    $data = array( 'error' => $e->getMessage() );
}

header('Content-Type: application/json');
echo json_encode( $data );
eZExecution::cleanExit();
