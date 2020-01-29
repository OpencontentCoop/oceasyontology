<?php
$Module = array('name' => 'easyontology');

$ViewList = array();
$ViewList['data'] = array(
    'script' => 'data.php',
    'params' => array('Concept', 'ID'),
    'functions' => array('view')
);


$FunctionList = array();
$FunctionList['view'] = array();
