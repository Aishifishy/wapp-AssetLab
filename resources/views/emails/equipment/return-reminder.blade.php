@extends('emails.base')

@section('title', $daysOverdue > 0 ? 'Equipment Overdue - Immediate Return Required' : 'Equipment Return Reminder')

@section('content')
<div class="header">
    <h1>
        @if($daysOverdue > 0)
            Equipment Overdue
        @else
            Return Reminder
        @endif
    </h1>
    <p class="subtitle">
        @if($daysOverdue > 0)
            ⚠️ {{ $daysOverdue }} {{ $daysOverdue === 1 ? 'day' : 'days' }} overdue
        @else
            📅 Due today or soon
        @endif
    </p>
</div>

<div class="content">
    <p><strong>Hello {{ $user->name }}!</strong></p>
    
    <p>
        @if($daysOverdue > 0)
            <strong>URGENT:</strong> The equipment you borrowed is now <strong>{{ $daysOverdue }} {{ $daysOverdue === 1 ? 'day' : 'days' }} overdue</strong>. 
            Please return it immediately to avoid further penalties.
        @else
            This is a friendly reminder that your borrowed equipment is due for return today or very soon.
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
            <span class="info-label">Quantity Borrowed:</span>
            <span class="info-value">{{ $equipmentRequest->quantity }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Borrowed Date:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($equipmentRequest->borrow_date)->format('F j, Y') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Expected Return Date:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($equipmentRequest->expected_return_date)->format('F j, Y') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Status:</span>
            <span class="info-value">
                @if($daysOverdue > 0)
                    <span class="status-badge cancelled">⚠️ {{ $daysOverdue }} {{ $daysOverdue === 1 ? 'Day' : 'Days' }} Overdue</span>
                @else
                    <span class="status-badge pending">📅 Due for Return</span>
                @endif
            </span>
        </div>
    </div>

    @if($daysOverdue > 0)
        <div class="alert alert-warning">
            <strong>🚨 OVERDUE NOTICE:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Your equipment is <strong>{{ $daysOverdue }} {{ $daysOverdue === 1 ? 'day' : 'days' }} overdue</strong></li>
                <li>Immediate return is required to avoid further penalties</li>
                <li>Late fees may apply according to university policy</li>
                <li>Repeated violations may affect future borrowing privileges</li>
            </ul>
        </div>

        <div class="alert alert-info">
            <strong>📞 Contact Information:</strong>
            <br>If you're unable to return the equipment immediately due to exceptional circumstances, 
            please contact the equipment administrator immediately to discuss your situation.
        </div>
    @else
        <div class="alert alert-info">
            <strong>📋 Return Instructions:</strong>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Return the equipment to its designated location</li>
                <li>Ensure all components are included and in good condition</li>
                <li>Inform the staff of any issues or damages</li>
                <li>Obtain a return confirmation receipt</li>
            </ul>
        </div>
    @endif

    <h3>Equipment Details</h3>
    <div class="info-card">
        <div class="info-item">
            <span class="info-label">Category:</span>
            <span class="info-value">{{ $equipment->category->name ?? 'N/A' }}</span>
        </div>
        @if($equipment->location)
        <div class="info-item">
            <span class="info-label">Return Location:</span>
            <span class="info-value">{{ $equipment->location }}</span>
        </div>
        @endif
        @if($equipment->serial_number)
        <div class="info-item">
            <span class="info-label">Serial Number:</span>
            <span class="info-value">{{ $equipment->serial_number }}</span>
        </div>
        @endif
    </div>

    @if($equipmentRequest->purpose)
    <p><strong>Original Purpose:</strong> {{ $equipmentRequest->purpose }}</p>
    @endif

    <div class="alert alert-warning">
        <strong>🔍 Return Checklist:</strong>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li>All equipment components are present</li>
            <li>Equipment is clean and in good condition</li>
            <li>Any damages are reported immediately</li>
            <li>Return is completed during office hours</li>
            <li>Return receipt is obtained as confirmation</li>
        </ul>
    </div>

    <p><strong>Original Request Date:</strong> {{ $equipmentRequest->created_at->format('F j, Y \a\t g:i A') }}</p>
    
    <p>Thank you for your cooperation in returning the equipment promptly. This helps ensure availability for other students.</p>
</div>
@endsection