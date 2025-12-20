<?php

namespace App\Services;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

class QRCodeGeneratorService
{
    /**
     * Generate QR code as PNG data (base64 ready)
     *
     * @param string $text
     * @param int $size
     * @return string
     */
    public function generateQRCodePng($text, $size = 150)
    {
        $qrCode = QrCode::create($text)
            ->setSize($size)
            ->setMargin(5)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $pngWriter = new PngWriter();
        $result = $pngWriter->write($qrCode);

        return $result->getString();
    }

    /**
     * Generate QR code as SVG string (for web display)
     *
     * @param string $text
     * @param int $size
     * @return string
     */
    public function generateQRCodeSvg($text, $size = 150)
    {
        $qrCode = QrCode::create($text)
            ->setSize($size)
            ->setMargin(5)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $svgWriter = new SvgWriter();
        $result = $svgWriter->write($qrCode);

        return $result->getString();
    }
}