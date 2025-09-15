@extends('emails.base')

@section('title', 'New Equipment Borrow Request - Approval Required')

@section('content')
<div class="header">
    <h1>New Equipment Request</h1>
    <p class="subtitle">Approval Required</p>
</div>

<div class="content">
    <p><strong>Hello Administrator!</strong></p>
    
    <p>A new equipment borrow request has been submitted and requires your approval.</p>

    <div class="info-card">
        <div class="info-item">
            <span class="info-label">Request ID:</span>
            <span class="info-value">#{{ $equipmentRequest->id }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Student:</span>
            <span class="info-value">{{ $user->name }} ({{ $user->email }})</span>
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
                <span class="status-badge pending">🕒 Pending Your Approval</span>
            </span>
        </div>
    </div>

    <h3>Request Details</h3>
    @if($equipmentRequest->purpose)
    <p><strong>Purpose:</strong></p>
    <p>{{ $equipmentRequest->purpose }}</p>
    @endif



    <div class="alert alert-warning">
        <strong>⚡ Action Required:</strong>
        <br>Please review this equipment request and take appropriate action. The student is waiting for approval.
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/admin/equipment/borrow-requests') }}" 
           style="display: inline-block; background-color: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600;">
            📦 View in Admin Panel
        </a>
    </div>

    <div class="info-card">
        <div class="info-item">
            <span class="info-label">Submitted:</span>
            <span class="info-value">{{ $equipmentRequest->created_at->format('F j, Y \a\t g:i A') }}</span>
        </div>
        @if($equipment->location)
        <div class="info-item">
            <span class="info-label">Equipment Location:</span>
            <span class="info-value">{{ $equipment->location }}</span>
        </div>
        @endif
        @if($equipment->condition)
        <div class="info-item">
            <span class="info-label">Current Condition:</span>
            <span class="info-value">{{ ucfirst($equipment->condition) }}</span>
        </div>
        @endif
    </div>

    <p><em>This is an automated notification from AssetLab. Please log in to the admin panel to manage this request.</em></p>
</div>
@endsection