<?php

class DBConnection {
    public static $instance;

    public function __construct() {
        $this->conn = new mysqli(
            '146.59.159.40',
            'davidff',
            'root',
            'event_maps'
        );
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new DBConnection();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}