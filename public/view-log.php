<?php
$logFile = __DIR__ . '/storage/scan_debug.log';
echo '<h2>Scan Debug Log</h2><pre>';
if (file_exists($logFile)) {
    echo htmlspecialchars(file_get_contents($logFile));
    echo '</pre><p><a href="?clear=1">Clear Log</a></p>';
    if (isset($_GET['clear'])) {
        file_put_contents($logFile, '');
        echo '<meta http-equiv="refresh" content="0">';
    }
} else {
    echo 'No log yet. Try scanning first.</pre>';
}
