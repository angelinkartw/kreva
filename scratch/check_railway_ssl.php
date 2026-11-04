<?php
try {
    $pdo = new PDO('mysql:host=yamabiko.proxy.rlwy.net;port=22135;dbname=railway', 'root', 'hvPPNTMmxhvuwIJVEMNGQWKCucvjqlyU', [
        PDO::MYSQL_ATTR_SSL_CA => true,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ]);
    echo "Connected to Railway with SSL!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
