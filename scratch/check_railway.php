<?php
try {
    $pdo = new PDO('mysql:host=yamabiko.proxy.rlwy.net;port=22135;dbname=railway', 'root', 'hvPPNTMmxhvuwIJVEMNGQWKCucvjqlyU', [
        PDO::ATTR_TIMEOUT => 5,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "Connected to Railway!\n";
    $stmt = $pdo->query("SHOW TABLES");
    print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
} catch (Exception $e) {
    echo "Error Railway: " . $e->getMessage() . "\n";
}
