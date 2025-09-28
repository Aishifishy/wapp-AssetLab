@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Edit Laboratory</h1>
        <a href="{{ route('admin.laboratory.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <i class="fas fa-edit text-gray-500 mr-2"></i>
                <h3 class="text-lg font-medium text-gray-900">Laboratory Details</h3>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('admin.laboratory.update', $laboratory) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Laboratory Name</label>
                            <input type="text" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $laboratory->name) }}"                                   required>
                            <x-form-error field="name" />
                        </div>

                        <div>
                            <label for="room_number" class="block text-sm font-medium text-gray-700">Room Number</label>
                            <input type="text" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('room_number') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                   id="room_number" 
                                   name="room_number" 
                                   value="{{ old('room_number', $laboratory->room_number) }}"                                   required>
                            <x-form-error field="room_number" />
                        </div>

                        <div>
                            <label for="building" class="block text-sm font-medium text-gray-700">Building</label>
                            <input type="text" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('building') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                   id="building" 
                                   name="building" 
                                   value="{{ old('building', $laboratory->building) }}" 
                                   required>                            <x-form-error field="building" />
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="capacity" class="block text-sm font-medium text-gray-700">Seating Capacity</label>
                            <input type="number" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('capacity') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                   id="capacity" 
                                   name="capacity" 
                                   value="{{ old('capacity', $laboratory->capacity) }}" 
                                   required 
                                   min="1">                            <x-form-error field="capacity" />
                        </div>

                        <div>
                            <label for="number_of_computers" class="block text-sm font-medium text-gray-700">Number of Computers</label>
                            <input type="number" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('number_of_computers') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                   id="number_of_computers" 
                                   name="number_of_computers" 
                                   value="{{ old('number_of_computers', $laboratory->number_of_computers) }}" 
                                   required 
                                   min="1">                            <x-form-error field="number_of_computers" />
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select class="mt-1 block w-full px-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('status') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="available" {{ old('status', $laboratory->status) === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="in_use" {{ old('status', $laboratory->status) === 'in_use' ? 'selected' : '' }}>In Use</option>
                                <option value="under_maintenance" {{ old('status', $laboratory->status) === 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                <option value="reserved" {{ old('status', $laboratory->status) === 'reserved' ? 'selected' : '' }}>Reserved</option>
                            </select>                            <x-form-error field="status" />
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="require_faci_req_form" 
                                   name="require_faci_req_form" 
                                   value="1"
                                   {{ old('require_faci_req_form', $laboratory->require_faci_req_form) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="require_faci_req_form" class="ml-2 block text-sm text-gray-900">
                                Require ITSO Facility Reservation Request Form
                            </label>
                            <x-form-error field="require_faci_req_form" />
                        </div>

                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i> Update Laboratory
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 