<?php

namespace App\Services\Shared;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

class QrCodeService
{
    public function generateSvg(string $token): string
    {
        $path = route('scan', ['token' => $token], absolute: false);
        $baseUrl = rtrim((string) config('app.qr_base_url'), '/');
        $scanUrl = $baseUrl !== ''
            ? $baseUrl.$path
            : route('scan', ['token' => $token]);

        $qrCode = new QrCode(
            data: $scanUrl,
            size: 400,
            margin: 10,
            foregroundColor: new Color(10, 135, 130), // -color-primary: #0s8782
        );

        return (new SvgWriter)->write($qrCode)->getString();
    }
}

// $qr = new \Endroid\QrCode\QrCode(data: 'https://yourdomain.com/scan/test-token-123', size: 400, margin: 10, foregroundColor: new Endroid\QrCode\Color\Color(10, 135, 130), backgroundColor: new Endroid\QrCode\Color\Color(255, 255, 255));
