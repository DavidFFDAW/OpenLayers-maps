<?php

$my = new mysqli('146.59.159.40', 'davidff', 'root', '', 3306);

// $my->query("CREATE DATABASE IF NOT EXISTS `event_maps`");
$my->select_db('event_maps');


$services = $my->query("SHOW DATABASES");

if($services && $services->num_rows > 0){
    $services->fetch_all(MYSQLI_ASSOC);
}

foreach($services as $service){
    echo $service['Database'] . PHP_EOL; //work properly, cause it implements Iterator 
}


?>