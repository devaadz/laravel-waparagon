@extends('public.layout')

@section('content')
<div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6" role="alert">
    <p class="font-bold text-blue-800">Admin Preview Mode</p>
    <p class="text-blue-700 text-sm">You are viewing this form as a preview. This is for testing purposes.</p>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-4">{{ $form->name }}</h1>
    @if($form->description)
        <p class="text-gray-600 mb-6">{{ $form->description }}</p>
    @endif

    <form method="POST" action="{{ route('public.form.submit', $form->id) }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email *</label>
            <input type="email" name="email" id="email" value="admin@test.com" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="store_id" class="block text-gray-700 text-sm font-bold mb-2">Select Store *</label>
            <select name="store_id" id="store_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                <option value="">Choose a store</option>
                @foreach($stores as $store)
                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                @endforeach
            </select>
            @error('store_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Next
            </button>
        </div>
    </form>
</div>

<div class="mt-6 bg-gray-50 p-4 rounded">
    <h3 class="font-bold text-gray-700 mb-2">Preview Information:</h3>
    <ul class="text-sm text-gray-600 space-y-1">
        <li><strong>Form ID:</strong> {{ $form->id }}</li>
        <li><strong>Status:</strong> {{ $form->status }}</li>
        <li><strong>Fields:</strong> {{ $form->fields->count() }}</li>
        <li><strong>Email Notification:</strong> {{ $form->enable_email_notification ? 'Enabled' : 'Disabled' }}</li>
        <li><strong>WhatsApp Notification:</strong> {{ $form->enable_whatsapp_notification ? 'Enabled' : 'Disabled' }}</li>
    </ul>
</div>
@endsection
