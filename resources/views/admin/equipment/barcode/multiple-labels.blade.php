<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Barcode Labels</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            width: 8.5in;
            height: 14in;
            margin: 0;
            padding: 0;
            background-color: white;
        }
        
        .page {
            width: 8.5in;
            height: 14in;
            padding: 0.6in; /* Increased padding for ultra-safe margins */
            page-break-after: always;
            display: flex;
            flex-direction: column;
            position: relative;
            box-sizing: border-box;
        }
        
        .page:last-child {
            page-break-after: avoid;
        }
        
        /* Document border - larger margins for safety */
        .document-border {
            position: absolute;
            top: 0.3in;
            left: 0.3in;
            right: 0.3in;
            bottom: 0.3in;
            border: 2px solid #000;
            border-radius: 4px;
            pointer-events: none;
        }
        
        /* Inner content area - ultra-conservative */
        .page-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            margin: 0.5in 0.5in 0.8in 0.5in; /* Much larger margins */
            position: relative;
            z-index: 1;
            max-height: calc(14in - 1.6in); /* Ultra-conservative height */
            max-width: calc(8.5in - 1.2in);  /* Ultra-conservative width */
            overflow: hidden;
        }
        
        .header {
            text-align: center;
            margin-bottom: 0.15in;
            padding: 0.1in;
            border-bottom: 1px solid #333;
            background-color: #f8f9fa;
            border-radius: 4px;
            flex-shrink: 0;
        }
        
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 0.03in;
            color: #000;
        }
        
        .header .info {
            font-size: 9pt;
            color: #666;
        }
        
        .labels-container {
            flex-grow: 1;
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            max-height: calc(100% - 0.25in);
            overflow: hidden;
        }
        
        .labels-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.05in; /* Minimal gap to save space */
            width: 100%;
            justify-content: flex-start;
            align-content: flex-start;
            max-width: 6in; /* Ultra-conservative width constraint */
            max-height: 100%;
        }
        
        .label {
            border: 2px solid #000;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        /* Actual label dimensions - matching single label sizes */
        .labels-grid.small .label {
            width: 2.5in;
            height: 1in;
            padding: 0.08in;
        }
        
        .labels-grid.standard .label {
            width: 3in;
            height: 1.25in;
            padding: 0.1in;
        }
        
        .labels-grid.medium .label {
            width: 3.5in;
            height: 1.5in;
            padding: 0.12in;
        }
        
        .labels-grid.large .label {
            width: 4in;
            height: 2in;
            padding: 0.15in;
        }
        
        .equipment-name {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0.05in;
            color: #000;
            line-height: 1.1;
        }
        
        .equipment-category {
            color: #666;
            margin-bottom: 0.05in;
            font-style: italic;
        }
        
        .barcode-container {
            margin: 0.05in 0;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .barcode-image {
            max-width: 90%;
            max-height: 60%;
            height: auto;
            display: block;
        }
        
        .barcode-text {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            letter-spacing: 1px;
            margin-top: 0.03in;
            color: #000;
        }
        
        .equipment-id {
            color: #888;
            margin-top: 0.03in;
        }
        
        /* Small label styles */
        .labels-grid.small .equipment-name {
            font-size: 8pt;
        }
        
        .labels-grid.small .equipment-category {
            font-size: 6pt;
        }
        
        .labels-grid.small .barcode-text {
            font-size: 6pt;
        }
        
        .labels-grid.small .equipment-id {
            font-size: 5pt;
        }
        
        /* Standard label styles */
        .labels-grid.standard .equipment-name {
            font-size: 10pt;
        }
        
        .labels-grid.standard .equipment-category {
            font-size: 8pt;
        }
        
        .labels-grid.standard .barcode-text {
            font-size: 8pt;
        }
        
        .labels-grid.standard .equipment-id {
            font-size: 7pt;
        }
        
        /* Medium label styles */
        .labels-grid.medium .equipment-name {
            font-size: 12pt;
        }
        
        .labels-grid.medium .equipment-category {
            font-size: 10pt;
        }
        
        .labels-grid.medium .barcode-text {
            font-size: 10pt;
        }
        
        .labels-grid.medium .equipment-id {
            font-size: 8pt;
        }
        
        /* Large label styles */
        .labels-grid.large .equipment-name {
            font-size: 14pt;
        }
        
        .labels-grid.large .equipment-category {
            font-size: 12pt;
        }
        
        .labels-grid.large .barcode-text {
            font-size: 12pt;
        }
        
        .labels-grid.large .equipment-id {
            font-size: 10pt;
        }
        
        .footer {
            position: fixed;
            bottom: 0.35in;
            left: 0.6in;
            right: 0.6in;
            font-size: 7pt;
            color: #666;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 0.05in;
            background-color: white;
            z-index: 10;
        }
        
        @page {
            margin: 0;
            size: legal portrait;
        }
        
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .page {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    @foreach($pages as $pageIndex => $pageLabels)
        <div class="page">
            <!-- Document border for professional appearance -->
            <div class="document-border"></div>
            
            <div class="page-content">
                @if($pageIndex === 0)
                    <div class="header">
                        <h1>Equipment Barcode Labels</h1>
                        <div class="info">
                            Total Labels: {{ $totalLabels }} | Label Size: {{ ucfirst($labelSize) }} | Generated: {{ $generatedAt }}
                        </div>
                    </div>
                @endif
                
                <div class="labels-container">
                    <div class="labels-grid {{ $labelSize }}">
                        @foreach($pageLabels as $item)
                            <div class="label">
                                <div class="equipment-name">{{ Str::limit($item['equipment']->name, 25) }}</div>
                                
                                @if($item['equipment']->category)
                                    <div class="equipment-category">{{ Str::limit($item['equipment']->category->name, 20) }}</div>
                                @endif
                                
                                <div class="barcode-container">
                                    <img src="{{ $item['barcodeImage'] }}" alt="Barcode" class="barcode-image">
                                    <div class="barcode-text">{{ $item['barcode'] }}</div>
                                </div>
                                
                                <div class="equipment-id">ID: {{ $item['equipment']->id }}</div>
                            </div>
                        @endforeach
                        
                        {{-- Fill empty slots if needed --}}
                        @for($i = $pageLabels->count(); $i < $labelsPerPage; $i++)
                            <div class="label" style="border: none; box-shadow: none;"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    
    <div class="footer">
        ResourEase Equipment Management System - Generated on {{ $generatedAt }}
    </div>
</body>
</html>
