<?php
$db = new PDO('sqlite:database/database.sqlite');
$tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);

echo "Tables in SQLite Database:\n";
foreach ($tables as $tableName) {
    echo "- $tableName\n";
    $columns = $db->query("PRAGMA table_info(`$tableName`)")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "  * {$column['name']} ({$column['type']})\n";
    }
}
