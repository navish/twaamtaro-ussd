<?php
function db()
{
	static $dbcon;
    $dbcon = pg_connect("host=138.197.5.83 port=5432 dbname=adaprod user=adaprod password=adaprod");
    return $dbcon;
}
    
?>