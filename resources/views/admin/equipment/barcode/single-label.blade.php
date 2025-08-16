<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Barcode Label</title>
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
            padding: 0.5in;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: white;
            position: relative;
            box-sizing: border-box;
            overflow: hidden;
        }
        
        /* Document border for professional appearance */
        .document-border {
            position: absolute;
            top: 0.25in;
            left: 0.25in;
            right: 0.25in;
            bottom: 0.25in;
            border: 2px solid #000;
            border-radius: 4px;
            pointer-events: none;
        }
        
        .label-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            max-width: 7.5in;
            max-height: 13in;
        }
        
        .label {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            border: 2px solid #000;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* Size-specific label dimensions */
        .label.small {
            width: 2.5in;
            height: 1in;
            padding: 0.1in;
        }
        
        .label.standard {
            width: 3in;
            height: 1.25in;
            padding: 0.15in;
        }
        
        .label.medium {
            width: 3.5in;
            height: 1.5in;
            padding: 0.2in;
        }
        
        .label.large {
            width: 4in;
            height: 2in;
            padding: 0.25in;
        }
        
        .equipment-name {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0.1in;
            color: #000;
        }
        
        .equipment-category {
            color: #666;
            margin-bottom: 0.1in;
            font-style: italic;
        }
        
        .barcode-container {
            margin: 0.1in 0;
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
            margin-top: 0.05in;
            color: #000;
        }
        
        .equipment-id {
            color: #888;
            margin-top: 0.05in;
        }
        
        /* Small label styles */
        .label.small .equipment-name {
            font-size: 10pt;
        }
        
        .label.small .equipment-category {
            font-size: 8pt;
        }
        
        .label.small .barcode-text {
            font-size: 8pt;
        }
        
        .label.small .equipment-id {
            font-size: 6pt;
        }
        
        /* Standard label styles */
        .label.standard .equipment-name {
            font-size: 12pt;
        }
        
        .label.standard .equipment-category {
            font-size: 10pt;
        }
        
        .label.standard .barcode-text {
            font-size: 10pt;
        }
        
        .label.standard .equipment-id {
            font-size: 8pt;
        }
        
        /* Medium label styles */
        .label.medium .equipment-name {
            font-size: 14pt;
        }
        
        .label.medium .equipment-category {
            font-size: 12pt;
        }
        
        .label.medium .barcode-text {
            font-size: 12pt;
        }
        
        .label.medium .equipment-id {
            font-size: 10pt;
        }
        
        /* Large label styles */
        .label.large .equipment-name {
            font-size: 16pt;
        }
        
        .label.large .equipment-category {
            font-size: 14pt;
        }
        
        .label.large .barcode-text {
            font-size: 14pt;
        }
        
        .label.large .equipment-id {
            font-size: 12pt;
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
    <!-- Document border for professional appearance -->
    <div class="document-border"></div>
    
    <div class="label-container">
        <div class="label {{ $labelSize }}">
            <div class="equipment-name">{{ $equipment->name }}</div>
            
            @if($equipment->category)
                <div class="equipment-category">{{ $equipment->category->name }}</div>
            @endif
            
            <div class="barcode-container">
                <img src="{{ $barcodeImage }}" alt="Barcode" class="barcode-image">
                <div class="barcode-text">{{ $barcode }}</div>
            </div>
            
            <div class="equipment-id">ID: {{ $equipment->id }}</div>
        </div>
    </div>
    
    <div class="footer">
        Generated: {{ $generatedAt }}
    </div>
</body>
</html>
