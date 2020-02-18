<?php

/** @var eZModule $module */
$module = $Params["Module"];
$concept = $Params["Concept"];
$id = $Params["ID"];

$suffix = false;
if (strpos($id, '.') !== false){
    $suffix = eZFile::suffix($id);
    $id = str_replace('.' . $suffix, '', $id);
}

$headersOptions = $headers = [];
$outputFormatOptions = [];
$error = false;
/** @var \EasyRdf\Format $format */
foreach (\EasyRdf\Format::getFormats() as $format) {
    if ($format->getSerialiserClass()) {
        $outputFormatOptions[$format->getLabel()] = $format->getName();
        $headersOptions[$format->getLabel()] = array_keys($format->getMimeTypes());
        $headers = array_merge($headers, array_keys($format->getMimeTypes()));
    }
}

$requestAccept = strtolower($_SERVER['HTTP_ACCEPT']);
$getFormat = isset($_GET['format']) ? $_GET['format'] : null;
$getDebug = isset($_GET['debug']) ? true : false;
$getEncode = isset($_GET['encode']) ? true : false;

switch ($suffix){
    case 'php':
    case 'json':
    case 'jsonld':
    case 'dot':
    case 'n3':
        $getFormat = $suffix;
        break;
    case 'nt':
        $getFormat = 'ntriples';
        break;
    case 'ttl':
        $getFormat = 'turtle';
        break;
    case 'rdf':
        $getFormat = 'rdfxml';
        break;
}

if ($requestAccept == 'application/ld+json' || $getFormat == 'jsonld'){
    $getDebug = true;
}

try {
    $converter = \Opencontent\Easyontology\ConverterFactory::factory($concept, $id);
    $jsonData = $converter->jsonSerialize();

    if ($getDebug) {
        if (!$getEncode) {
            header('Content-Type: application/json');
        }
        print json_encode($jsonData);
        eZExecution::cleanExit();
    }

    $uri = \Opencontent\Easyontology\ConverterHelper::generateId($concept, $id);

    if (in_array($requestAccept, $headers) || in_array($getFormat, $outputFormatOptions)) {
        $graph = new \EasyRdf\Graph($uri);

        $graph->parse(json_encode($jsonData), 'jsonld', $uri);
        $format = $getFormat ? \EasyRdf\Format::getFormat($getFormat) : \EasyRdf\Format::getFormat($requestAccept);
        $output = $graph->serialise($format);
        if (!is_scalar($output)) {
            $output = var_export($output, true);
        }

        if ($getEncode) {
            if (strpos($requestAccept, 'json') !== false){
                print $output;
            }else {
                print htmlspecialchars($output);
            }
        } else {
            header('Content-Type: ' . $format->getDefaultMimeType());
            print $output;
        }
        eZExecution::cleanExit();
    }

} catch (Exception $e) {
    $error = $e->getMessage();
    eZDebug::writeError($error);
    if (in_array($requestAccept, $headers)) {
        print $error;
        eZExecution::cleanExit();
    }
}

$tpl = eZTemplate::factory();
$tpl->setVariable('output_format_list', $outputFormatOptions);
$tpl->setVariable('header_format_list', $headersOptions);
$tpl->setVariable('uri', $uri);
$tpl->setVariable('error', $error);

$Result = array();
$Result['content'] = $tpl->fetch('design:onto/data.tpl');
$Result['left_menu'] = false;
$contentInfoArray = array(
    'node_id' => null,
    'class_identifier' => null
);
$contentInfoArray['persistent_variable'] = array(
    'show_path' => true
);
if (is_array($tpl->variable('persistent_variable'))) {
    $contentInfoArray['persistent_variable'] = array_merge($contentInfoArray['persistent_variable'], $tpl->variable('persistent_variable'));
}
$Result['content_info'] = $contentInfoArray;
$Result['path'] = array(
    array('url' => false, 'text' => "OntoPiA data/$concept/$id")
);