<?php
use Database\MySQLWrapper;
$mysqli = new MySQLWrapper();

$sql = file_get_contents(__DIR__ . '/Examples/cars-setup.sql');
$queries = explode(';', $sql);

foreach ($queries as $query) {
    $result = $mysqli->query($query);
}


if($result === false) throw new Exception('Could not execute query.');
else print("Successfully ran all SQL setup queries.".PHP_EOL);