<?php
    global $dbcon;
    $dbcon = pg_connect("host=127.0.0.1 port=5432 dbname=mitaro user=postgres password=nancy");

?>