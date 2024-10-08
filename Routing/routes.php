<?php

use Database\MySQLWrapper;
use Helpers\DatabaseHelper;
use Helpers\ValidationHelper;
use Response\HTTPRenderer;
use Response\Render\HTMLRenderer;
use Response\Render\JSONRenderer;

return [
    "" => function(): HTTPRenderer {
        return new HTMLRenderer("component/home");
    },
    "create" => function(): HTTPRenderer {
        
        if (!isset($_FILES['image'])) {
            // file_put_contents('debug.log', print_r($_FILES, true));
            return new JSONRenderer( ['result'=> '共有URL作成失敗', 'message' => '画像ファイルが見つかりませんでした。', 'url' => '']);
        }
        // データのサイズと形式を検証する
        if ($_FILES['image']['size'] > 1000000) {
            return new JSONRenderer( ['result'=> '共有URL作成失敗', 'message' => 'ファイルサイズは1MB以下にしてください。', 'url' => '']);
        }
        // リクエスト頻度を確認
        if (!DatabaseHelper::isRequestAllowed()) return new JSONRenderer( ['result'=> '共有URL作成失敗', 'message' => 'リクエスト頻度が高すぎます。1分後に再実行してください。', 'url' => '']);
        // ipを記録する
        DatabaseHelper::saveRequestLog();

        $validExtensions = ['png', 'jpg', 'jpeg', 'gif'];
        $mediaType = str_replace('image/', '', $_FILES['image']['type'] );
        if (!in_array($mediaType, $validExtensions)) {
            return new JSONRenderer( ['result'=> '作成失敗', 'message' => 'このファイル拡張子は非対応です。', 'url' => $mediaType]);
        }

        // databaseにデータを保存する databasehelperで作成
        $pathData = DatabaseHelper::saveImage($mediaType);
        
        
        return new JSONRenderer(array_merge(["result" => "作成成功"], $pathData));
    },
    "png" => function(): HTTPRenderer {
        return new HTMLRenderer("component/image");
    },
    "jpeg" => function(): HTTPRenderer  {
        return new HTMLRenderer("component/image");
    },
    "gif" => function(): HTTPRenderer {
        return new HTMLRenderer("component/image");
    }
];

