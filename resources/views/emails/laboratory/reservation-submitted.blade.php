@extends('emails.base')

@section('title', 'Laboratory Reservation Submitted')

@section('content')
<div class="header">
    <h1>Reservation Submitted</h1>
    <p class="subtitle">Your laboratory reservation request has been received</p>
</div>

<div class="content">
    <p><strong>Hello {{ $user->name }}!</strong></p>
    
    <p>Your laboratory reservation request has been successfully submitted and is now pending approval from the laboratory administrators.</p>

    <div class="info-card">
        <div class="info-item">
            <span class="info-label">Reservation ID:</span>
            <span class="info-value">#{{ $reservation->id }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Laboratory:</span>
            <span class="info-value">{{ $laboratory->name }}</span>
        </div>
        @if($laboratory->location)
        <div class="info-item">
            <span class="info-label">Location:</span>
            <span class="info-value">{{ $laboratory->location }}</span>
        </div>
        @endif
        <div class="info-item">
            <span class="info-label">Date:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($reservation->start_datetime)->format('F j, Y') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Time:</span>
            <span class="info-value">
                {{ \Carbon\Carbon::parse($reservation->start_datetime)->format('g:i A') }} - 
                {{ \Carbon\Carbon::parse($reservation->end_datetime)->format('g:i A') }}
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Duration:</span>
            <span class="info-value">
                {{ \Carbon\Carbon::parse($reservation->start_datetime)->diffForHumans(\Carbon\Carbon::parse($reservation->end_datetime), true) }}
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Status:</span>
            <span class="info-value">
                <span class="status-badge pending">🕒 Pending Approval</span>
            </span>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>📋 What happens next?</strong>
        <br>Your request has been sent to the laboratory administrators for review. You will receive another email once your request has been approved or if any changes are needed.
    </div>

    <h3>Reservation Details</h3>
    <div class="info-card">
        <div class="info-item">
            <span class="info-label">Purpose:</span>
            <span class="info-value">{{ $reservation->purpose }}</span>
        </div>
        @if($reservation->expected_participants)
        <div class="info-item">
            <span class="info-label">Expected Participants:</span>
            <span class="info-value">{{ $reservation->expected_participants }}</span>
        </div>
        @endif
        @if($reservation->special_requirements)
        <div class="info-item">
            <span class="info-label">Special Requirements:</span>
            <span class="info-value">{{ $reservation->special_requirements }}</span>
        </div>
        @endif
    </div>

    <div class="info-card">
        <h4 style="margin-bottom: 10px; color: #1f2937;">🏢 Laboratory Information:</h4>
        <div class="info-item">
            <span class="info-label">Laboratory Name:</span>
            <span class="info-value">{{ $laboratory->name }}</span>
        </div>
        @if($laboratory->description)
        <div class="info-item">
            <span class="info-label">Description:</span>
            <span class="info-value">{{ $laboratory->description }}</span>
        </div>
        @endif
        <div class="info-item">
            <span class="info-label">Capacity:</span>
            <span class="info-value">{{ $laboratory->capacity }} students</span>
        </div>
        @if($laboratory->equipment_available)
        <div class="info-item">
            <span class="info-label">Available Equipment:</span>
            <span class="info-value">{{ $laboratory->equipment_available }}</span>
        </div>
        @endif
    </div>

    <div class="alert alert-warning">
        <strong>⚠️ Important Reminders:</strong>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Please wait for approval before using the laboratory</li>
            <li>Arrive on time for your scheduled session</li>
            <li>Follow all laboratory safety protocols and guidelines</li>
            <li>Contact the administrator if you need to cancel or modify your request</li>
            <li>Clean up after your session and report any issues</li>
        </ul>
    </div>

    <div class="info-card">
        <h4 style="margin-bottom: 10px; color: #1f2937;">📞 Need Help?</h4>
        <p>If you have any questions about your reservation or need to make changes, please contact the laboratory administrators. They will respond to your request as soon as possible.</p>
    </div>

    <p><strong>Submitted on:</strong> {{ $reservation->created_at->format('F j, Y \a\t g:i A') }}</p>

    <p>Thank you for using our laboratory reservation system!</p>

    <p><strong>Best regards,</strong><br>
    The AssetLab Team</p>
</div>
@endsection