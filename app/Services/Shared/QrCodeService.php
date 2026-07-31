<?php

namespace App\Services\Shared;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

class QrCodeService
{
    public function generateSvg(string $token): string
    {
        $qrCode = new QrCode(
            data: route('scan', ['token' => $token]),
            size: 400,
            margin: 10
        );

        return (new SvgWriter())->write($qrCode)->getString();
    }
}
