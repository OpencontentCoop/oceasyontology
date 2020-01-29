<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\Map;

class TagConverter extends AbstractFieldConverter
{

    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content)
    {
        $attribute = \eZContentObjectAttribute::fetch($this->fieldDefinition['id'], $this->fieldDefinition['version']);
        if ($attribute instanceof \eZContentObjectAttribute) {
            $tags = \eZTags::createFromAttribute($attribute, 'ita-PA')->attribute('keywords');

            // controlla che la traduzione ita-PA sia effettivamente un uri
            foreach ($tags as $index => $tag){
                if (strpos($tag, 'http') === false){
                    unset($tags[$index]);
                }
            }

            if (count($tags) > 0) {
                $tags = array_fill_keys($tags, '@id');

                return array_flip($tags);
            }
        }

        return false;
    }

}