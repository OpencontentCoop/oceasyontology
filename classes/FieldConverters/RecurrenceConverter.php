<?php

namespace Opencontent\Easyontology\FieldConverters;

use Opencontent\Easyontology\AttributeConverters\TimeIntervalConverter;
use Opencontent\Easyontology\ConverterHelper;
use Opencontent\Easyontology\Map;

class RecurrenceConverter extends AbstractFieldConverter
{
    const SCHEMA_SCHEDULE = 'http://schema.org/Schedule';

    const SCHEMA_DURATION = 'http://schema.org/Duration';

    public function convert($dataByLanguage, $mapToUri, Map $classMap, $content)
    {
        $values = null;
        $data = array_shift($dataByLanguage);

        if ($this->rdfRange == TimeIntervalConverter::TIME_INTERVAL_TYPE || $this->rdfRange == TimeIntervalConverter::DURATION_TYPE) {

            $concept = $this->rdfRange == TimeIntervalConverter::TIME_INTERVAL_TYPE ? 'time-interval' : 'duration';

            $values = [];
            foreach ($data['content']['recurrences'] as $recurrence) {
                $values[] = $this->getDoc($concept, $this->fieldDefinition['id'] . '-' . $this->fieldDefinition['version'] . '-' . $recurrence['id']);
            }
            if (count($values) == 1) {
                $values = $values[0];
            }
        }

        if ($this->rdfRange == self::SCHEMA_SCHEDULE) {

            if (count($data['content']['recurrences']) > 1) {

                $startDateTime = strtotime($data['content']['input']['startDateTime']);
                $endDateTime = strtotime($data['content']['input']['endDateTime']);
                $until = strtotime($data['content']['input']['until']);

                $values = [
                    "@type" => ConverterHelper::compactUri(self::SCHEMA_SCHEDULE, $this->context),
                    ConverterHelper::compactUri("http://schema.org/startDate", $this->context) => date('Y-m-d', $startDateTime),
                    ConverterHelper::compactUri("http://schema.org/endDate", $this->context) => date('Y-m-d', $until),
                    ConverterHelper::compactUri("http://schema.org/repeatFrequency", $this->context) => $data['content']['input']['recurrencePattern'],
                    ConverterHelper::compactUri("http://schema.org/byDay", $this->context) => $data['content']['input']['byweekday'], //"http://schema.org/Wednesday",
                    ConverterHelper::compactUri("http://schema.org/startTime", $this->context) => date('H:iP', $startDateTime),
                    ConverterHelper::compactUri("http://schema.org/endTime", $this->context) => date('H:iP', $endDateTime),
                    ConverterHelper::compactUri("http://schema.org/scheduleTimezone", $this->context) => $data['content']['input']['timeZone']['name']
                ];
            }
        }

        return $values;
    }

    private function getDoc($concept, $id)
    {
        try {
            $converter = \Opencontent\Easyontology\ConverterFactory::factory($concept, $id);
            $newContext = array_merge($this->context->getArrayCopy(), $converter->getContext()->getArrayCopy());
            $this->context->exchangeArray($newContext);

            return $converter->getDoc();

        } catch (\Exception $e) {
            \eZDebug::writeError($e->getMessage(), __METHOD__ . '#' . $e->getLine());
        }

        return false;
    }

}