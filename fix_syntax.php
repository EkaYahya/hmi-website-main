<?php
// Fix remaining issues using hex-encoded patterns

$fixes = [
    // Garbled question mark icon
    [hex2bin('c3a2e2809c'), hex2bin('e29d93')],  // â" → ❓
    // Garbled X icon
    [hex2bin('c3a2c28c'), hex2bin('e29d8c')],     // âŒ → ❌
];

// Fix nested PHP in asset() — these are literal strings  

$dirs = [__DIR__ . '/admin', __DIR__ . '/lms'];
$totalFixed = 0;

foreach ($dirs as $dir) {
    foreach (glob($dir . '/*.php') as $file) {
        $content = file_get_contents($file);
        $original = $content;
        $name = basename($file);

        // Apply simple string replacements for garbled icons
        foreach ($fixes as $fix) {
            $content = str_replace($fix[0], $fix[1], $content);
        }

        // Fix nested PHP in asset() calls        $searchAsset = "asset('/" . hex2bin('3c3f');
        $offset = 0;
        while (($pos = strpos($content, $searchAsset, $offset)) !== false) {
            $closeSearch = hex2bin('3f3e') . "')";
            $endPos = strpos($content, $closeSearch, $pos);
            if ($endPos !== false) {
                $innerStart = $pos + strlen($searchAsset);
                $innerEnd = $endPos;
                $inner = substr($content, $innerStart, $innerEnd - $innerStart);
                // Remove leading "= " 
                $inner = ltrim($inner, "= \t");

                $replacement = "asset('/' . " . $inner . ")";
                $fullLen = ($endPos + strlen($closeSearch)) - $pos;
                $content = substr($content, 0, $pos) . $replacement . substr($content, $pos + $fullLen);
                $offset = $pos + strlen($replacement);
            } else {
                $offset = $pos + 5;
            }
        }

        if ($content !== $original) {
            file_put_contents($file, $content);
            echo "FIXED: $name\n";
            $totalFixed++;
        }
    }
}

echo "\nTotal: $totalFixed files fixed\n";
