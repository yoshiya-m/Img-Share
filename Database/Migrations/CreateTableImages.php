<?php

namespace Database\Migrations;

use Database\SchemaMigration;

class CreateTableImages implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE images (
                id INT PRIMARY KEY AUTO_INCREMENT,
                file_name VARCHAR(50) NOT NULL,
                share_path VARCHAR(50) NOT NULL,
                delete_path VARCHAR(50) NOT NULL,
                view_count INT NOT NULL,
                is_expired BOOLEAN DEFAULT FALSE,
                UNIQUE INDEX unique_share_path (share_path),
                UNIQUE INDEX unique_delete_path (delete_path),
                expired_at DATETIME NOT NULL

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
