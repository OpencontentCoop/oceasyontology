<?php

namespace Opencontent\Easyontology;


class MapCollection implements \JsonSerializable
{
    private $classIdentifier;

    /**
     * @var Map[]
     */
    private $maps = array();


    public static function createFromJsonString($string)
    {
        return self::createFromArray(json_decode($string, true));
    }

    public static function createFromArray(array $data)
    {
        $collection = new MapCollection();
        $collection->setClassIdentifier($data['classIdentifier']);
        foreach ($data['maps'] as $map) {
            $collection->addMap(Map::createFromArray($map));
        }

        return $collection;
    }

    /**
     * @return string
     */
    public function getClassIdentifier()
    {
        return $this->classIdentifier;
    }

    /**
     * @param mixed $classIdentifier
     */
    public function setClassIdentifier($classIdentifier)
    {
        $this->classIdentifier = $classIdentifier;
    }

    /**
     * @return Map[]
     */
    public function getMaps()
    {
        return $this->maps;
    }

    public function hasMaps()
    {
        return count($this->maps) > 0;
    }

    public function addMap(Map $map)
    {
        $this->maps[$map->getSlug()] = $map;
    }

    public function removeMap(Map $map)
    {
        unset($this->maps[$map->getSlug()]);
    }

    public function hasMap(Map $map)
    {
        return isset($this->maps[$map->getSlug()]);
    }

    public function renameMap(Map $map, $newSlug)
    {
        if (isset($this->maps[$map->getSlug()])) {
            $newMap = clone $this->maps[$map->getSlug()];
            unset($this->maps[$map->getSlug()]);
            $this->maps[$newSlug] = $newMap;

            return $newMap;
        }

        return $map;
    }

    /**
     * @param Map[] $maps
     */
    public function setMaps($maps)
    {
        $this->maps = $maps;
    }

    /**
     * @param $slug
     * @return bool|Map
     */
    public function findMapBySlug($slug)
    {
        foreach ($this->maps as $map) {
            if ($map->getSlug() == $slug) {
                return $map;
            }
        }

        return false;
    }

    public function jsonSerialize()
    {
        $maps = [];
        foreach ($this->maps as $map) {
            $maps[] = $map->jsonSerialize();
        }

        return [
            'classIdentifier' => $this->classIdentifier,
            'maps' => $maps
        ];
    }
}