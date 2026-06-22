<?php
/**
 * Quick fix for remaining corrupted arrows and symbols
 * in berita_detail.php and any other files
 */
$dirs = [__DIR__ . '/public', __DIR__ . '/admin', __DIR__ . '/lms'];
$totalFixed = 0;

// Build mapping of all corrupted → correct strings
// These are specific known corruptions from the PowerShell encoding issue
$replacements = [
    // Arrows
    "\xC3\xA2\xE2\x80\xA0" => "\xE2\x86\x90",       // ← (leftwards arrow)
    "\xC3\xA2\xE2\x80\xBA\xC2\x92" => "\xE2\x86\x92", // → (rightwards arrow)
    // Checkmarks and symbols
    "\xC3\xA2\xC5\x93" => "\xE2\x9C",                  // ✓ ✍ partial fix
    "\xC3\xA2\xE2\x80\x9C" => "\xE2\x80\x94",          // — em dash
    "\xC3\xA2\xE2\x82\xAC\xE2\x80\x9C" => "\xE2\x80\x94", // — em dash alt
    "\xC3\xA2\xE2\x82\xAC\xE2\x84\xA2" => "\xE2\x80\x99", // ' right single quote
    "\xC3\xA2\xE2\x82\xAC\xC2\x9C" => "\xE2\x80\x9C",     // " left double quote
    "\xC3\xA2\xE2\x82\xAC\xC2\x9D" => "\xE2\x80\x9D",     // " right double quote
    "\xC3\xA2\xE2\x82\xAC\xC2\xA2" => "\xE2\x80\xA2",     // • bullet
];

foreach ($dirs as $dir) {
    foreach (glob($dir . '/*.php') as $file) {
        $content = file_get_contents($file);
        $original = $content;

        foreach ($replacements as $broken => $fixed) {
            $content = str_replace($broken, $fixed, $content);
        }

        if ($content !== $original) {
            file_put_contents($file, $content);
            echo "FIXED: " . basename($file) . "\n";
            $totalFixed++;
        }
    }
}

echo "\nFixed: $totalFixed files\n";
