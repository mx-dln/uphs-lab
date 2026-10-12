<?php
$base = __DIR__ . '/../resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base));
$count = 0;

foreach ($files as $file) {
    if ($file->getExtension() !== 'php') continue;
    $path = $file->getPathname();
    $content = file_get_contents($path);

    if (!str_contains($content, 'layouts/app.php')) continue;

    $orig = $content;
    echo "Fixing: " . basename(dirname($path)) . '/' . $file->getFilename() . "\n";

    // Remove {{content}} line (with surrounding newlines)
    $content = str_replace("{{content}}\n", '', $content);
    $content = str_replace("\n{{content}}", '', $content);

    // Replace the first require app.php with ob_start()
    $search1 = "<?php require __DIR__ . '/../layouts/app.php'; ?>\n";
    if (str_contains($content, $search1)) {
        $content = str_replace($search1, "<?php ob_start(); ?>\n", $content);
    } else {
        $search1 = "<?php require __DIR__ . '/../layouts/app.php'; ?>";
        if (str_contains($content, $search1)) {
            $content = str_replace($search1, "<?php ob_start(); ?>", $content);
        }
    }

    // Replace close.php with content capture + app include
    $search2 = "<?php require __DIR__ . '/../layouts/close.php'; ?>";
    $replace2 = "<?php \$content = ob_get_clean(); require __DIR__ . '/../layouts/app.php'; ?>";
    if (str_contains($content, $search2)) {
        $content = str_replace($search2, $replace2, $content);
    } else {
        // Try without closing tag
        $search2b = "<?php require __DIR__ . '/../layouts/close.php'; ?>\n";
        if (str_contains($content, $search2b)) {
            $content = str_replace($search2b, $replace2 . "\n", $content);
        }
    }

    if ($content !== $orig) {
        file_put_contents($path, $content);
        $count++;
    }
}

// Clean close.php
$closePath = $base . '/layouts/close.php';
file_put_contents($closePath, "<script>\n    lucide.createIcons();\n</script>\n");

echo "\nFixed $count files.\n";
