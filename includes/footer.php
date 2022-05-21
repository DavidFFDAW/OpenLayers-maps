<?php
$dir = explode('C:\xampp\htdocs\\',dirname(__DIR__))[1];
$port = isset($prt) ? $prt : '';
$host = $_SERVER['HTTP_HOST'];
$finalRoute = $host.(!empty($prt) ? ':'.$prt : '').'/'.$dir;

?>
<script id="foota" src="<?= 'http://'.$finalRoute ?>/een.js"></script>
</body>
</html>