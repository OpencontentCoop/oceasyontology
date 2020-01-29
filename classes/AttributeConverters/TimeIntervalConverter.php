<?php

namespace Opencontent\Easyontology\AttributeConverters;

use Opencontent\Easyontology\ConverterHelper;
use Opencontent\Easyontology\AbstractConverter;

class TimeIntervalConverter extends AbstractConverter
{
    const TIME_INTERVAL_TYPE = 'https://w3id.org/italia/onto/TI/TimeInterval';

    const DURATION_TYPE = 'https://w3id.org/italia/onto/TI/Duration';

    private $concept;

    private $id;

    private $recurrences = [];

    private $currentRecurrence;

    private $isConverted;

    public function __construct($concept, $id)
    {
        $this->concept = $concept;
        $this->id = $id;
        list($id, $version, $currentRecurrence) = explode('-', $this->id, 3);
        /** @var \eZContentObjectAttribute $attribute */
        $attribute = \eZContentObjectAttribute::fetch($id, $version);
        if ($attribute->attribute('data_type_string') == 'ocevent') {
            $content = $attribute->content();
            $this->recurrences = $content['recurrences'];
        }
        $this->currentRecurrence = $currentRecurrence;
        $this->context = new \ArrayObject();
    }

    protected function convert()
    {
        if (!$this->isConverted) {
            if ($this->concept == 'time-interval') {
                $this->doc = [
                    '@id' => ConverterHelper::generateId($this->concept, $this->id),
                    '@type' => ConverterHelper::compactUri(self::TIME_INTERVAL_TYPE, $this->context)
                ];

                foreach ($this->recurrences as $recurrence) {
                    if ($this->currentRecurrence == $recurrence['id']) {
                        $this->doc[ConverterHelper::compactUri('https://w3id.org/italia/onto/TI/startTime', $this->context)] = [
                            '@value' => $recurrence['start'],
                            '@type' => ConverterHelper::compactUri("http://www.w3.org/2001/XMLSchema#dateTime", $this->context)
                        ];
                        $this->doc[ConverterHelper::compactUri('https://w3id.org/italia/onto/TI/endTime', $this->context)] = [
                            '@value' => $recurrence['end'],
                            '@type' => ConverterHelper::compactUri("http://www.w3.org/2001/XMLSchema#dateTime", $this->context)
                        ];
                    }
                }
            } elseif ($this->concept == 'duration') {
                $this->doc = [
                    '@id' => ConverterHelper::generateId($this->concept, $this->id),
                    '@type' => [
                        ConverterHelper::compactUri(self::DURATION_TYPE, $this->context),
                        ConverterHelper::compactUri('https://w3id.org/italia/onto/MU/Value', $this->context),
                        ConverterHelper::compactUri('https://w3id.org/italia/onto/l0/Entity', $this->context)
                    ]
                ];

                foreach ($this->recurrences as $recurrence) {
                    if ($this->currentRecurrence == $recurrence['id']) {
                        $start = strtotime($recurrence['start']);
                        $end = strtotime($recurrence['end']);
                        $duration = ($end - $start) / 60 / 60;
                        $this->doc[ConverterHelper::compactUri('https://w3id.org/italia/onto/MU/value', $this->context)] = ['@value' => $duration];
                        $this->doc[ConverterHelper::compactUri('https://w3id.org/italia/onto/MU/hasMeasurementUnit', $this->context)] = [
                            '@id' => ConverterHelper::generateId($this->concept, $this->id) . '#value/measument-unit/ora',
                            '@type' => ConverterHelper::compactUri('https://w3id.org/italia/onto/MU/MeasurementUnit', $this->context),
                            ConverterHelper::compactUri('https://w3id.org/italia/onto/l0/name', $this->context) => ['@value' => 'Ora', '@language' => 'it']
                        ];

                    }
                }
            }


            $this->isConverted = true;
        }
    }

}