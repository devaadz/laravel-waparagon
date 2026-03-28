<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WhatsApp Messages - WA Paragon Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    @include('admin.layout')

    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold">WhatsApp Messages</h2>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form method="GET" class="mb-4 flex flex-wrap gap-3">
                <select name="device_id" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">All Devices</option>
                    @foreach($stores as $store)
                        @if($store->whatsapp_device_id)
                            <option value="{{ $store->whatsapp_device_id }}" {{ request('device_id') == $store->whatsapp_device_id ? 'selected' : '' }}>
                                {{ $store->name }} - {{ $store->whatsapp_device_id }}
                            </option>
                        @endif
                    @endforeach
                </select>

                <input type="text" name="from_phone" value="{{ request('from_phone') }}" placeholder="Filter by phone" class="border border-gray-300 rounded px-3 py-2">

                <select name="type" class="border border-gray-300 rounded px-3 py-2">
                    <option value="">All Types</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>

                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Filter</button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left">From</th>
                            <th class="px-4 py-2 text-left">Device</th>
                            <th class="px-4 py-2 text-left">Type</th>
                            <th class="px-4 py-2 text-left">Message</th>
                            <th class="px-4 py-2 text-left">Time</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $message)
                        <tr class="border-t">
                            <td class="px-4 py-2 text-sm">{{ $message->from_phone }}</td>
                            <td class="px-4 py-2 text-sm">{{ substr($message->device_id, 0, 15) }}...</td>
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
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.whatsapp.show', $message) }}" class="text-blue-500 hover:underline">View</a>
                                <form method="POST" action="{{ route('admin.whatsapp.destroy', $message) }}" class="inline ml-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Delete?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-2 text-center text-gray-500">No messages found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $messages->links() }}
        </div>
    </div>
</body>
</html>