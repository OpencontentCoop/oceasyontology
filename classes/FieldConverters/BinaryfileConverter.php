<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\Map;

class BinaryfileConverter extends AbstractFieldConverter
{
    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content)
    {
        $value = [];
        foreach ($dataByLanguage as $language => $field) {
            $url = $field['content']['url'];
            if (strpos($url, 'http') === false) {
                \eZURI::transformURI($url, true, 'full');
            }
            $value[] = [
                '@language' => $language,
                '@id' => $url,
            ];
        }

        return $value;
    }

}