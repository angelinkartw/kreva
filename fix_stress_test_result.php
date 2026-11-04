<?php
/**
 * fix_stress_test_result.php
 *
 * This script fixes the JSON syntax error in `stress-test-result.json`.
 * The original file contains multiple JSON objects concatenated one after another
 * without commas or an outer array, which leads to the "End of file expected"
 * parsing error shown by the editor.
 *
 * The script performs the following steps:
 *   1. Load the file content.
 *   2. Trim whitespace and ensure there is a newline at the end.
 *   3. Insert a comma between adjacent objects (pattern: "}\s*{")
 *   4. Wrap the whole content in a JSON array (`[ ... ]`).
 *   5. Save the corrected content back to the same file.
 *
 * After running the script, the file will be a valid JSON array of objects
 * that can be parsed without errors.
 */

$filePath = __DIR__ . '/stress-test-result.json';

if (!file_exists($filePath)) {
    echo "File not found: $filePath\n";
    exit(1);
}

$content = file_get_contents($filePath);
// Remove any trailing whitespace
$content = trim($content);

// Insert commas between consecutive JSON objects
$fixed = preg_replace('/}\s*{/', '},\n{', $content);

// Wrap with array brackets
$fixed = "[\n" . $fixed . "\n]";

file_put_contents($filePath, $fixed);

echo "Fixed JSON saved to $filePath\n";
?>
