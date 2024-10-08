<?php

namespace Database\Migrations;

use Database\SchemaMigration;

class CreateTableRequest_logs implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE request_logs (
                id INT PRIMARY KEY AUTO_INCREMENT,
                ip_address VARCHAR(50) NOT NULL,
                last_request DATETIME NOT NULL
            )"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE images"
        ];
    }
}
