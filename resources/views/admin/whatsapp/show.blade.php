<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Message Details - WA Paragon Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    @include('admin.layout')

    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold">Message Details</h2>
            <a href="{{ route('admin.whatsapp.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Back</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Message Metadata</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-gray-700 font-semibold">Message ID</label>
                        <p class="text-gray-900 break-all">{{ $message->message_id }}</p>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold">Device</label>
                        <p class="text-gray-900 break-all">{{ $message->device_id }}</p>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold">From</label>
                        <p class="text-gray-900">{{ $message->from_phone }}</p>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold">To</label>
                        <p class="text-gray-900">{{ $message->to_phone ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold">Type</label>
                        <p class="text-gray-900">{{ ucfirst($message->type) }}</p>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold">Direction</label>
                        <p class="text-gray-900">
                            @if($message->is_from_me)
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">Outgoing</span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-sm">Incoming</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold">Timestamp</label>
                        <p class="text-gray-900">{{ $message->message_timestamp?->format('M d, Y H:i:s') ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="text-gray-700 font-semibold">Received</label>
                        <p class="text-gray-900">{{ $message->created_at->format('M d, Y H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold mb-4">Content</h3>

                @if($message->text)
                <div class="mb-4">
                    <label class="text-gray-700 font-semibold block mb-2">Message Text</label>
                    <div class="bg-gray-50 p-3 rounded border border-gray-200 break-words">{{ $message->text }}</div>
                </div>
                @endif

                @if($message->media_url)
                <div class="mb-4">
                    <label class="text-gray-700 font-semibold block mb-2">Media URL</label>
                    <a href="{{ $message->media_url }}" target="_blank" class="text-blue-500 hover:underline break-all">{{ $message->media_url }}</a>
                </div>
                @endif

                @if($message->payload)
                <div>
                    <label class="text-gray-700 font-semibold block mb-2">Full Payload</label>
                    <pre class="bg-gray-50 p-3 rounded border border-gray-200 overflow-auto text-xs">{{ json_encode($message->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                </div>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <form method="POST" action="{{ route('admin.whatsapp.destroy', $message) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Delete this message?')">Delete Message</button>
            </form>
        </div>
    </div>
</body>
</html>