<?php

class EasyOntologyExtraParameter extends OCClassExtraParametersHandlerBase
{
    const IDENTIFIER = 'easyontology';

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getName()
    {
        return 'Mappatura in ontologia json-ld';
    }

    public function attributes()
    {
        $attributes = parent::attributes();
        $attributes[] = 'easyontology_list';
        $attributes[] = 'easyontology';

        return $attributes;
    }

    public function attribute($key)
    {
        switch ($key) {

            case 'easyontology_list':
                $mapCollection = \Opencontent\Easyontology\MapperRegistry::fetchMapCollectionByClassIdentifier($this->class->attribute('identifier'));
                $links = [];
                foreach ($mapCollection->getMaps() as $map){
                    $links[] = $map->jsonSerialize();
                }
                return $links;

            case 'easyontology':
                return $this->getClassParameter('easyontology');
        }

        return parent::attribute($key);
    }

    protected function handleAttributes()
    {
        return false;
    }

    protected function classEditTemplateUrl()
    {
        return 'design:classtools/extraparameters/' . $this->getIdentifier() . '/edit_class.tpl';
    }
}