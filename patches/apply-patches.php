<?php
/**
 * Composer post-autoload-dump hook.
 * Applies vendor patches idempotently after install/update.
 * Safe to run multiple times — skips already-patched files.
 */

$patchesDir = dirname(__FILE__);
$patches = [
    [
        'file' => $patchesDir . '/spatie-medialibrary-model-type-null.patch',
        'target' => dirname(__DIR__) . '/vendor/spatie/laravel-medialibrary/src/Conversions/ConversionCollection.php',
        'marker' => 'PATCH_SpatieModelTypeNull_v1',
    ],
];

foreach ($patches as $patch) {
    if (!file_exists($patch['target'])) {
        continue; // vendor not installed yet
    }

    $contents = file_get_contents($patch['target']);

    if (strpos($contents, $patch['marker']) !== false) {
        continue; // already patched
    }

    if (!file_exists($patch['file'])) {
        fwrite(STDERR, "Patch file not found: {$patch['file']}\n");
        continue;
    }

    $patchContents = file_get_contents($patch['file']);

    // Extract the marker from the patch file (first line comment)
    if (preg_match('/MARKER:\s*(\S+)/', $patchContents, $m)) {
        $marker = $m[1];
    } else {
        $marker = $patch['marker'];
    }

    // Apply as a string replacement: search → replace
    // The patch file format: first section is SEARCH, second (after ===) is REPLACE
    $sections = preg_split('/^===\s*$/m', $patchContents);

    if (count($sections) >= 2) {
        $search = trim($sections[0]);
        $replace = trim($sections[1]);
        // Remove marker comments
        $search = preg_replace('/^#.*$/m', '', $search);
        $replace = preg_replace('/^#.*$/m', '', $replace);
        $search = trim($search);
        $replace = trim($replace);

        if (strpos($contents, $search) !== false) {
            $contents = str_replace($search, $replace, $contents);
            // Add marker comment at top
            $contents = "/* {$marker} */\n" . $contents;
            file_put_contents($patch['target'], $contents);
            echo "  Patched: {$patch['target']}\n";
        } else {
            echo "  Already patched or source changed: {$patch['target']}\n";
        }
    }
}
