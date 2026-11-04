<?php
for ($i = 1; $i <= 3; $i++) {
    try {
        echo "Attempt $i...\n";
        $pdo = new PDO('mysql:host=yamabiko.proxy.rlwy.net;port=22135;dbname=railway', 'root', 'hvPPNTMmxhvuwIJVEMNGQWKCucvjqlyU', [
            PDO::ATTR_TIMEOUT => 10,
        ]);
        echo "Success on attempt $i!\n";
        $stmt = $pdo->query("SHOW TABLES");
        print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
        break;
    } catch (Exception $e) {
        echo "Failed attempt $i: " . $e->getMessage() . "\n";
        sleep(2);
    }
}
