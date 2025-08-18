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
            padding: 0.6in; 
            margin: 0;
            padding: 0;
            background-color: white;
        }
        
        /* .page {
            width: 8.5in;
            height: 14in;
            padding: 0.6in; 
            page-break-after: always;
            display: flex;
            flex-direction: column;
            position: relative;
            box-sizing: border-box;
        } */
        
        .page:last-child {
            page-break-after: avoid;
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
            gap: 0.05in;
            width: 100%;
            justify-content: flex-start;
            align-content: flex-start;
            max-width: 6in;
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
            
            .page {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pageIndex => $pageLabels): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="page">
            
            <div class="page-content">
                <?php if($pageIndex === 0): ?>
                    <div class="header">
                        <h1>Equipment Barcode Labels</h1>
                        <div class="info">
                            Total Labels: <?php echo e($totalLabels); ?> | Generated: <?php echo e($generatedAt); ?>

                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="labels-container">
                    <div class="labels-grid">
                        <?php $__currentLoopData = $pageLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="label">
                                <div class="equipment-name"><?php echo e(Str::limit($item['equipment']->name, 25)); ?></div>
                                
                                <?php if($item['equipment']->category): ?>
                                    <div class="equipment-category"><?php echo e(Str::limit($item['equipment']->category->name, 20)); ?></div>
                                <?php endif; ?>
                                
                                <div class="barcode-container">
                                    <img src="<?php echo e($item['barcodeImage']); ?>" alt="Barcode" class="barcode-image">
                                    <div class="barcode-text"><?php echo e($item['barcode']); ?></div>
                                </div>
                                
                                <div class="equipment-id">ID: <?php echo e($item['equipment']->id); ?></div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
                        
                        <?php for($i = $pageLabels->count(); $i < $labelsPerPage; $i++): ?>
                            <div class="label" style="border: none; box-shadow: none;"></div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    <div class="footer">
        ResourEase Equipment Management System - Generated on <?php echo e($generatedAt); ?>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\wappResourEase\resources\views/admin/equipment/barcode/multiple-labels.blade.php ENDPATH**/ ?>