@extends('admin.layout')

@section('title', 'Login Device - ' . $device->name)

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Login Device: {{ $device->name }}</h1>
            <a href="{{ route('admin.devices.show', $device) }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Device
            </a>
        </div>

        <div class="text-center">
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Scan QR Code</h2>
                <p class="text-gray-600 mb-4">
                    Open WhatsApp on your phone and scan this QR code to link the device.
                </p>
            </div>

            @if(isset($qrData['qr']) && $qrData['qr'])
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <img src="data:image/png;base64,{{ $qrData['qr'] }}" alt="QR Code" class="mx-auto">
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex">
                        <div class="ml-3">
                            <p class="text-sm text-yellow-800">
                                QR code not available. The device might already be logged in or there was an error.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="text-sm text-gray-500">
                <p>Device ID: <code class="bg-gray-100 px-2 py-1 rounded">{{ $device->device_id }}</code></p>
                <p>Status: <span class="capitalize">{{ $device->status }}</span></p>
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.devices.show', $device) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    Continue to Device Details
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Auto refresh QR code every 30 seconds
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endsection