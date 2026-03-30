@extends('admin.layout')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.notification-logs.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
        Back to Notification Logs
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Notification Details</h1>

            <div class="space-y-6">
                <!-- Status -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Status</h3>
                    <div class="flex items-center">
                        @if($log->status === 'sent')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Sent Successfully
                            </span>
                        @elseif($log->status === 'failed')
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                Failed
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Type -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Type</h3>
                    <p class="text-lg text-gray-800">
                        @if($log->type === 'whatsapp')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                WhatsApp Message
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                Email Message
                            </span>
                        @endif
                    </p>
                </div>

                <!-- Recipient -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Recipient</h3>
                    <p class="text-lg text-gray-800 font-mono bg-gray-50 p-3 rounded">{{ $log->recipient }}</p>
                </div>

                <!-- Form -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Form</h3>
                    @if($log->form)
                        <a href="{{ route('admin.forms.show', $log->form) }}" class="text-lg text-blue-600 hover:text-blue-800 font-semibold">
                            {{ $log->form->name }}
                        </a>
                    @else
                        <p class="text-gray-600">Form deleted or not found</p>
                    @endif
                </div>

                <!-- Message -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Message</h3>
                    <div class="bg-gray-50 p-4 rounded border border-gray-200 whitespace-pre-wrap">
                        {{ Str::limit($log->message, 500) }}
                    </div>
                </div>

                <!-- Timestamps -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Created At</h3>
                        <p class="text-gray-800">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2">Sent At</h3>
                        <p class="text-gray-800">{{ $log->sent_at ? $log->sent_at->format('d/m/Y H:i:s') : '-' }}</p>
                    </div>
                </div>

                <!-- Error Message -->
                @if($log->status === 'failed' && $log->error_message)
                    <div class="border-t border-red-200 bg-red-50 p-4 rounded">
                        <h3 class="text-sm font-semibold text-red-700 uppercase tracking-wide mb-2">Error Details</h3>
                        <p class="text-red-800 font-mono text-sm">{{ $log->error_message }}</p>
                    </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="mt-8 pt-6 border-t border-gray-200 flex gap-4">
                @if($log->status === 'failed')
                    <form method="POST" action="{{ route('admin.notification-logs.retry', $log) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-6 rounded-lg transition-all duration-300">
                            Retry Sending
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.notification-logs.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-lg transition-all duration-300">
                    Back
                </a>
            </div>
        </div>
    </div>

    <!-- Related Information -->
    <div class="space-y-6">
        <!-- Response Info -->
        @if($log->response)
            <div class="bg-blue-50 rounded-lg shadow-md p-6 border border-blue-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Related Response</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide font-semibold">Submission ID</p>
                        <p class="text-gray-800 font-mono">{{ $log->response->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide font-semibold">Total Answers</p>
                        <p class="text-gray-800">{{ $log->response->answers->count() }} fields</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 uppercase tracking-wide font-semibold">Submitted At</p>
                        <p class="text-gray-800">{{ $log->response->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($log->response && $log->form)
                        <a href="{{ route('admin.responses.show', $log->response) }}" 
                           class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-semibold">
                            View Response
                        </a>
                    @endif
                </div>
            </div>
        @endif

        <!-- Quick Stats -->
        <div class="bg-indigo-50 rounded-lg shadow-md p-6 border border-indigo-200">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Stats</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total by Type</span>
                    <span class="font-bold text-gray-800">
                        {{ $log->form ? $log->form->notification_logs()->where('type', $log->type)->count() : 0 }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Success Rate</span>
                    <span class="font-bold text-green-600">
                        {{ $log->form ? round(($log->form->notification_logs()->where('status', 'sent')->count() / max($log->form->notification_logs()->count(), 1)) * 100) : 0 }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
