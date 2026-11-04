<?php
$avgFactors = null;
try {
    $val = $avgFactors->score_ov ?? 0;
    echo "Value: $val\n";
} catch (\Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
