@extends('admin.layout')

@section('title', 'Create WhatsApp Device')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Create WhatsApp Device</h1>
            <a href="{{ route('admin.devices.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Devices
            </a>
        </div>

        <form action="{{ route('admin.devices.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Device Name
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                       placeholder="e.g., Toko Utama" required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="device_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Device ID
                </label>
                <input type="text" name="device_id" id="device_id" value="{{ old('device_id') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('device_id') border-red-500 @enderror"
                       placeholder="e.g., toko_utama" required>
                <p class="mt-1 text-sm text-gray-500">Unique identifier for the device (no spaces, use underscores)</p>
                @error('device_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('admin.devices.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Create Device
                </button>
            </div>
        </form>
    </div>
</div>
@endsection