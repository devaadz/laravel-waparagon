@extends('admin.layout')

@section('content')
<div class="bg-gradient-to-r from-green-50 to-blue-50 p-8 rounded-lg mb-8">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">WhatsApp Notification Logs</h1>
            <p class="text-gray-600 mt-2">Track WhatsApp messages sent to customers who filled out forms</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-right">
                <div class="text-4xl font-bold text-green-600 mb-2">
                    {{ $logs->total() }}
                </div>
                <p class="text-gray-600">Total messages</p>
            </div>
            <a href="{{ route('admin.whatsapp.export', request()->query()) }}"
               class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Excel
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Filters</h2>

    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Device</label>
            <select name="device_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <option value="">All Devices</option>
                @foreach($stores as $store)
                    <option value="{{ $store->whatsapp_device_id }}" {{ request('device_id') == $store->whatsapp_device_id ? 'selected' : '' }}>
                        {{ $store->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Recipient Phone</label>
            <input type="text" name="recipient" value="{{ request('recipient') }}"
                   placeholder="Enter phone number"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <option value="">All Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end">
            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                Filter
            </button>
        </div>
    </form>
</div>

<div class="overflow-x-auto bg-white rounded-lg shadow-md">
    <table class="w-full">
        <thead class="bg-gray-100 border-b border-gray-300">
            <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Recipient</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Device System</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Form</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Message</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Sent At</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $log->recipient }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        @if($log->whatsappDevice)
                            <div class="text-xs">
                                <div class="font-medium text-green-600">{{ $log->device_name ?? $log->whatsappDevice->name }}</div>
                                <div class="text-gray-500">{{ $log->device_system ?? $log->whatsappDevice->system }}</div>
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $log->form->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ Str::limit($log->message, 50) }}
                    </td>
                    <td class="px-6 py-4">
                        @if($log->status === 'sent')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Sent
                            </span>
                        @elseif($log->status === 'failed')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Failed
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $log->sent_at ? $log->sent_at->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('admin.notification-logs.show', $log) }}" class="text-green-600 hover:text-green-800 font-semibold">
                            View Details
                        </a>
                        @if($log->status === 'failed')
                            <form method="POST" action="{{ route('admin.notification-logs.retry', $log) }}" class="inline ml-4">
                                @csrf
                                <button type="submit" class="text-orange-600 hover:text-orange-800 font-semibold">
                                    Retry
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        No WhatsApp messages found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $logs->links() }}
</div>
@endsection