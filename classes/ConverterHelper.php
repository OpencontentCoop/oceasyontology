<?php

namespace Opencontent\Easyontology;

use EasyRdf\RdfNamespace;

class ConverterHelper
{
    public static function generateId($concept, $id)
    {
        return rtrim(\eZSys::serverURL(), '/') . '/onto/data/' . $concept . '/' . $id;
    }

    public static function compactUri($uri, $context = null)
    {
        $short = RdfNamespace::shorten($uri);
        if (!$short) {
            $namespaces = RdfNamespace::namespaces();
            $uriParts = self::generateCompactUriParts($uri);
            RdfNamespace::set($uriParts['prefix'], $uriParts['long']);
            $short = RdfNamespace::shorten($uri);
        }

        list($prefix, $local) = explode(':', $short);
        $long = RdfNamespace::get($prefix);

        if ($context) {
            $context[$prefix] = $long;
        }

        return $short;
    }

    public static function getUriPrefix($uri)
    {
        $short = self::compactUri($uri);
        list($prefix, $local) = explode(':', $short);

        return $prefix;
    }

    private static function generateCompactUriParts($uri)
    {
        $parsed = parse_url($uri);
        $pathArray = explode('/', $parsed['path']);
        array_pop($pathArray);
        $long = rtrim($parsed['scheme'] . '://' . $parsed['host'] . implode('/', $pathArray), '/') . '/';
        $prefix = strtolower(array_pop($pathArray));

        return [
            'prefix' => $prefix,
            'long' => $long,
        ];
    }
}