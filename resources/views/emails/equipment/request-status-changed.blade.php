@extends('emails.base')

@section('title', 'Equipment Request Status Updated')

@section('content')
<div class="header">
    <h1>Equipment Request Update</h1>
    <p class="subtitle">
        @if($equipmentRequest->status === 'approved')
            ✅ Request Approved
        @elseif($equipmentRequest->status === 'rejected')
            ❌ Request Rejected
        @elseif($equipmentRequest->status === 'returned')
            🔄 Equipment Returned
        @elseif($equipmentRequest->status === 'cancelled')
            ⚪ Request Cancelled
        @else
            📋 Status Updated
        @endif
    </p>
</div>

<div class="content">
    <p><strong>Hello {{ $user->name }}!</strong></p>
    
    <p>
        @if($equipmentRequest->status === 'approved')
            Great news! Your equipment borrow request has been <strong>approved</strong>. You can now proceed to collect the equipment.
        @elseif($equipmentRequest->status === 'rejected')
            We regret to inform you that your equipment borrow request has been <strong>rejected</strong>.
        @elseif($equipmentRequest->status === 'returned')
            Thank you for returning the equipment. Your borrow request has been marked as <strong>completed</strong>.
        @elseif($equipmentRequest->status === 'cancelled')
            Your equipment borrow request has been <strong>cancelled</strong> as requested.
        @else
            Your equipment borrow request status has been updated.
        @endif
    </p>

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
            <span class="info-label">Quantity:</span>
            <span class="info-value">{{ $equipmentRequest->quantity }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Previous Status:</span>
            <span class="info-value">{{ ucfirst($previousStatus ?? 'Unknown') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Current Status:</span>
            <span class="info-value">
                @if($equipmentRequest->status === 'approved')
                    <span class="status-badge success">✅ Approved</span>
                @elseif($equipmentRequest->status === 'rejected')
                    <span class="status-badge cancelled">❌ Rejected</span>
                @elseif($equipmentRequest->status === 'returned')
                    <span class="status-badge success">🔄 Returned</span>
                @elseif($equipmentRequest->status === 'cancelled')
                    <span class="status-badge cancelled">⚪ Cancelled</span>
                @else
                    <span class="status-badge pending">{{ ucfirst($equipmentRequest->status) }}</span>
                @endif
            </span>
        </div>
    </div>

    @if($equipmentRequest->status === 'approved')
        <div class="alert alert-success">
            <strong>🎉 Next Steps:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Collect your equipment from the designated location</li>
                <li><strong>Borrow Date:</strong> {{ \Carbon\Carbon::parse($equipmentRequest->borrow_date)->format('F j, Y') }}</li>
                <li><strong>Return Date:</strong> {{ \Carbon\Carbon::parse($equipmentRequest->expected_return_date)->format('F j, Y') }}</li>
                <li>Inspect the equipment before accepting</li>
                <li>Report any issues immediately</li>
            </ul>
        </div>

        @if($equipmentRequest->admin_notes)
        <p><strong>Administrator Notes:</strong></p>
        <div class="info-card">
            <p>{{ $equipmentRequest->admin_notes }}</p>
        </div>
        @endif

        <div class="alert alert-warning">
            <strong>⚠️ Important Reminders:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Return the equipment on or before the due date</li>
                <li>Handle the equipment with care</li>
                <li>Report any damages immediately</li>
                <li>Late returns may result in penalties</li>
            </ul>
        </div>

    @elseif($equipmentRequest->status === 'rejected')
        @if($equipmentRequest->admin_notes)
        <p><strong>Reason for Rejection:</strong></p>
        <div class="info-card">
            <p>{{ $equipmentRequest->admin_notes }}</p>
        </div>
        @endif

        <div class="alert alert-info">
            <strong>💡 What you can do:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Review the reason for rejection</li>
                <li>Modify your request if possible</li>
                <li>Submit a new request with corrections</li>
                <li>Contact the administrator for clarification</li>
            </ul>
        </div>

    @elseif($equipmentRequest->status === 'returned')
        <div class="alert alert-success">
            <strong>✅ Return Confirmed:</strong>
            <br>Thank you for returning the equipment in good condition. Your borrowing record has been updated.
        </div>

        @if($equipmentRequest->return_notes)
        <p><strong>Return Notes:</strong></p>
        <div class="info-card">
            <p>{{ $equipmentRequest->return_notes }}</p>
        </div>
        @endif
    @endif

    <div class="info-card">
        <div class="info-item">
            <span class="info-label">Updated:</span>
            <span class="info-value">{{ $equipmentRequest->updated_at->format('F j, Y \a\t g:i A') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Original Request:</span>
            <span class="info-value">{{ $equipmentRequest->created_at->format('F j, Y \a\t g:i A') }}</span>
        </div>
    </div>

    <p>If you have any questions about this update, please contact the equipment administrator.</p>
</div>
@endsection