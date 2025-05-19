@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">Edit Term</h1>
        <a href="{{ route('admin.academic.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Calendar
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-calendar-alt me-1"></i>
            Term Details for {{ $academicYear->name }}
        </div>
        <div class="card-body">
            <form action="{{ route('admin.academic.terms.update', [$academicYear, $term]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="name" class="form-label">Term Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $term->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                id="start_date" name="start_date" 
                                value="{{ old('start_date', $term->start_date->format('Y-m-d')) }}" 
                                min="{{ $academicYear->start_date->format('Y-m-d') }}"
                                max="{{ $academicYear->end_date->format('Y-m-d') }}"
                                required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                id="end_date" name="end_date" 
                                value="{{ old('end_date', $term->end_date->format('Y-m-d')) }}"
                                min="{{ $academicYear->start_date->format('Y-m-d') }}"
                                max="{{ $academicYear->end_date->format('Y-m-d') }}"
                                required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Note:</strong> Term dates must be within the academic year period
                    ({{ $academicYear->start_date->format('M d, Y') }} - {{ $academicYear->end_date->format('M d, Y') }}).
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Term
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Validate end date is after start date
        $('#end_date').change(function() {
            const startDate = new Date($('#start_date').val());
            const endDate = new Date($(this).val());
            
            if (endDate <= startDate) {
                alert('End date must be after start date');
                $(this).val('');
            }
        });

        // Validate start date is before end date
        $('#start_date').change(function() {
            const startDate = new Date($(this).val());
            const endDate = new Date($('#end_date').val());
            
            if (endDate && startDate >= endDate) {
                alert('Start date must be before end date');
                $(this).val('');
            }
        });
    });
</script>
@endpush
@endsection 