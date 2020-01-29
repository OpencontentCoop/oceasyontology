<?php

namespace Opencontent\Easyontology;

abstract class AbstractConverter implements ConverterInterface, \JsonSerializable
{
    protected $doc = [];

    /**
     * @var \ArrayObject
     */
    protected $context;

    abstract protected function convert();

    public function getDoc()
    {
        $this->convert();
        return $this->doc;
    }

    public function getContext()
    {
        $this->convert();
        return $this->context;
    }

    public function jsonSerialize()
    {
        $data = [];
        $context = $this->getContext();
        if ($context) {
            $data['@context'] = $context->getArrayCopy();
        }

        $data = array_merge($data, $this->getDoc());
        return $data;
    }
}