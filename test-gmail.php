<?php

// Simple Gmail SMTP Test
require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('🎉 Gmail SMTP Test from AssetLab - Your email system is working!', function ($message) {
        $message->to('assetlab.mail@gmail.com')
                ->subject('AssetLab - Gmail SMTP Test');
    });
    
    echo "✅ Email sent successfully via Gmail SMTP!\n";
    echo "Check your inbox at assetlab.mail@gmail.com\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}