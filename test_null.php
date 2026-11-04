<?php
$latest = null;
try {
    $val = ($latest->total_score ?? 0) * 20;
    echo "Result: $val\n";
} catch (\Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
