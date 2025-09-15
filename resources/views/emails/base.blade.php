<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AssetLab Notification')</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 40px;
            text-align: center;
        }
        
        .email-header h1 {
            margin: 0 0 10px 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .email-header .subtitle {
            margin: 0;
            opacity: 0.9;
            font-size: 16px;
        }
        
        .email-body {
            padding: 40px;
        }
        
        .header h1 {
            color: #1f2937;
            margin: 0 0 20px 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .header .subtitle {
            color: #6b7280;
            margin: 0 0 20px 0;
            font-size: 16px;
        }
        
        .content {
            font-size: 16px;
            line-height: 1.6;
        }
        
        .content p {
            margin: 0 0 16px 0;
        }
        
        .content h3 {
            color: #1f2937;
            margin: 30px 0 15px 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .content h4 {
            color: #374151;
            margin: 20px 0 10px 0;
            font-size: 16px;
            font-weight: 600;
        }
        
        .info-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #374151;
        }
        
        .info-value {
            color: #1f2937;
            text-align: right;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
        }
        
        .status-badge.success {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-badge.pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .status-badge.approved {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .status-badge.rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .alert {
            padding: 16px 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid;
        }
        
        .alert-success {
            background-color: #f0fdf4;
            border-left-color: #22c55e;
            color: #15803d;
        }
        
        .alert-info {
            background-color: #eff6ff;
            border-left-color: #3b82f6;
            color: #1e40af;
        }
        
        .alert-warning {
            background-color: #fffbeb;
            border-left-color: #f59e0b;
            color: #d97706;
        }
        
        .alert-danger {
            background-color: #fef2f2;
            border-left-color: #ef4444;
            color: #dc2626;
        }
        
        .email-footer {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 30px 40px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        
        .email-footer a {
            color: #3b82f6;
            text-decoration: none;
        }
        
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 10px 0;
        }
        
        .button:hover {
            background-color: #2563eb;
        }
        
        .button-secondary {
            background-color: #6b7280;
        }
        
        .button-secondary:hover {
            background-color: #4b5563;
        }
        
        ul, ol {
            margin: 15px 0;
            padding-left: 25px;
        }
        
        li {
            margin: 5px 0;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 0 10px;
            }
            
            .email-header,
            .email-body,
            .email-footer {
                padding: 20px;
            }
            
            .info-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .info-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>AssetLab</h1>
            <p class="subtitle">Laboratory & Equipment Management System</p>
        </div>
        
        <div class="email-body">
            @yield('content')
        </div>
        
        <div class="email-footer">
            <p>
                This email was sent from AssetLab System<br>
                <a href="{{ url('/') }}">{{ config('app.name') }}</a>
            </p>
            <p>
                If you have any questions, please contact your system administrator.
            </p>
        </div>
    </div>
</body>
</html>