<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Device Details - WA Paragon Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    @include('admin.layout')

    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold">Device Details</h2>
            <a href="{{ route('admin.devices.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Back</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Device Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Device Information</h3>

                <div class="space-y-3">
                    <div>
                        <label class="text-gray-700 font-semibold">Device ID</label>
                        <p class="text-gray-900 break-all font-mono text-sm">{{ $device->device_id }}</p>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold">Phone Number</label>
                        <p class="text-gray-900">{{ $device->phone_number ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold">Status</label>
                        <p class="text-gray-900">
                            <span class="px-2 py-1 rounded text-sm
                                @if($device->status === 'connected') bg-green-100 text-green-800
                                @elseif($device->status === 'connecting') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($device->status) }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold">Login Status</label>
                        <p class="text-gray-900">
                            @if($device->is_logged_in)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">Logged In</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm">Not Logged In</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold">Last Login</label>
                        <p class="text-gray-900">{{ $device->last_login_at?->format('M d, Y H:i:s') ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold">Last Logout</label>
                        <p class="text-gray-900">{{ $device->last_logout_at?->format('M d, Y H:i:s') ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold">Created</label>
                        <p class="text-gray-900">{{ $device->created_at->format('M d, Y H:i:s') }}</p>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold">Updated</label>
                        <p class="text-gray-900">{{ $device->updated_at->format('M d, Y H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <!-- Store Link -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Store Link</h3>

                @if($device->store)
                <div class="mb-4">
                    <p class="text-gray-700 mb-2">Linked to store:</p>
                    <div class="bg-blue-50 p-3 rounded">
                        <p class="font-semibold">{{ $device->store->name }}</p>
                        <p class="text-sm text-gray-600">{{ $device->store->address }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.devices.unlink-store', $device) }}">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Unlink from store?')">
                        Unlink from Store
                    </button>
                </form>
                @else
                <form method="POST" action="{{ route('admin.devices.link-store', $device) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="store_id" class="block text-gray-700 text-sm font-bold mb-2">Select Store</label>
                        <select name="store_id" id="store_id" class="border border-gray-300 rounded w-full py-2 px-3 text-gray-700" required>
                            <option value="">Choose a store...</option>
                            @foreach(\App\Models\Store::all() as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Link to Store
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Recent Messages -->
        <div class="mt-6 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4">Recent Messages ({{ $device->messages->count() }})</h3>

            @if($device->messages->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left">From</th>
                            <th class="px-4 py-2 text-left">Type</th>
                            <th class="px-4 py-2 text-left">Message</th>
                            <th class="px-4 py-2 text-left">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($device->messages as $message)
                        <tr class="border-t">
                            <td class="px-4 py-2 text-sm">{{ $message->from_phone }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">{{ ucfirst($message->type) }}</span>
                            </td>
                            <td class="px-4 py-2 text-sm">
                                @if($message->text)
                                    {{ Str::limit($message->text, 50) }}
                                @else
                                    <em class="text-gray-500">{{ $message->type }} message</em>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm">{{ $message->message_timestamp?->format('M d, H:i') ?? $message->created_at->format('M d, H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.whatsapp.index', ['device_id' => $device->device_id]) }}" class="text-blue-500 hover:underline">
                    View all messages for this device →
                </a>
            </div>
            @else
            <p class="text-gray-500">No messages yet.</p>
            @endif
        </div>

        <!-- Device Info JSON -->
        @if($device->device_info)
        <div class="mt-6 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold mb-4">Device Info (JSON)</h3>
            <pre class="bg-gray-50 p-3 rounded border border-gray-200 overflow-auto text-xs">{{ json_encode($device->device_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
        @endif

        <!-- Delete Device -->
        <div class="mt-6">
            <form method="POST" action="{{ route('admin.devices.destroy', $device) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Delete this device? This will also delete all associated messages.')">
                    Delete Device
                </button>
            </form>
        </div>
    </div>
</body>
</html>