<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\ConverterHelper;
use Opencontent\Easyontology\Map;

class ImageConverter extends AbstractFieldConverter
{
    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content)
    {
        $field = array_shift($dataByLanguage);

        $url = $field['content']['url'];
        if (strpos($url, 'http') === false) {
            \eZURI::transformURI($url, true, 'full');
        }

        $type = $this->rdfRange == 'http://schema.org/URL' ? 'http://schema.org/ImageObject' : $this->rdfRange;

        $values = [
            '@type' => ConverterHelper::compactUri($type, $this->context),
            '@id' => $url,
        ];

        return $values;
    }

}