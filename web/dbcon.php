<?php
function db()
{
	static $dbcon;
    $dbcon = pg_connect(pg_connection_string_from_database_url());
    return $dbcon;
}

function pg_connection_string_from_database_url() {
  // extract(parse_url('postgres://jplhkyofzqmllx:69565a9e84a5e350ca1ded9f75004924ec08cd2d7874b16417230763061e7cac@ec2-23-23-78-213.compute-1.amazonaws.com:5432/d8dsrp7ftqpl8'));
  return 'user=jplhkyofzqmllx password=69565a9e84a5e350ca1ded9f75004924ec08cd2d7874b16417230763061e7cac host=ec2-23-23-78-213.compute-1.amazonaws.com dbname=d8dsrp7ftqpl8'; # <- you may want to add sslmode=require there too
}
?>