<?php

namespace App\Services\Shared;

use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class PdfService
{
    /**
     * Create a new mPDF instance with standard configuration
     */
    public function createMpdf(array $config = []): Mpdf
    {
        $defaultConfig = [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_header' => 0,
            'margin_footer' => 0,
            'orientation' => 'P',
        ];

        $config = array_merge($defaultConfig, $config);

        return new Mpdf($config);
    }

    /**
     * Generate PDF for 80mm ticket format
     */
    public function generate80mmTicket(string $html, string $filename = 'ticket.pdf'): \Illuminate\Http\Response
    {
        $mpdf = $this->createMpdf([
            'format' => [80, 297], // 80mm width, variable height
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5,
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate PDF for A4 format
     */
    public function generateA4(string $html, string $filename = 'document.pdf'): \Illuminate\Http\Response
    {
        $mpdf = $this->createMpdf([
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate PDF for A5 format (stickers)
     */
    public function generateA5(string $html, string $filename = 'sticker.pdf'): \Illuminate\Http\Response
    {
        $mpdf = $this->createMpdf([
            'format' => 'A5',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5,
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate PDF for A6 format (small stickers)
     */
    public function generateA6(string $html, string $filename = 'sticker.pdf'): \Illuminate\Http\Response
    {
        $mpdf = $this->createMpdf([
            'format' => 'A6',
            'margin_left' => 3,
            'margin_right' => 3,
            'margin_top' => 3,
            'margin_bottom' => 3,
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate PDF with custom format
     */
    public function generateCustom(string $html, array $config, string $filename = 'document.pdf'): \Illuminate\Http\Response
    {
        $mpdf = $this->createMpdf($config);
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Get PDF as string (for storage or email)
     */
    public function getPdfAsString(string $html, array $config = []): string
    {
        $mpdf = $this->createMpdf($config);
        $mpdf->WriteHTML($html);
        return $mpdf->Output('', 'S');
    }
}

