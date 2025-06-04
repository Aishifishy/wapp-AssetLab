@extends('layouts.admin')

@section('title', 'Create Equipment Request')
@section('header', 'Create Equipment Request')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg">
        <div class="p-6">
            <form action="{{ route('admin.equipment.store-request') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="user_id">User</label>
                    <select name="user_id" id="user_id" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->department }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="equipment_id">Equipment</label>
                    <select name="equipment_id" id="equipment_id" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select Equipment</option>
                        @foreach($equipment as $item)
                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->category }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="purpose">Purpose</label>
                    <textarea name="purpose" id="purpose" required rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="Please describe why this equipment is needed..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="requested_from">From Date</label>
                    <input type="datetime-local" name="requested_from" id="requested_from" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="requested_until">Until Date</label>
                    <input type="datetime-local" name="requested_until" id="requested_until" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="flex justify-end mt-6">
                    <a href="{{ route('admin.equipment.borrow-requests') }}" class="btn-secondary mr-2">Cancel</a>
                    <button type="submit" class="btn-primary">Create Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<!-- Equipment request date validation is now handled by equipment-manager.js module -->
@endpush
@endpush 