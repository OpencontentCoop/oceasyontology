<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\Map;
use Opencontent\Easyontology\MapConverter;

class RecurrenceConverter extends AbstractFieldConverter
{
    const TI_AT_TIME = 'https://w3id.org/italia/onto/TI/TimeInterval';

    const TI_AT_DURATION = 'https://w3id.org/italia/onto/TI/Duration';

    const SCHEMA_SCHEDULE = 'http://schema.org/Schedule';

    const SCHEMA_DURATION = 'http://schema.org/Duration';

    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content)
    {
        $values = null;
        $data = array_shift($dataByLanguage);

        if ($this->rdfRange == self::TI_AT_TIME || $this->rdfRange == self::TI_AT_DURATION) {

            $concept = $this->rdfRange == self::TI_AT_TIME ? 'time-interval' : 'duration';

            $values = [];
            foreach ($data['content']['recurrences'] as $recurrence) {
                $values[] = MapConverter::generateId($concept, $this->fieldDefinition['id'] . '-' . $this->fieldDefinition['version'] . '-' . $recurrence['id']);
            }
            if (count($values) == 1) {
                $values = $values[0];
            }
        }

        if ($this->rdfRange == self::SCHEMA_SCHEDULE){

            if (count($data['content']['recurrences']) > 1) {

                $startDateTime = strtotime($data['content']['input']['startDateTime']);
                $endDateTime = strtotime($data['content']['input']['endDateTime']);
                $until = strtotime($data['content']['input']['until']);

                $values = [
                    "@type" => self::SCHEMA_SCHEDULE,
                    "http://schema.org/startDate" => date('Y-m-d', $startDateTime),
                    "http://schema.org/endDate" => date('Y-m-d', $until),
                    "http://schema.org/repeatFrequency" => $data['content']['input']['recurrencePattern'],
                    "http://schema.org/byDay" => $data['content']['input']['byweekday'], //"http://schema.org/Wednesday",
                    "http://schema.org/startTime" => date('H:iP', $startDateTime),
                    "http://schema.org/endTime" => date('H:iP', $endDateTime),
                    "http://schema.org/scheduleTimezone" => $data['content']['input']['timeZone']['name']
                ];
            }
        }

        return $values;
    }

}