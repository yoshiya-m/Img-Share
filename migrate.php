<?php
spl_autoload_extensions(".php");
spl_autoload_register(function($class) {
    $filePath = __DIR__ . "/" . str_replace("\\", "/", $class) . ".php";
    if (file_exists($filePath)) {
        require_once($filePath);
    }
});

use Database\Migrations\CreateTableImages;
use Database\MySQLWrapper;

$requesttMigration = new CreateTableImages();
$sql = $requesttMigration->up()[0];

$db = new MySQLWrapper();
$db->query($sql);
