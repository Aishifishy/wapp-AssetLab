<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratory Reservation Status Update</title>
    @vite('resources/css/app.css')
</head>
<body class="email-body">
    <div class="email-header">
        <h1>Laboratory Reservation Update</h1>
    </div>
      <div class="email-content">
        <p>Dear {{ $reservation->user->name }},</p>
        
        <p>Your laboratory reservation status has been updated to 
            <span class="status-{{ $reservation->status }}">{{ ucfirst($reservation->status) }}</span>.
        </p>
        
        <h2>Reservation Details:</h2>
        
        <div class="info-row">
            <span class="info-label">Laboratory:</span>
            <span>{{ $reservation->laboratory->name }} ({{ $reservation->laboratory->building }}, Room {{ $reservation->laboratory->room_number }})</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Date:</span>
            <span>{{ $reservation->reservation_date->format('F d, Y') }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Time:</span>
            <span>{{ \Carbon\Carbon::parse($reservation->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('h:i A') }}</span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Purpose:</span>
            <span>{{ $reservation->purpose }}</span>
        </div>
          @if($reservation->status === 'rejected' && $reservation->rejection_reason)
            <div class="info-row info-row-top">
                <span class="info-label">Rejection Reason:</span>
                <span>{{ $reservation->rejection_reason }}</span>
            </div>
        @endif
          @if($reservation->status === 'approved')
            <div class="reminders-box">
                <h3 class="reminders-heading">Important Reminders:</h3>
                <ul>
                    <li>Please bring a valid school ID when using the laboratory.</li>
                    <li>Follow all laboratory rules and regulations.</li>
                    <li>If you need to cancel your reservation, please do so at least 24 hours in advance.</li>
                </ul>
            </div>
        @endif
          <div class="view-button-container">
            <p>You can view the complete details of your reservation by logging into your account.</p>
            <p><a href="{{ route('ruser.laboratory.reservations.show', $reservation) }}" class="view-reservation-button">View Reservation</a></p>
        </div>
    </div>
      <div class="email-footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} WappResourEase. All rights reserved.</p>
    </div>
</body>
</html>
