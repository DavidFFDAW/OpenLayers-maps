<?php

require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'models/Event.php');
$allEvents = Event::all();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado eventos</title>
</head>
<body>
    <a href="/create.php">Crear Evento</a>
    <div>
        <pre>
            <?php
                print_r($allEvents);
            ?>
        </pre>
    </div>
</body>
</html>