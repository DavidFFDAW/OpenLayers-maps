<?php

class Event {

    private $id;
    private $name = 'peoa';
    private $date = '2016-01-01';
    private $hasMap = false;
    private $config = 'array()';
    private $startHour = '08:00';
    private $endHour = '17:00';
    private $aproxDuration = '1:00';
    private $location = 'Sala de Reuniões';
    private $initialCoords = array(-23.54789,-46.63889);
    private $createdAt = '2016-01-01';

    private $fillable_fields = array(

        'name' => array(
            'type' => 'string',
            'max'  => 50,
            'nullable' => false,
        ),
        'datetime' => array(
            'type' => 'datetime',
            'nullable' => false,
        ),
        'has_map' => array(
            'type' => 'boolean',
            'default' => true,
        ),
        'configuration' =>array(
            'type' => 'string',
            'nullable' => true,
        ),
        'hour_start' => array(            
            'type' => 'datetime',
            'nullable' => true,
        ),
        'hour_end' => array(        
            'type' => 'datetime',
            'nullable' => true,
        ),
        'aprox_duration' => array(
            'type' => 'int',
            'nullable' => true,
        ),
        'location' => array(            
            'type' => 'string',
            'max'  => 20,
            'nullable' => false,
            'default' => '',
        ),
        // serialized array ↓
        'initial_coords' => array(
            'type' => 'string',
            'max'  => 255,
            'nullable' => true,
            'default' => null,
            'serialized' => true,
        ),
        'created_at' => array(
            'type' => 'datetime',
            'nullable' => true,
        ),
    );

    private function getDbFieldsNames (): array {
        return array_keys($this->fillable_fields);
    }

    public function getFillableFields (): array {
        return $this->fillable_fields;
    }

    private function getFieldsWithValues (): array {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'datetime' => $this->date,
            'has_map' => $this->hasMap,
            'configuration' => $this->config,
            'hour_start' => $this->startHour,
            'hour_end' => $this->endHour,
            'aprox_duration' => $this->aproxDuration,
            'location' => $this->location,
            'initial_coords' => $this->initialCoords,
            'created_at' => $this->createdAt,
        );
    }

    private function getValuedFieldsFromInsertedFields (): array {
        $currentValues = $this->getFieldsWithValues();
        $empt = array();

        foreach ($this->fillable_fields as $field => $options) {
            $options['value'] = $currentValues[$field];
            $empt[$field] = $options;
        }

        // foreach ($this->fillable_fields as $field => $options) {
        //     if (!isset($currentValues[$field])) {
        //         throw new Error('Field ' . $field . ' has not been found');
        //     }

        //     if (!empty($currentValues[$field])) {
        //         if (in_array($options['type'],array('string', 'datetime'))) {
        //             $currentValues[$field] = "'".$currentValues[$field]."'";
        //         }
        //         if ($options['type'] === 'boolean') {
        //             $currentValues[$field] = $currentValues[$field] ? 1 : 0;
        //         }
        //     }

        //     if (isset($options['default'])) {
        //         $currentValues[$field] = $options['default'];
        //     }
        //     if (isset($options['nullable'])) {
        //         if ($options['nullable']) {
        //             $currentValues[$field] = NULL;
        //         } else {
        //             // throw new Error('Field ' . $field . ' is not nullable');
        //         } 
        //     }
        // }

        return $empt;
    }

    private function getFinalFields(array $fields): array {
        $finalFields = array();

        foreach ($fields as $field => $options) {
            $value = $options['value'];
            $type = $options['type'];
            $nullable = isset($options['nullable']) ? $options['nullable'] : false;
            $serialize = isset($options['serialized']) ? $options['serialized'] : false;

            if (empty($value)) {
                if ($nullable) {
                    $finalFields[$field] = 'NULL';
                } else {
                    if ($options['default']) {
                        $finalFields[$field] = $options['default'];
                    } else {
                        throw new Error('Field ' . $field . ' is not nullable');
                    }
                }
            }

            if ($serialize) {
                $options['value'] = serialize($options['value']);
            } 
            if (in_array($type,['string','datetime'])) {
                $finalFields[$field] = "'".$options['value']."'";
            }
            if ($type === 'boolean') {
                $finalFields[$field] = $options['value'] ? 1 : 0;
            }
        }

        return $finalFields;
    }

    public function getInsertSentenceBasedOnFields (): string {
        $fields = $this->getDbFieldsNames();
        $unifiedFields = implode(', ', $fields);
        $valuedFields = $this->getValuedFieldsFromInsertedFields();
        $finalFields = $this->getFinalFields($valuedFields);

        return "INSERT INTO events ($unifiedFields) VALUES (".implode(', ', $finalFields).")";        
    }

}

$p = new Event();

echo $p->getInsertSentenceBasedOnFields();