<?php

namespace Opencontent\Easyontology;

use EasyRdf\RdfNamespace;

class ConverterHelper
{
    public static function generateId($concept, $id)
    {
        return rtrim(\eZSys::serverURL(), '/') . '/onto/data/' . $concept . '/' . $id;
    }

    public static function compactUri($uri, $context)
    {
        $short = RdfNamespace::shorten($uri);
        if (!$short) {
            $parsed = parse_url($uri);
            $pathArray = explode('/', $parsed['path']);
            array_pop($pathArray);
            $long = rtrim($parsed['scheme'] . '://' . $parsed['host'] . implode('/', $pathArray), '/') . '/';
            $prefix = strtolower(array_pop($pathArray));
            RdfNamespace::set($prefix, $long);
            $short = RdfNamespace::shorten($uri);
        }

        list($prefix, $local) = explode(':', $short);
        $long = RdfNamespace::get($prefix);

        $context[$prefix] = $long;

        return $short;
    }
}