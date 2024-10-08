<?php

use Helpers\DatabaseHelper;

spl_autoload_extensions(".php");
spl_autoload_register(function ($class) {
    $filePath = __DIR__ . "/" . str_replace("\\", "/", $class) . ".php";
    if (file_exists($filePath)) {
        require_once($filePath);
    } 
});
$DEBUG = true;
$routes = include('Routing/routes.php');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');
$segments = explode('/',$path);
$mediaType = $segments[0] ?? "";
$imgId = $segments[1] ?? "";


if (isset($routes[$mediaType])) {
    
    $renderer = $routes[$mediaType]();

    try {

        foreach ($renderer->getFields() as $name => $value) {

            $sanitized_value = filter_var($value, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

            if ($sanitized_value && $sanitized_value === $value) {
                header("{$name}: {$sanitized_value}");
            } else {
                http_response_code(500);
                print("Internal error, please contact the admin.<br>");
                exit;
            }
            http_response_code(200);
            print($renderer->getContent());
        }
    } catch (Exception $e) {
        http_response_code(500);
        if ($DEBUG) {
            print('error: ' . $e);
        }

        print("Internal error, please contact the admin.<br>");
    }
} else {
    if (DatabaseHelper::doesDeletePathExist($mediaType)) {
        $deletePath = $mediaType;
        DatabaseHelper::deleteImageFile($deletePath);
        echo "画像を削除しました。";
        exit;
    }

    http_response_code(404);
    echo "404 Not Found: The requested route was not found on this server.";

    echo $path;
}
