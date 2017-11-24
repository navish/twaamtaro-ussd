<?php
function db()
{
	static $dbcon;
    $dbcon = pg_connect(pg_connection_string_from_database_url());
    return $dbcon;
}

function pg_connection_string_from_database_url() {
  return "host=localhost port=5432 dbname=mitaro user=postgres password=nancy"; # <- you may want to add sslmode=require there too
}
    
?>