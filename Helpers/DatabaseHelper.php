<?php

namespace Helpers;

use Database\MySQLWrapper;
use DateTime;
use Exception;

class DatabaseHelper
{
    private static function getUniquePath(string $pathType): string
    {

        $db = new MySQLWrapper();
        $CREATE_TIMES = 3;
        for ($i = 0; $i < $CREATE_TIMES; $i++) {
            $randomPath = self::generateRandomString();
            if ($pathType === "share") $stmt = $db->prepare("SELECT * FROM images WHERE share_path = ?");
            else if ($pathType === "delete") $stmt = $db->prepare("SELECT * FROM images WHERE delete_path = ?");
            $stmt->bind_param("s", $randomPath);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows <= 0) break;
            if ($i >= 2) throw new Exception("Could not make a unique URL.");
        }
        return $randomPath;
    }
    public static function saveImage($mediaType): array
    {

        $sharePath = DatabaseHelper::getUniquePath('share');
        $deletePath = DatabaseHelper::getUniquePath('delete');

        $newFileName = sprintf("uploads/%s.%s", $sharePath, $mediaType);
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $newFileName)) {
            throw new Exception('ファイルの保存に失敗しました。/n');
        }

        $now = new DateTime();
        $expirationTime = '+10 days';
        $expireDate = $now->modify($expirationTime)->format('Y-m-d H:i:s');;
        $viewCount = 0;
        // データ登録
        $db = new MySQLWrapper();
        $stmt = $db->prepare(
            "
            INSERT INTO images (file_name,share_path, delete_path, view_count, last_viewed_at) 
            values (?, ?, ?, ?, NOW());"
        );

        $stmt->bind_param("sssi", $newFileName, $sharePath, $deletePath, $viewCount);
        $stmt->execute();
        $basePath = Settings::env("BASE_URL");

        return ["sharePath" => sprintf("%s/%s/%s", $basePath, $mediaType, $sharePath), "deletePath" => sprintf("%s/%s", $basePath, $deletePath)];
    }

    public static function generateRandomString($length = 20): string
    {
        return bin2hex(random_bytes($length / 2)); // 16進数に変換
    }


    public static function doesDeletePathExist($path): bool
    {
        $db = new MySQLWrapper();
        $stmt = $db->prepare("SELECT * FROM images WHERE delete_path = ?");
        $stmt->bind_param("s", $path);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }


    public static function deleteImageFile($deletePath): void
    {

        $db = new MySQLWrapper();
        $stmt = $db->prepare("UPDATE images SET is_expired = TRUE WHERE delete_path = ?");
        $stmt->bind_param("s", $deletePath);
        $stmt->execute();


        $stmt = $db->prepare("SELECT * FROM images WHERE delete_path = ?");
        $stmt->bind_param("s", $deletePath);
        $stmt->execute();
        $result = $stmt->get_result();
        $filename = $result->fetch_assoc()["file_name"];
        if (file_exists($filename)) {
            unlink($filename);
        }

        return;
    }
    public static function updateViewedDate($sharePath): void {
        $db = new MySQLWrapper();
        $stmt = $db->prepare("UPDATE images SET last_viewed_at = NOW() WHERE share_path = ?");
        $stmt->bind_param("s", $sharePath);
        $stmt->execute();
        return;
    }
    public static function addViewCount($sharePath): void
    {
        $db = new MySQLWrapper();
        $stmt = $db->prepare("UPDATE images SET view_count = view_count + 1 WHERE share_path = ?");
        $stmt->bind_param("s", $sharePath);
        $stmt->execute();
        return;
    }
    public static function getViewCount($sharePath): int
    {
        $db = new MySQLWrapper();
        $stmt = $db->prepare("SELECT * FROM images WHERE share_path = ?");
        $stmt->bind_param("s", $sharePath);
        $stmt->execute();
        $data = $stmt->get_result();
        $viewCount = $data->fetch_assoc()["view_count"];
        return $viewCount;
    }
    public static function isExpired($sharePath): string
    {
        $db = new MySQLWrapper();
        $stmt = $db->prepare("SELECT * FROM images WHERE share_path = ?");
        $stmt->bind_param("s", $sharePath);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $isExpired = $data["is_expired"];
        return $isExpired;
    }
    public static function doesIpAddressExist($ip_address): bool
    {
        $db = new MySQLWrapper();
        $stmt = $db->prepare("SELECT * FROM request_logs WHERE ip_address = ?");
        $stmt->bind_param("s", $ip_address);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public static function isRequestAllowed(): bool {
        
        $ip_address = $_SERVER['REMOTE_ADDR'];
        if (!self::doesIpAddressExist($ip_address)) return true;

        $db = new MySQLWrapper();
        $stmt = $db->prepare("SELECT * FROM request_logs WHERE ip_address = ?");
        $stmt->bind_param("s", $ip_address);
        $stmt->execute();
        $result = $stmt->get_result();
        $lastRequest = $result->fetch_assoc()["last_request"];
        $lastRequest = new DateTime($lastRequest);
        $now = new DateTime();
        $minInterval = 60;
        $interval = $now->getTimestamp() - $lastRequest->getTimestamp();

        return $interval > $minInterval;
    }
    public static function saveRequestLog(): void
    {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $db = new MySQLWrapper();
        $now = new DateTime();
        $now = $now->format('Y-m-d H:i:s');

        if (self::doesIpAddressExist($ip_address)) {
            $stmt = $db->prepare("UPDATE request_logs SET last_request = ? WHERE ip_address = ?");
            $stmt->bind_param("ss", $now, $ip_address);
        } else {
            $stmt = $db->prepare("INSERT INTO request_logs (ip_address, last_request) 
            VALUES (?, ?)");
            $stmt->bind_param("ss", $ip_address, $now);
        }

        $stmt->execute();
        return;
    }

    public static function deleteUnaccecedImage(): void {

        $db = new MySQLWrapper();
        $stmt = $db->prepare("UPDATE images SET is_expired = TRUE WHERE last_viewed_at < NOW() - INTERVAL 10 DAY");
        $stmt->execute();

        $stmt = $db->prepare("SELECT * FROM images WHERE is_expired = TRUE");
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        foreach($data as $row) {
            $filename = $row["file_name"];
            if (file_exists($filename)) {
                unlink($filename);
            }
        }
        return;
    }
}
