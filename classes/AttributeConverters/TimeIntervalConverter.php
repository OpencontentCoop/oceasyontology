<?php

namespace Opencontent\Easyontology\AttributeConverters;

use Opencontent\Easyontology\ConverterInterface;
use Opencontent\Easyontology\MapConverter;

class TimeIntervalConverter implements ConverterInterface, \JsonSerializable
{
    const TIME_INTERVAL_TYPE = 'https://w3id.org/italia/onto/TI/TimeInterval';

    const DURATION_TYPE = 'https://w3id.org/italia/onto/TI/Duration';

    private $concept;

    private $id;

    private $recurrences = [];

    private $currentRecurrence;

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
    }

    public function getDoc()
    {
        if ($this->concept == 'time-interval') {
            $doc = [
                '@id' => MapConverter::generateId($this->concept, $this->id),
                '@type' => self::TIME_INTERVAL_TYPE
            ];

            foreach ($this->recurrences as $recurrence) {
                if ($this->currentRecurrence == $recurrence['id']) {
                    $doc['https://w3id.org/italia/onto/TI/startTime'] = [
                        '@value' => $recurrence['start'],
                        '@type' => "http://www.w3.org/2001/XMLSchema#dateTime"
                    ];
                    $doc['https://w3id.org/italia/onto/TI/endTime'] = [
                        '@value' => $recurrence['end'],
                        '@type' => "http://www.w3.org/2001/XMLSchema#dateTime"
                    ];
                }
            }
        } elseif ($this->concept == 'duration') {
            $doc = [
                '@id' => MapConverter::generateId($this->concept, $this->id),
                '@type' => [
                    self::DURATION_TYPE,
                    'https://w3id.org/italia/onto/MU/Value',
                    'https://w3id.org/italia/onto/l0/Entity'
                ]
            ];

            foreach ($this->recurrences as $recurrence) {
                if ($this->currentRecurrence == $recurrence['id']) {
                    $start = strtotime($recurrence['start']);
                    $end = strtotime($recurrence['end']);
                    $duration = ($end - $start) / 60 / 60;
                    $doc['https://w3id.org/italia/onto/MU/value'] = ['@value' => $duration];
                    $doc['https://w3id.org/italia/onto/MU/hasMeasurementUnit'] = [
                        '@id' => MapConverter::generateId($this->concept, $this->id) . '#value/measument-unit/ora',
                        '@type' => 'https://w3id.org/italia/onto/MU/MeasurementUnit',
                        'https://w3id.org/italia/onto/l0/name' => ['@value' => 'Ora', '@language' => 'it']
                    ];

                }
            }
        }


        return $doc;
    }

    public function getContext()
    {
        // TODO: Implement getContext() method.
    }

    public function jsonSerialize()
    {
        $data = [];
        $context = $this->getContext();
        if ($context) {
            $data['@context'] = $context;
        }

        $data = array_merge($data, $this->getDoc());
        return $data;
    }


}