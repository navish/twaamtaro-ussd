<?php
function db()
{
	static $dbcon;
    $dbcon = pg_connect("host=127.0.0.1 port=5432 dbname=YourDbName user=YourUserName password=YourPassword");
    return $dbcon;
}
    
?>