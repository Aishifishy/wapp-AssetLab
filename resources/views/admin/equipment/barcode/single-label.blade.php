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
            padding: 0;
            background-color: white;
        }
        
        .label-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            margin: 0.5in 0.5in 0.8in 0.5in;
            position: relative;
            z-index: 1;
            max-height: calc(14in - 1.6in);
            max-width: calc(8.5in - 1.2in);
            overflow: hidden;
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
            width: 2in;
            height: 1in;
            padding: 0.08in;
        }
        
        .equipment-name {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 0.05in;
            color: #000;
            line-height: 1.1;
            font-size: 8pt;
        }
        
        .equipment-category {
            color: #666;
            margin-bottom: 0.05in;
            font-style: italic;
            font-size: 6pt;
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
            font-size: 6pt;
        }
        
        .equipment-id {
            color: #888;
            margin-top: 0.03in;
            font-size: 5pt;
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
        }
    </style>
</head>
<body>
    <div class="label-container">
        <div class="label">
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
        AssetLab Equipment Management System - Generated: {{ $generatedAt }}
    </div>
</body>
</html>
