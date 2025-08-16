<?php

namespace App\Services;

use App\Models\Equipment;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;

/**
 * Service for generating printable equipment barcodes
 */
class BarcodeService
{
    protected $generator;
    protected $svgGenerator;
    protected $htmlGenerator;

    public function __construct()
    {
        $this->generator = new BarcodeGeneratorPNG();
        $this->svgGenerator = new BarcodeGeneratorSVG();
        $this->htmlGenerator = new BarcodeGeneratorHTML();
    }

    /**
     * Generate barcode image for a single equipment
     */
    public function generateBarcodeImage(Equipment $equipment, $format = 'png', $width = 2, $height = 50)
    {
        $barcode = $equipment->getIdentificationCode();
        
        Log::info('BarcodeService: Generating barcode image', [
            'equipment_id' => $equipment->id,
            'barcode' => $barcode,
            'format' => $format,
            'barcode_length' => strlen($barcode ?? ''),
            'barcode_raw' => bin2hex($barcode ?? '')
        ]);
        
        if (!$barcode) {
            throw new \Exception('Equipment does not have a barcode');
        }

        // Validate and sanitize barcode string
        $barcode = trim($barcode);
        if (empty($barcode)) {
            throw new \Exception('Barcode is empty or contains only whitespace');
        }

        // Ensure barcode contains only valid characters for CODE 128
        // CODE 128 supports printable ASCII characters
        if (!preg_match('/^[\x20-\x7E]+$/', $barcode)) {
            throw new \Exception('Barcode contains invalid characters. Only printable ASCII characters are allowed.');
        }

        try {
            Log::info('BarcodeService: Attempting barcode generation with CODE 128', [
                'barcode' => $barcode,
                'format' => $format
            ]);
            
            switch (strtolower($format)) {
                case 'svg':
                    return $this->svgGenerator->getBarcode($barcode, 'C128', $width, $height);
                case 'html':
                    return $this->htmlGenerator->getBarcode($barcode, 'C128', $width, $height);
                case 'png':
                default:
                    return $this->generator->getBarcode($barcode, 'C128', $width, $height);
            }
        } catch (\Exception $e) {
            Log::warning('BarcodeService: CODE 128 generation failed, trying CODE 39', [
                'barcode' => $barcode,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            // If CODE 128 fails, try with Code 39 which is more tolerant
            try {
                switch (strtolower($format)) {
                    case 'svg':
                        return $this->svgGenerator->getBarcode($barcode, 'C39', $width, $height);
                    case 'html':
                        return $this->htmlGenerator->getBarcode($barcode, 'C39', $width, $height);
                    case 'png':
                    default:
                        return $this->generator->getBarcode($barcode, 'C39', $width, $height);
                }
            } catch (\Exception $fallbackException) {
                Log::error('BarcodeService: Both CODE 128 and CODE 39 generation failed', [
                    'barcode' => $barcode,
                    'code128_error' => $e->getMessage(),
                    'code39_error' => $fallbackException->getMessage(),
                    'code128_line' => $e->getLine(),
                    'code39_line' => $fallbackException->getLine()
                ]);
                
                throw new \Exception('Error generating barcode for "' . $barcode . '". CODE 128 error: ' . $e->getMessage() . '. CODE 39 error: ' . $fallbackException->getMessage());
            }
        }
    }

    /**
     * Generate base64 encoded barcode for embedding in HTML/PDF
     */
    public function generateBarcodeBase64(Equipment $equipment, $width = 2, $height = 50)
    {
        try {
            $barcodeData = $this->generateBarcodeImage($equipment, 'png', $width, $height);
            return 'data:image/png;base64,' . base64_encode($barcodeData);
        } catch (\Exception $e) {
            throw new \Exception('Error generating base64 barcode: ' . $e->getMessage());
        }
    }

    /**
     * Generate printable barcode labels PDF for single equipment
     */
    public function generateSingleBarcodePDF(Equipment $equipment, $labelSize = 'standard')
    {
        try {
            $barcode = $equipment->getIdentificationCode();
            $barcodeImage = $this->generateBarcodeBase64($equipment, 2, 50);
            
            $data = [
                'equipment' => $equipment,
                'barcode' => $barcode,
                'barcodeImage' => $barcodeImage,
                'labelSize' => $labelSize,
                'generatedAt' => now()->format('M d, Y H:i:s')
            ];

            $pdf = Pdf::loadView('admin.equipment.barcode.single-label', $data);
            $pdf->setPaper($this->getPaperSize($labelSize));
            
            return $pdf;

        } catch (\Exception $e) {
            Log::error('BarcodeService: Error generating single barcode PDF', [
                'equipment_id' => $equipment->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            throw new \Exception('Error generating barcode PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate printable barcode labels PDF for multiple equipment
     */
    public function generateMultipleBarcodePDF(Collection $equipment, $labelSize = 'standard', $labelsPerPage = null)
    {
        try {
            // Calculate labels per page based on label size
            if ($labelsPerPage === null) {
                $labelsPerPage = $this->getLabelsPerPage($labelSize);
            }
            
            $barcodes = $equipment->map(function ($item) {
                return [
                    'equipment' => $item,
                    'barcode' => $item->getIdentificationCode(),
                    'barcodeImage' => $this->generateBarcodeBase64($item, 2, 50),
                ];
            });

            // Only create pages for actual labels (no empty pages)
            $pages = $barcodes->chunk($labelsPerPage);
            
            // Filter out any empty pages
            $pages = $pages->filter(function ($page) {
                return $page->count() > 0;
            });
            
            $data = [
                'pages' => $pages,
                'labelSize' => $labelSize,
                'labelsPerPage' => $labelsPerPage,
                'totalLabels' => $barcodes->count(),
                'totalPages' => $pages->count(),
                'generatedAt' => now()->format('M d, Y H:i:s')
            ];

            $pdf = Pdf::loadView('admin.equipment.barcode.multiple-labels', $data);
            $pdf->setPaper($this->getPaperSize($labelSize));
            
            return $pdf;

        } catch (\Exception $e) {
            Log::error('BarcodeService: Error generating multiple barcode PDF', [
                'equipment_count' => $equipment->count(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            throw new \Exception('Error generating multiple barcode PDF: ' . $e->getMessage());
        }
    }

    /**
     * Get optimal labels per page based on actual label dimensions and Legal paper size
     */
    protected function getLabelsPerPage($labelSize)
    {
        // Legal paper dimensions: 8.5" x 14" 
        // Ultra-conservative available space to prevent ANY overflow: ~6" x 9"
        $availableWidth = 6.0;   // Ultra-conservative width
        $availableHeight = 9.0;  // Ultra-conservative height with large margins
        
        // Smaller gap between labels
        $gap = 0.05; // Reduced gap to 0.05"
        
        switch ($labelSize) {
            case 'small':
                $labelWidth = 1.8 + $gap;  // Smaller label size
                $labelHeight = 0.9 + $gap;
                $maxLabels = 12; // Hard limit
                break;
            case 'standard':
                $labelWidth = 2.2 + $gap;  // Smaller label size
                $labelHeight = 1.0 + $gap;
                $maxLabels = 8;  // Hard limit
                break;
            case 'medium':
                $labelWidth = 2.8 + $gap;  // Smaller label size
                $labelHeight = 1.3 + $gap;
                $maxLabels = 6;  // Hard limit
                break;
            case 'large':
                $labelWidth = 3.5 + $gap;  // Smaller label size
                $labelHeight = 1.8 + $gap;
                $maxLabels = 4;  // Hard limit
                break;
            default:
                $labelWidth = 2.2 + $gap;
                $labelHeight = 1.0 + $gap;
                $maxLabels = 8;  // Hard limit
        }
        
        // Calculate how many labels fit with ultra-conservative approach
        $labelsPerRow = max(1, floor($availableWidth / $labelWidth));
        $labelsPerColumn = max(1, floor($availableHeight / $labelHeight));
        
        $calculated = $labelsPerRow * $labelsPerColumn;
        
        // Apply hard maximum to prevent overflow
        return min($calculated, $maxLabels);
    }

    /**
     * Generate barcode sheet for all equipment
     */
    public function generateAllEquipmentBarcodesPDF($labelSize = 'standard')
    {
        $equipment = Equipment::whereNotNull('barcode')
            ->with('category')
            ->orderBy('name')
            ->get();

        if ($equipment->isEmpty()) {
            throw new \Exception('No equipment with barcodes found');
        }

        return $this->generateMultipleBarcodePDF($equipment, $labelSize);
    }

    /**
     * Generate barcode sheet for specific equipment IDs
     */
    public function generateSelectedEquipmentBarcodesPDF(array $equipmentIds, $labelSize = 'standard')
    {
        $equipment = Equipment::whereIn('id', $equipmentIds)
            ->whereNotNull('barcode')
            ->with('category')
            ->orderBy('name')
            ->get();

        if ($equipment->isEmpty()) {
            throw new \Exception('No equipment with barcodes found for selected items');
        }

        return $this->generateMultipleBarcodePDF($equipment, $labelSize);
    }

    /**
     * Get paper size based on label size
     */
    protected function getPaperSize($labelSize)
    {
        // Use Legal paper size (8.5" x 14") for seamless printing
        // Create label dimensions within the legal paper
        switch ($labelSize) {
            case 'small':
                return [8.5 * 72, 14 * 72, 'pt', 'portrait']; // Legal paper, small label content
            case 'medium':
                return [8.5 * 72, 14 * 72, 'pt', 'portrait']; // Legal paper, medium label content
            case 'large':
                return [8.5 * 72, 14 * 72, 'pt', 'portrait']; // Legal paper, large label content
            case 'standard':
            default:
                return [8.5 * 72, 14 * 72, 'pt', 'portrait']; // Legal paper, standard label content
        }
    }

    /**
     * Get available label sizes
     */
    public static function getLabelSizes()
    {
        return [
            'small' => 'Small (2.5" × 1") on Legal Paper',
            'standard' => 'Standard (3" × 1.25") on Legal Paper',
            'medium' => 'Medium (3.5" × 1.5") on Legal Paper',
            'large' => 'Large (4" × 2") on Legal Paper'
        ];
    }
}
