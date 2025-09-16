@extends('emails.base')

@section('title', 'Laboratory Schedule Change Notification')

@section('content')
<div class="header">
    <h1>Schedule Change Notice</h1>
    <p class="subtitle">{{ $laboratory->name }}</p>
</div>

<div class="content">
    <p><strong>Hello {{ $user ? $user->name : 'Student' }}!</strong></p>
    
    <p>We're writing to inform you of an important schedule change that may affect your laboratory reservations.</p>

    <div class="info-card">
        <div class="info-item">
            <span class="info-label">Laboratory:</span>
            <span class="info-value">{{ $laboratory->name }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Override Date:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($override->override_date)->format('F j, Y') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Affected Time:</span>
            <span class="info-value">
                @if($override->originalSchedule)
                    {{ $override->originalSchedule->start_time->format('g:i A') }} - {{ $override->originalSchedule->end_time->format('g:i A') }}
                @elseif($override->originalReservation)
                    {{ \Carbon\Carbon::parse($override->originalReservation->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($override->originalReservation->end_time)->format('g:i A') }}
                @else
                    Time details not available
                @endif
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Override Type:</span>
            <span class="info-value">
                @if($override->override_type === 'cancel')
                    <span class="status-badge cancelled">🚫 Cancelled</span>
                @elseif($override->override_type === 'reschedule')
                    <span class="status-badge pending">📅 Rescheduled</span>
                @elseif($override->override_type === 'replace')
                    <span class="status-badge pending">🔄 Replaced</span>
                @else
                    <span class="status-badge pending">📅 Schedule Override</span>
                @endif
            </span>
        </div>
        @if($override->originalSchedule)
            <div class="info-item">
                <span class="info-label">Affected Schedule:</span>
                <span class="info-value">{{ $override->originalSchedule->subject_name }} (Regular Class)</span>
            </div>
        @elseif($override->originalReservation)
            <div class="info-item">
                <span class="info-label">Affected Reservation:</span>
                <span class="info-value">{{ $override->originalReservation->subject ?? $override->originalReservation->purpose }} (Laboratory Reservation)</span>
            </div>
        @endif
    </div>

    @if($override->override_type === 'cancel')
        <div class="alert alert-warning">
            <strong>🚫 Schedule Cancelled:</strong>
            <br>
            @if($override->originalSchedule)
                The regular class schedule has been cancelled for this date.
            @elseif($override->originalReservation)
                Your laboratory reservation has been cancelled.
            @endif
            Reason: {{ $override->reason }}
        </div>
    @else
        <div class="alert alert-info">
            <strong>📅 Schedule Override:</strong>
            <br>
            @if($override->originalSchedule)
                The regular laboratory schedule has been modified for this date.
            @elseif($override->originalReservation)
                Your laboratory reservation has been overridden/rescheduled.
            @endif
            Reason: {{ $override->reason }}
        </div>
        
        @if($override->override_type === 'reschedule' || $override->override_type === 'replace')
            <div class="info-card">
                <h4>New Schedule Details:</h4>
                <div class="info-item">
                    <span class="info-label">New Time:</span>
                    <span class="info-value">
                        {{ \Carbon\Carbon::parse($override->new_start_time)->format('g:i A') }} - 
                        {{ \Carbon\Carbon::parse($override->new_end_time)->format('g:i A') }}
                    </span>
                </div>
                @if($override->new_subject_name)
                    <div class="info-item">
                        <span class="info-label">Subject:</span>
                        <span class="info-value">{{ $override->new_subject_name }}</span>
                    </div>
                @endif
                @if($override->new_instructor_name)
                    <div class="info-item">
                        <span class="info-label">Instructor:</span>
                        <span class="info-value">{{ $override->new_instructor_name }}</span>
                    </div>
                @endif
            </div>
        @endif
    @endif

    @if($override->reason)
    <h3>Reason for Schedule Change</h3>
    <div class="info-card">
        <p>{{ $override->reason }}</p>
    </div>
    @endif

    @if($affectedReservations && count($affectedReservations) > 0)
        <h3>Your Affected Reservations</h3>
        <div class="alert alert-warning">
            <strong>⚠️ Impact on Your Reservations:</strong>
            <br>The following reservation(s) you have are affected by this schedule change:
        </div>

        @foreach($affectedReservations as $reservation)
        <div class="info-card">
            <div class="info-item">
                <span class="info-label">Reservation ID:</span>
                <span class="info-value">#{{ $reservation->id }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Original Time:</span>
                <span class="info-value">
                    {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('F j, Y') }} 
                    {{ \Carbon\Carbon::parse($reservation->start_time)->format('g:i A') }} - 
                    {{ \Carbon\Carbon::parse($reservation->end_time)->format('g:i A') }}
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Purpose:</span>
                <span class="info-value">{{ $reservation->purpose }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    @if($override->override_type === 'cancel')
                        <span class="status-badge cancelled">❌ Cancelled</span>
                    @else
                        <span class="status-badge pending">⚠️ Schedule Changed</span>
                    @endif
                </span>
            </div>
        </div>
        @endforeach

        <div class="alert alert-info">
            <strong>📞 Next Steps:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Contact the laboratory administrator to discuss alternative arrangements</li>
                <li>Consider rescheduling your affected reservations to different time slots</li>
                <li>Check the updated schedule for available alternatives</li>
                <li>We apologize for any inconvenience and will work with you to find suitable alternatives</li>
            </ul>
        </div>
    @endif

    @if($override->notes)
    <h3>Additional Information</h3>
    <div class="info-card">
        <p>{{ $override->notes }}</p>
    </div>
    @endif

    <div class="info-card">
        <div class="info-item">
            <span class="info-label">Override Created:</span>
            <span class="info-value">{{ $override->created_at->format('F j, Y \a\t g:i A') }}</span>
        </div>
        @if($laboratory->location)
        <div class="info-item">
            <span class="info-label">Laboratory Location:</span>
            <span class="info-value">{{ $laboratory->location }}</span>
        </div>
        @endif
        <div class="info-item">
            <span class="info-label">Laboratory Capacity:</span>
            <span class="info-value">{{ $laboratory->capacity }} students</span>
        </div>
    </div>

    <div class="alert alert-warning">
        <strong>📋 Important Reminders:</strong>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Check the laboratory schedule regularly for any updates</li>
            <li>Plan your activities around scheduled closures or overrides</li>
            <li>Contact administration early if you need to reschedule reservations</li>
            <li>Subscribe to calendar updates to stay informed of changes</li>
        </ul>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/ruser/laboratory/calendar') }}" 
           style="display: inline-block; background-color: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600;">
            📅 View Updated Schedule
        </a>
    </div>

    <p>We apologize for any inconvenience this schedule change may cause. If you have any questions or need assistance with rescheduling, please don't hesitate to contact the laboratory administrator.</p>

    <p>Thank you for your understanding and flexibility.</p>
</div>
@endsection