@extends('admin.layout')


@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold">Form-Store Connections</h1>
            <p class="text-gray-600 mt-2">List all forms and stores that are connected ({{ $formStores->count() }})</p>
        </div>
<div class="space-x-2">
            <a href="{{ route('admin.forms.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Forms
            </a>
            <a href="{{ route('admin.stores.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Stores
            </a>
            <a href="{{ route('admin.form-stores.create') }}" class="bg-emerald-500 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-xl shadow-lg">
                ➕ Create Link
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-6 py-3 text-left">Link Name</th>
                    <th class="border px-6 py-3 text-left">Form</th>
                    <th class="border px-6 py-3 text-left">Store</th>
                    <th class="border px-6 py-3 text-left">Slug</th>
                    <th class="border px-6 py-3 text-left">Form URL</th>
                    <th class="border px-6 py-3 text-left">QR Code</th>
                    <th class="border px-6 py-3 text-left">Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($formStores as $formStore)
                <tr class="hover:bg-gray-50">
                    <td class="border px-6 py-4 font-semibold">{{ $formStore->name }}</td>
                    <td class="border px-6 py-4">
                        <a href="{{ route('admin.forms.show', $formStore->form) }}" class="text-blue-500 hover:underline">
                            {{ $formStore->form->name }}
                        </a>
                    </td>
                    <td class="border px-6 py-4">
                        <a href="{{ route('admin.stores.show', $formStore->store) }}" class="text-green-500 hover:underline">
                            {{ $formStore->store->name }}
                        </a>
                    </td>
                    <td class="border px-6 py-4 font-mono text-sm bg-gray-50">{{ $formStore->custom_url_slug }}</td>
                    <td class="border px-6 py-4">
<a href="/form/{{ $formStore->custom_url_slug }}" target="_blank" class="text-blue-500 hover:underline">
                            /form/{{ $formStore->custom_url_slug }}
                        </a>
                    </td>
                    <td class="border px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('public.form.store.qr', $formStore->custom_url_slug) }}" target="_blank" class="text-blue-500 hover:underline">
                                View
                            </a>
                            <a href="{{ route('public.form.store.qr.download', $formStore->custom_url_slug) }}" download="qr-{{ $formStore->custom_url_slug }}.png" class="text-green-500 hover:underline">
                                Download
                            </a>
                        </div>
                    </td>
                    <td class="border px-6 py-4 text-sm text-gray-600">{{ $formStore->created_at->format('Y-m-d H:i') }}</td>
                    <td class="border px-6 py-4">
                        <a href="{{ route('admin.forms.stores', $formStore->form_id) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                            Manage Links
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="border px-6 py-8 text-center text-gray-500">No Form-Store connections found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

