@extends('layouts.admin')

@section('content')
<x-admin.form-wrapper 
    title="Add New Laboratory"
    :back-route="route('admin.laboratory.index')"
    back-text="Back to List"
    :form-action="route('admin.laboratory.store')"
    submit-text="Create Laboratory">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-6">
            <x-admin.form-field
                name="name"
                label="Laboratory Name"
                :required="true" />

            <x-admin.form-field
                name="room_number"
                label="Room Number"
                :required="true" />

            <x-admin.form-field
                name="building"
                label="Building"
                :required="true" />
        </div>
        
        <div class="space-y-6">
            <x-admin.form-field
                name="capacity"
                label="Seating Capacity"
                type="number"
                :required="true"
                min="1" />

            <x-admin.form-field
                name="number_of_computers"
                label="Number of Computers"
                type="number"
                :required="true"
                min="1" />

            <x-admin.form-field
                name="status"
                label="Status"
                type="select"
                :required="true"
                :options="[
                    'available' => 'Available',
                    'in_use' => 'In Use',
                    'under_maintenance' => 'Under Maintenance',
                    'reserved' => 'Reserved'
                ]" />
        </div>
    </div>

</x-admin.form-wrapper>
@endsection