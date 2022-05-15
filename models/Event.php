<?php

require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'../DBConnection.php');

class Event {

    private $id;
    private $name;
    private $date;
    private $hasMap;
    private $config;
    private $startHour;
    private $endHour;
    private $aproxDuration;
    private $location;
    private $initialCoords;
    private $createdAt;

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

    private function getValueOrDefault($array, $field, $default): mixed {
        return isset($array[$field]) ? $array[$field] : $default;
    }

    public function __construct(array $array = array()) {
        $this->id = $this->getValueOrDefault($array, 'id', null);
        $this->name = $this->getValueOrDefault($array, 'name', null);
        $this->date = $this->getValueOrDefault($array, 'datetime', null);
        $this->hasMap = $this->getValueOrDefault($array, 'map', null);;
        $this->config = $this->getValueOrDefault($array, 'config', null);
        $this->startHour = $this->getValueOrDefault($array, 'start', null);
        $this->endHour = $this->getValueOrDefault($array, 'end', null);
        $this->aproxDuration = $this->getValueOrDefault($array, 'duration', null);
        $this->location = $this->getValueOrDefault($array, 'location', null);
        $this->initialCoords = $this->getValueOrDefault($array, 'coords', null);
        $this->createdAt = $this->getValueOrDefault($array, 'createdAt', null);
    }

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
                        throw new Error('Field `' . $field . '` is not nullable');
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

    public static function all() {
        $conn = DBConnection::getInstance()->getConnection();
        $stmt = $conn->prepare("SELECT * FROM events");
        $stmt->execute();

        $result = $stmt->get_result()->fetch_all(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Get the value of id
     */ 
    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of name
     */ 
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName(string $name): self
    {
        $this->name = "'$name'";

        return $this;
    }

    /**
     * Get the value of date
     */ 
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * Set the value of date
     *
     * @return  self
     */ 
    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of hasMap
     */ 
    public function getHasMap(): bool
    {
        return boolval($this->hasMap);
    }

    /**
     * Set the value of hasMap
     *
     * @return  self
     */ 
    public function setHasMap(bool $hasMap): self
    {
        $this->hasMap = $hasMap;

        return $this;
    }

    /**
     * Get the value of config
     */ 
    public function getConfig(): mixed
    {
        return json_decode($this->config);
    }

    /**
     * Set the value of config
     *
     * @return  self
     */ 
    public function setConfig($config): self
    {
        $this->config = json_encode($config);

        return $this;
    }

    /**
     * Get the value of startHour
     */ 
    public function getStartHour(): string
    {
        return $this->startHour;
    }

    /**
     * Set the value of startHour
     *
     * @return  self
     */ 
    public function setStartHour(string $startHour): self
    {
        $this->startHour = $startHour;

        return $this;
    }

    /**
     * Get the value of endHour
     */ 
    public function getEndHour(): string
    {
        return $this->endHour;
    }

    /**
     * Set the value of endHour
     *
     * @return  self
     */ 
    public function setEndHour(string $endHour): self
    {
        $this->endHour = $endHour;

        return $this;
    }

    /**
     * Get the value of aproxDuration
     */ 
    public function getAproxDuration(): int
    {
        return $this->aproxDuration;
    }

    /**
     * Set the value of aproxDuration
     *
     * @return  self
     */ 
    public function setAproxDuration(int $aproxDuration): self
    {
        $this->aproxDuration = $aproxDuration;

        return $this;
    }

    /**
     * Get the value of location
     */ 
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Set the value of location
     *
     * @return  self
     */ 
    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get the value of createdAt
     */ 
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */ 
    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of initialCoords
     */ 
    public function getInitialCoords(): array
    {
        return unserialize($this->initialCoords);
    }

    /**
     * Set the value of initialCoords serialized
     *
     * @return  self
     */ 
    public function setInitialCoords(array $initialCoords): self
    {
        $this->initialCoords = serialize($initialCoords);

        return $this;
    }
}