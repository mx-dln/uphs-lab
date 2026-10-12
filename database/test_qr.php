<?php
require __DIR__ . '/../vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

$options = new QROptions([
    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
    'eccLevel' => QRCode::ECC_M,
    'scale' => 10,
    'imageBase64' => false,
]);

try {
    $qrcode = new QRCode($options);
    $out = $qrcode->render('https://example.com', __DIR__ . '/../public/qr/test.png');
    echo 'Render output: ' . var_export($out, true) . PHP_EOL;
    echo 'File exists: ' . (file_exists(__DIR__ . '/../public/qr/test.png') ? 'yes' : 'no') . PHP_EOL;
    if (file_exists(__DIR__ . '/../public/qr/test.png')) {
        echo 'File size: ' . filesize(__DIR__ . '/../public/qr/test.png') . PHP_EOL;
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    echo 'Class: ' . get_class($e) . PHP_EOL;
}
