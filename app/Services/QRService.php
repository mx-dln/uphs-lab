<?php

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QRService
{
    private QROptions $options;

    public function __construct()
    {
        $this->options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_M,
            'scale' => 10,
            'imageBase64' => false,
        ]);
    }

    public function generate(string $data, string $filename): string
    {
        $dir = dirname(__DIR__, 2) . '/public/qr/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filePath = $dir . $filename . '.png';
        $qrcode = new QRCode($this->options);
        $qrcode->render($data, $filePath);
        return 'public/qr/' . $filename . '.png';
    }

    public function generateLaboratoryQR(int $labId, string $labName): string
    {
        $url = rtrim($_ENV['APP_URL'] ?? 'http://localhost/computer-lab-system', '/') . '/scan/laboratory/' . $labId;
        $filename = 'lab_' . $labId . '_' . time();
        return $this->generate($url, $filename);
    }

    public function generateEquipmentQR(int $equipmentId, string $assetTag): string
    {
        $url = rtrim($_ENV['APP_URL'] ?? 'http://localhost/computer-lab-system', '/') . '/scan/equipment/' . $equipmentId;
        $filename = 'eq_' . $equipmentId . '_' . $assetTag;
        return $this->generate($url, $filename);
    }

    public function getQRPath(string $filename): string
    {
        return 'public/qr/' . $filename;
    }
}
