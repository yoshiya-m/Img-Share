<?php
// ファイルがexpiredしていたら

// ファイルパスを取得

use Helpers\DatabaseHelper;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');
$segments = ($path == '') ? [] : explode('/', $path);
$segments = explode('/', $path);
$mediaType = $segments[0] ?? "";
$imgId = $segments[1] ?? "";
$filePath = sprintf("/uploads/%s.%s", $imgId, $mediaType);
if (DatabaseHelper::isExpired($imgId)) {
    echo "この画像は有効期限切れです";
    exit;
}
DatabaseHelper::addViewCount($imgId);
DatabaseHelper::updateViewedDate($imgId);
$viewCount = DatabaseHelper::getViewCount($imgId);
use Helpers\Settings;

$siteURL = Settings::env('BASE_URL');;

?>

<div class="bg-info text-center py-2 h-20">
    <h1><a href="<?php echo $siteURL ?>" class="text-decoration-none text-dark">Image Share Service</a></h1>
</div>
<div class="d-flex flex-column justify-content-center align-items-center">
    <h3 class="my-3">表示回数： <span><?php echo $viewCount?></span></h3>
    <img src="<?php echo $filePath ?>" class="w-80 h-80">
</div>

</div>