<?php
// Final check: look for any remaining C3 A2 or C3 B0 corrupted sequences
$dirs = [__DIR__ . '/public', __DIR__ . '/admin', __DIR__ . '/lms'];
$issues = 0;

foreach ($dirs as $dir) {
    foreach (glob($dir . '/*.php') as $file) {
        $c = file_get_contents($file);
        $name = basename($file);

        // Check for corrupted emoji start (C3 B0 followed by C5)
        if (strpos($c, "\xC3\xB0\xC5") !== false) {
            echo "CORRUPT EMOJI: $name\n";
            $issues++;
        }
        // Check for corrupted arrow (C3 A2 E2 80 A0)
        if (strpos($c, "\xC3\xA2\xE2\x80\xA0") !== false) {
            echo "CORRUPT ARROW: $name\n";
            $issues++;
        }
        // Check for corrupted symbols (C3 A2 C5 93)
        if (strpos($c, "\xC3\xA2\xC5\x93") !== false) {
            echo "CORRUPT SYMBOL: $name\n";
            $issues++;
        }
    }
}

echo "\nTotal issues: $issues\n";
echo $issues === 0 ? "ALL CLEAN! No more encoding corruption found.\n" : "Some issues remain.\n";
