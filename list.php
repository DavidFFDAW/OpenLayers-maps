<?php

require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'includes/headers.php';
require_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'models/Event.php');
$allEvents = Event::all();

?>
    <a href="./create.php">Crear Evento</a>
    <div>
        <pre>
            <?php
                print_r($allEvents);
            ?>
        </pre>
    </div>