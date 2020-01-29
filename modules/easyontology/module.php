<?php
$Module = array('name' => 'easyontology');

$ViewList = array();
$ViewList['dashboard'] = array(
    'script' => 'dashboard.php',
    "default_navigation_part" => 'ezsetupnavigationpart',
    'functions' => array('mapper')
);

$ViewList['mapper'] = array(
    'script' => 'mapper.php',
    "default_navigation_part" => 'ezsetupnavigationpart',
    'functions' => array('mapper'),
    'params' => array('ClassIdentifier', 'MapSlug')
);

$ViewList['mapped'] = array(
    'script' => 'mapped.php',
    "default_navigation_part" => 'ezsetupnavigationpart',
    'functions' => array('mapper'),
    'params' => array('ClassIdentifier', 'MapSlug')
);

$ViewList['inspect'] = array(
    'script' => 'inspect.php',
    "default_navigation_part" => 'ezsetupnavigationpart',
    'functions' => array('mapper'),
    'params' => array('Slug')
);

$ViewList['remove'] = array(
    'script' => 'remove.php',
    "default_navigation_part" => 'ezsetupnavigationpart',
    'functions' => array('mapper'),
    'params' => array()
);

$FunctionList = array();
$FunctionList['mapper'] = array();
