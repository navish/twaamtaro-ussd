<?php
function db()
{
	static $dbcon;
    $dbcon = pg_connect(pg_connection_string_from_database_url());
    return $dbcon;
}

function pg_connection_string_from_database_url() {
  extract(parse_url($_ENV["DATABASE_URL"]));
  return "user=$user password=$pass host=$host dbname=" . substr($path, 1); # <- you may want to add sslmode=require there too
}
    
?>