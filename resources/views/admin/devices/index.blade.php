<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WhatsApp Devices - WA Paragon Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    @include('admin.layout')

    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold">WhatsApp Devices</h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.devices.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add Device
                </a>
                <form method="POST" action="{{ route('admin.devices.sync') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Sync from GoWA
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="GET" class="mb-4 flex flex-wrap gap-3">
                <select name="status" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">All Status</option>
                    <option value="connected" {{ request('status') == 'connected' ? 'selected' : '' }}>Connected</option>
                    <option value="disconnected" {{ request('status') == 'disconnected' ? 'selected' : '' }}>Disconnected</option>
                    <option value="connecting" {{ request('status') == 'connecting' ? 'selected' : '' }}>Connecting</option>
                </select>

                <select name="is_logged_in" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">All Login Status</option>
                    <option value="1" {{ request('is_logged_in') == '1' ? 'selected' : '' }}>Logged In</option>
                    <option value="0" {{ request('is_logged_in') == '0' ? 'selected' : '' }}>Not Logged In</option>
                </select>

                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Filter</button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Device ID</th>
                            <th class="px-4 py-2 text-left">Phone</th>
                            <th class="px-4 py-2 text-left">Status</th>
                            <th class="px-4 py-2 text-left">Login Status</th>
                            <th class="px-4 py-2 text-left">Store</th>
                            <th class="px-4 py-2 text-left">Last Login</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices as $device)
                        <tr class="border-t">
                            <td class="px-4 py-2 font-medium">{{ $device->name ?? 'Unnamed' }}</td>
                            <td class="px-4 py-2 text-sm font-mono">{{ substr($device->device_id, 0, 20) }}...</td>
                            <td class="px-4 py-2">{{ $device->phone_number ?? '-' }}</td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 rounded text-xs
                                    @if($device->status === 'connected') bg-green-100 text-green-800
                                    @elseif($device->status === 'connecting') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($device->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2">
                                @if($device->is_logged_in)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Logged In</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">Not Logged In</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @if($device->store)
                                    <a href="{{ route('admin.stores.show', $device->store) }}" class="text-blue-500 hover:underline">
                                        {{ $device->store->name }}
                                    </a>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-sm">
                                {{ $device->last_login_at?->format('M d, H:i') ?? '-' }}
                            </td>
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.devices.login', $device) }}" class="text-green-500 hover:underline mr-2">Login</a>
                                <a href="{{ route('admin.devices.show', $device) }}" class="text-blue-500 hover:underline mr-2">View</a>
                                <form method="POST" action="{{ route('admin.devices.destroy', $device) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Delete?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-2 text-center text-gray-500">No devices found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $devices->links() }}
        </div>
    </div>
</body>
</html>