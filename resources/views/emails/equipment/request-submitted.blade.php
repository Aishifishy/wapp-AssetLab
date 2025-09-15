@extends('emails.base')

@section('title', 'Equipment Borrow Request Submitted')

@section('content')
<div class="header">
    <h1>Equipment Request Submitted</h1>
</div>

<div class="content">
    <p><strong>Hello {{ $user->name }}!</strong></p>
    
    <p>Your equipment borrow request has been successfully submitted and is now pending approval.</p>

    <div class="info-card">
        <div class="info-item">
            <span class="info-label">Request ID:</span>
            <span class="info-value">#{{ $equipmentRequest->id }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Equipment:</span>
            <span class="info-value">{{ $equipment->name }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Category:</span>
            <span class="info-value">{{ $equipment->category->name ?? 'N/A' }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Requested From:</span>
            <span class="info-value">{{ $equipmentRequest->requested_from->format('F j, Y \a\t g:i A') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Requested Until:</span>
            <span class="info-value">{{ $equipmentRequest->requested_until->format('F j, Y \a\t g:i A') }}</span>
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
        <br>Your request has been sent to the equipment administrators for review. You will receive another email once your request has been approved or if any changes are needed.
    </div>

    <h3>Request Details</h3>
    @if($equipmentRequest->purpose)
    <p><strong>Purpose:</strong></p>
    <p>{{ $equipmentRequest->purpose }}</p>
    @endif



    <div class="alert alert-warning">
        <strong>⚠️ Important Reminders:</strong>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>Please wait for approval before collecting the equipment</li>
            <li>Equipment must be returned on or before the expected return date</li>
            <li>Report any damages immediately to avoid penalties</li>
            <li>Contact the administrator if you need to cancel or modify your request</li>
        </ul>
    </div>

    @if($equipment->description)
    <p><strong>Equipment Description:</strong> {{ $equipment->description }}</p>
    @endif

    <p><strong>Submitted on:</strong> {{ $equipmentRequest->created_at->format('F j, Y \a\t g:i A') }}</p>
</div>
@endsection