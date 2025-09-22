@extends('emails.base')

@section('title', 'Welcome to AssetLab')

@section('content')
<div class="header">
    <h1>Welcome to AssetLab! 🎉</h1>
    <p class="subtitle">Email verification successful - Your account is now active</p>
</div>

<div class="content">
    <p><strong>Hello {{ $user->name }}!</strong></p>
    
    <div class="alert alert-success">
        <strong>✅ Email Verification Success!</strong>
        <p style="margin: 5px 0;">Your email address has been successfully verified. You now have full access to all AssetLab features!</p>
    </div>
    
    <p>Welcome to AssetLab, the Laboratory & Equipment Management System! Your registration and verification are now complete, and you can access all the features available to students.</p>

    <div class="info-card">
        <div class="info-item">
            <span class="info-label">Account Name:</span>
            <span class="info-value">{{ $user->name }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Email Address:</span>
            <span class="info-value">{{ $user->email }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Registration Date:</span>
            <span class="info-value">{{ $user->created_at->format('F j, Y \a\t g:i A') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Account Status:</span>
            <span class="info-value">
                <span class="status-badge success">✅ Active & Verified</span>
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Email Verified:</span>
            <span class="info-value">{{ $user->email_verified_at->format('F j, Y \a\t g:i A') }}</span>
        </div>
    </div>

    <div class="alert alert-success">
        <strong>🎯 What you can do with AssetLab:</strong>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li><strong>📦 Borrow Equipment:</strong> Request laboratory equipment for your projects and studies</li>
            <li><strong>🏢 Reserve Laboratories:</strong> Book computer laboratories for class activities and research</li>
            <li><strong>📅 View Schedules:</strong> Check laboratory availability and your reservation history</li>
            <li><strong>📊 Track Requests:</strong> Monitor the status of your equipment and laboratory requests</li>
            <li><strong>📧 Get Notifications:</strong> Receive email updates on your requests and reservations</li>
        </ul>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/ruser/dashboard') }}" 
           style="display: inline-block; background-color: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600;">
            🚀 Access Your Dashboard
        </a>
    </div>

    <h3>Getting Started Guide</h3>
    
    <div class="info-card">
        <h4 style="margin-bottom: 10px; color: #1f2937;">📦 Equipment Borrowing Process:</h4>
        <ol style="margin: 10px 0; padding-left: 20px;">
            <li>Browse available equipment in the system</li>
            <li>Submit a borrow request with purpose and dates</li>
            <li>Wait for administrator approval</li>
            <li>Collect your approved equipment</li>
            <li>Return equipment by the due date</li>
        </ol>
    </div>

    <div class="info-card">
        <h4 style="margin-bottom: 10px; color: #1f2937;">🏢 Laboratory Reservation Process:</h4>
        <ol style="margin: 10px 0; padding-left: 20px;">
            <li>Check laboratory availability in the schedule</li>
            <li>Submit a reservation request with session details</li>
            <li>Wait for administrator approval</li>
            <li>Use the laboratory during your scheduled time</li>
            <li>Follow all safety protocols and guidelines</li>
        </ol>
    </div>

    <div class="alert alert-info">
        <strong>💡 Pro Tips:</strong>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Submit requests well in advance to ensure availability</li>
            <li>Provide detailed purpose information to expedite approvals</li>
            <li>Check your email regularly for status updates</li>
            <li>Return equipment promptly to maintain good borrowing status</li>
            <li>Report any issues or damages immediately</li>
        </ul>
    </div>

    <h3>Important Guidelines</h3>
    
    <div class="alert alert-warning">
        <strong>📋 Please remember:</strong>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li><strong>Responsibility:</strong> You are responsible for all borrowed equipment</li>
            <li><strong>Punctuality:</strong> Return equipment and end lab sessions on time</li>
            <li><strong>Care:</strong> Handle all equipment with care and report damages</li>
            <li><strong>Compliance:</strong> Follow all safety protocols and usage guidelines</li>
            <li><strong>Respect:</strong> Be considerate of other users and shared resources</li>
        </ul>
    </div>

    <div class="info-card">
        <h4 style="margin-bottom: 10px; color: #1f2937;">📞 Need Help?</h4>
        <p>If you have any questions or need assistance with using AssetLab, please don't hesitate to contact the laboratory administrators. They're here to help you make the most of our facilities and equipment.</p>
    </div>

    <p>Thank you for joining AssetLab! We're excited to support your academic and research endeavors.</p>

    <p><strong>Happy learning!</strong><br>
    The AssetLab Team</p>
</div>
@endsection