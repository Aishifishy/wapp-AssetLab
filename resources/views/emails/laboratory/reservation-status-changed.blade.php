@extends('emails.base')

@section('title', 'Laboratory Reservation Status Updated')

@section('content')
<div class="header">
    <h1>Reservation Update</h1>
    <p class="subtitle">
        @if($reservation->status === 'approved')
            ✅ Reservation Approved
        @elseif($reservation->status === 'rejected')
            ❌ Reservation Rejected
        @elseif($reservation->status === 'cancelled')
            ⚪ Reservation Cancelled
        @elseif($reservation->status === 'completed')
            🎉 Reservation Completed
        @else
            📋 Status Updated
        @endif
    </p>
</div>

<div class="content">
    <p><strong>Hello {{ $user->name }}!</strong></p>
    
    <p>
        @if($reservation->status === 'approved')
            Excellent! Your laboratory reservation has been <strong>approved</strong>. You can now use the laboratory during your scheduled time.
        @elseif($reservation->status === 'rejected')
            We regret to inform you that your laboratory reservation has been <strong>rejected</strong>.
        @elseif($reservation->status === 'cancelled')
            Your laboratory reservation has been <strong>cancelled</strong> as requested.
        @elseif($reservation->status === 'completed')
            Your laboratory session has been marked as <strong>completed</strong>. Thank you for using the facilities responsibly.
        @else
            Your laboratory reservation status has been updated.
        @endif
    </p>

    <div class="info-card">
        <div class="info-item">
            <span class="info-label">Reservation ID:</span>
            <span class="info-value">#{{ $reservation->id }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Laboratory:</span>
            <span class="info-value">{{ $laboratory->name }}</span>
        </div>
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
            <span class="info-label">Previous Status:</span>
            <span class="info-value">{{ ucfirst($previousStatus ?? 'Unknown') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Current Status:</span>
            <span class="info-value">
                @if($reservation->status === 'approved')
                    <span class="status-badge success">✅ Approved</span>
                @elseif($reservation->status === 'rejected')
                    <span class="status-badge cancelled">❌ Rejected</span>
                @elseif($reservation->status === 'cancelled')
                    <span class="status-badge cancelled">⚪ Cancelled</span>
                @elseif($reservation->status === 'completed')
                    <span class="status-badge success">🎉 Completed</span>
                @else
                    <span class="status-badge pending">{{ ucfirst($reservation->status) }}</span>
                @endif
            </span>
        </div>
    </div>

    @if($reservation->status === 'approved')
        <div class="alert alert-success">
            <strong>🎉 Your laboratory session is confirmed!</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li><strong>Laboratory:</strong> {{ $laboratory->name }}</li>
                <li><strong>Date & Time:</strong> {{ \Carbon\Carbon::parse($reservation->start_datetime)->format('F j, Y \a\t g:i A') }} - {{ \Carbon\Carbon::parse($reservation->end_datetime)->format('g:i A') }}</li>
                @if($laboratory->location)
                <li><strong>Location:</strong> {{ $laboratory->location }}</li>
                @endif
                <li><strong>Capacity:</strong> {{ $laboratory->capacity }} students</li>
            </ul>
        </div>

        @if($reservation->admin_notes)
        <p><strong>Administrator Notes:</strong></p>
        <div class="info-card">
            <p>{{ $reservation->admin_notes }}</p>
        </div>
        @endif

        <div class="alert alert-warning">
            <strong>📋 Important Guidelines:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Arrive on time for your scheduled session</li>
                <li>Follow all laboratory safety protocols and rules</li>
                <li>Clean up after your session</li>
                <li>Report any equipment issues immediately</li>
                <li>Notify administration if you need to cancel (at least 24 hours in advance)</li>
            </ul>
        </div>

    @elseif($reservation->status === 'rejected')
        @if($reservation->rejection_reason)
        <p><strong>Reason for Rejection:</strong></p>
        <div class="info-card">
            <p>{{ $reservation->rejection_reason }}</p>
        </div>
        @else
        <div class="alert alert-info">
            <p>No specific reason was provided for the rejection. Please contact the administrator for more details.</p>
        </div>
        @endif

        <div class="alert alert-info">
            <strong>💡 What you can do:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Review the reason for rejection</li>
                <li>Check for alternative time slots</li>
                <li>Submit a new reservation with different dates/times</li>
                <li>Contact the laboratory administrator for guidance</li>
            </ul>
        </div>

    @elseif($reservation->status === 'completed')
        <div class="alert alert-success">
            <strong>✅ Session Completed:</strong>
            <br>Thank you for using the laboratory facilities responsibly. We hope your session was productive!
        </div>

        @if($reservation->completion_notes)
        <p><strong>Session Notes:</strong></p>
        <div class="info-card">
            <p>{{ $reservation->completion_notes }}</p>
        </div>
        @endif

        <div class="alert alert-info">
            <strong>📝 Feedback Welcome:</strong>
            <br>If you have any feedback about your laboratory experience, please don't hesitate to contact the administration.
        </div>
    @endif

    <h3>Original Reservation Details</h3>
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
        <div class="info-item">
            <span class="info-label">Submitted:</span>
            <span class="info-value">{{ $reservation->created_at->format('F j, Y \a\t g:i A') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Updated:</span>
            <span class="info-value">{{ $reservation->updated_at->format('F j, Y \a\t g:i A') }}</span>
        </div>
    </div>

    <p>If you have any questions about this update, please contact the laboratory administrator.</p>
</div>
@endsection