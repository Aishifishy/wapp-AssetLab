@extends('emails.base')

@section('content')
<h1>New Laboratory Reservation Received</h1>

<p>A new laboratory reservation has been submitted and requires your review.</p>

<div class="info-card">
    <h3>Reservation Details</h3>
    <div class="info-row">
        <span class="label">Reservation ID:</span>
        <span class="value">#{{ $reservation->id }}</span>
    </div>
    <div class="info-row">
        <span class="label">Status:</span>
        <span class="value">
            <span class="status-badge {{ $reservation->status === 'pending' ? 'status-pending' : ($reservation->status === 'approved' ? 'status-approved' : 'status-rejected') }}">
                {{ ucfirst($reservation->status) }}
            </span>
        </span>
    </div>
    <div class="info-row">
        <span class="label">Reserved by:</span>
        <span class="value">{{ $reservation->user->fname }} {{ $reservation->user->lname }}</span>
    </div>
    <div class="info-row">
        <span class="label">Contact:</span>
        <span class="value">{{ $reservation->user->email }}</span>
    </div>
    <div class="info-row">
        <span class="label">Date & Time:</span>
        <span class="value">{{ \Carbon\Carbon::parse($reservation->start_datetime)->format('l, F j, Y \a\t g:i A') }}</span>
    </div>
    <div class="info-row">
        <span class="label">Duration:</span>
        <span class="value">{{ \Carbon\Carbon::parse($reservation->start_datetime)->diffInHours(\Carbon\Carbon::parse($reservation->end_datetime)) }} hour(s)</span>
    </div>
</div>

<div class="info-card">
    <h3>Laboratory Information</h3>
    <div class="info-row">
        <span class="label">Laboratory:</span>
        <span class="value">{{ $reservation->laboratory->name }}</span>
    </div>
    <div class="info-row">
        <span class="label">Location:</span>
        <span class="value">{{ $reservation->laboratory->location }}</span>
    </div>
    <div class="info-row">
        <span class="label">Capacity:</span>
        <span class="value">{{ $reservation->laboratory->capacity }} people</span>
    </div>
</div>

@if($reservation->purpose)
<div class="info-card">
    <h3>Purpose/Activity</h3>
    <p>{{ $reservation->purpose }}</p>
</div>
@endif

<div class="admin-actions">
    <h3>Administrative Actions Required</h3>
    <ul>
        <li>Review the reservation details above</li>
        <li>Verify laboratory availability</li>
        <li>Check for any schedule conflicts</li>
        <li>Approve or reject the reservation through the admin panel</li>
        <li>Send confirmation to the user once processed</li>
    </ul>
</div>

<p><strong>Please log in to the admin panel to process this reservation:</strong></p>
<p><a href="{{ url('/admin/laboratory-reservations') }}" style="color: #3b82f6; text-decoration: none;">View in Admin Panel</a></p>

<div class="footer-note">
    <p><em>This is an automated notification. Please do not reply to this email.</em></p>
    <p><em>Submitted on: {{ $reservation->created_at->format('l, F j, Y \a\t g:i A') }}</em></p>
</div>
@endsection