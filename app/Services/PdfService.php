<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

/**
 * PDF Service using DOMPDF for PDF generation
 */
class PdfService
{
    public function __construct()
    {
        // No dependencies needed for DOMPDF-only implementation
    }

    /**
     * Generate PDF from view using DOMPDF
     */
    public function generatePdfFromView(string $view, array $data = [], array $options = []): string
    {
        return $this->generatePdfWithDomPdf($view, $data, $options);
    }

    /**
     * Generate PDF response using DOMPDF
     */
    public function generatePdfResponse(string $view, array $data = [], array $options = [], ?string $filename = null): Response
    {
        $pdfContent = $this->generatePdfFromView($view, $data, $options);

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Length' => strlen($pdfContent),
        ];

        if ($filename) {
            $headers['Content-Disposition'] = 'inline; filename="' . $filename . '"';
        }

        return response($pdfContent, 200, $headers);
    }

    /**
     * Get paper size configuration for Legal paper
     */
    public function getPaperSize(string $labelSize): array
    {
        // Return Legal paper format (8.5" x 14")
        return [8.5, 14, 'in', 'portrait'];
    }

    /**
     * Generate PDF using DOMPDF
     */
    private function generatePdfWithDomPdf(string $view, array $data, array $options): string
    {
        try {
            Log::info('PdfService: Using DOMPDF to generate PDF', [
                'view' => $view,
                'data_keys' => array_keys($data),
                'options' => $options
            ]);

            // Use loadView directly
            $pdf = Pdf::loadView($view, $data);

            // Apply paper size if provided
            if (isset($options['paper_size'])) {
                $paperSize = $options['paper_size'];
                $pdf->setPaper([$paperSize['width'], $paperSize['height']], 'portrait');
                Log::info('PdfService: Paper size set', [
                    'width' => $paperSize['width'],
                    'height' => $paperSize['height']
                ]);
            }

            $pdfContent = $pdf->output();
            Log::info('PdfService: PDF generated successfully', [
                'pdf_size' => strlen($pdfContent)
            ]);

            return $pdfContent;

        } catch (\Exception $e) {
            Log::error('PdfService: DOMPDF failed', [
                'view' => $view,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception('DOMPDF failed to generate PDF: ' . $e->getMessage());
        }
    }
}
