CREATE TABLE IF NOT EXISTS `events` (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `datetime` DATETIME NOT NULL,
    `has_map` BOOL DEFAULT TRUE,
    `configuration` JSON NULL,
    `hour_start` DATETIME NULL,
    `hour_end` DATETIME NULL,
    `aprox_duration` INT NULL,
    `location` VARCHAR(20),
    `initial_coords` VARCHAR(255) NULL DEFAULT NULL,
    `created_at` DATETIME DEFAULT current_timestamp() 
);