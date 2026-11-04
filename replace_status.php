<?php
$file = 'c:/laragon/www/sisehat/app/Http/Controllers/DashboardController.php';
$content = file_get_contents($file);

$content = str_replace(
    "->where('status', 'Selesai')",
    "->whereIn('status', ['Selesai', 'finished'])",
    $content
);

$content = str_replace(
    "->whereIn('status', ['Menunggu', 'Selesai'])",
    "->whereIn('status', ['Menunggu', 'Selesai', 'finished'])",
    $content
);

$content = str_replace(
    "\$activeAssessment->status === 'Selesai'",
    "in_array(\$activeAssessment->status, ['Selesai', 'finished'])",
    $content
);

file_put_contents($file, $content);
echo "Replacements done.";
