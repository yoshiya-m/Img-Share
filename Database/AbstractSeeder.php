<?php
namespace Database;

use Database\MySQLWrapper;

abstract class AbstractSeeder implements Seeder {
    protected MySQLWrapper $conn;
    protected ?string $tableName = null;

    protected array $tableColumns = [];

    const AVAILABLE_TYPES = [
        'int' => 'i',
        'float' => 'd',
        'string' => 's',
        'Carbon\Carbon' => 's'
    ];

    public function __construct(MySQLWrapper $conn) {
        $this->conn = $conn;
    }

    public function seed(): void {
        $data = $this->createRowData();

        if ($this->tableName === null) throw new \Exception('Class requires a table name');
        if (empty($this->tableColumns)) throw new \Exception('Class requires a columns');

        foreach ($data as $row) {
            $this->validateRow($row);
            $this->insertRow($row);
        }
    }

    protected function validateRow(array $row): void {
        if (count($row) !== count ($this->tableColumns)) throw new \Exception('Row does not match the');
    
        foreach ($row as $i => $value) {
            $columnDataType = $this->tableColumns[$i]['data_type'];
            $columnName = $this->tableColumns[$i]['column_name'];

            if(!isset(static::AVAILABLE_TYPES[$columnDataType])) throw new \InvalidArgumentException(sprintf("The data type %s is not an available data type.", $columnDataType));

            // PHPは、値のデータタイプを返すget_debug_type()関数とgettype()関数の両方を提供しています。クラス名でも機能します。https://www.php.net/manual/en/function.get-debug-type.php を参照してください。
            // get_debug_typeはネイティブのPHP 8タイプを返します。例えば、floatsのgettype、gettype(4.5)は、ネイティブのデータタイプ'float'ではなく文字列'double'を返します。
            // print("get_debug: " . get_debug_type($value) . " column data type: " . $columnDataType . PHP_EOL);
            if (get_debug_type($value) !== $columnDataType) throw new \InvalidArgumentException(sprintf("Value for %s should be of type %s. Here is the current value: %s", $columnName, $columnDataType, json_encode($value)));
        }
    }

    protected function insertRow(array $row): void {
        // カラム名を取得します。
        $columnNames = array_map(function($columnInfo){ return $columnInfo['column_name'];}, $this->tableColumns);

        // クエリを準備する際、count($row)のプレースホルダー '?' があります。bind_param関数はこれらにデータを挿入します。
        $placeholders = str_repeat('?,', count($row) - 1) . '?';

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->tableName,
            implode(', ', $columnNames),
            $placeholders
        );

        $stmt = $this->conn->prepare($sql);

        // implodeは配列を一つの文字列に結合し、その文字列を返します。
        $dataTypes = implode(array_map(function($columnInfo){ return static::AVAILABLE_TYPES[$columnInfo['data_type']];}, $this->tableColumns));

        // bind paramsは文字の配列（文字列）を取り、それぞれに値を挿入します。
        // 例：$stmt->bind_param('iss', ...array_values([1, 'John', 'john@example.com'])) は、ステートメントに整数、文字列、文字列を挿入します。
        $stmt->bind_param($dataTypes, ...array_values($row));

        $stmt->execute();
    }


}