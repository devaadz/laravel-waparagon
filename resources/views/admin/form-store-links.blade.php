@extends('admin.layout')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 py-12 px-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl p-10 mb-12 border border-white/60">
            <div class="text-center mb-12">
                <div class="inline-flex items-center gap-4 bg-gradient-to-r from-purple-500 to-pink-500 px-8 py-4 rounded-2xl shadow-2xl mb-8">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2h-5v2zM7 20h5v-2H7v2zM7 3v2h5V3H7z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.71 4.71l-1.42 1.42L17 7.29l1.29 1.29 1.42-1.42L20.71 4.71zM4 10h2v8H4zm10 0h2v8h-2zm-5 0h2v8H9z"></path>
                    </svg>
                    <div>
                        <h1 class="text-4xl font-black bg-gradient-to-r from-gray-900 to-slate-700 bg-clip-text text-transparent">🔗 Create Form-Store Link</h1>
                        <p class="text-xl text-gray-600 mt-2 font-light">Pick one form + one store → instant unique URL + QR code</p>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="mb-12 p-8 bg-gradient-to-r from-emerald-500 via-green-500 to-emerald-600 text-white rounded-3xl shadow-2xl flex items-center animate-pulse">
                    <svg class="w-16 h-16 mr-8 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-3xl font-bold mb-3">Link Created!</h3>
                        <p class="text-2xl opacity-90">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Form & Store Selector -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-end mb-16">
                <!-- Forms Dropdown -->
                <div>
                    <label class="block text-2xl font-bold text-gray-800 mb-6">Select Form</label>
                    <div class="relative">
                        <select name="form_id" id="form_select" required class="w-full px-8 py-6 text-xl font-bold border-2 border-gray-200 rounded-3xl focus:ring-8 focus:ring-blue-200 focus:border-blue-500 transition-all duration-500 shadow-xl hover:shadow-2xl appearance-none bg-white cursor-pointer">
                            <option value="">Choose a form...</option>
                            @foreach(App\Models\Form::where('status', 'active')->get() as $form)
                                <option value="{{ $form->id }}" data-slug="{{ $form->slug }}">{{ $form->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Stores Dropdown -->
                <div>
                    <label class="block text-2xl font-bold text-gray-800 mb-6">🏪 Select Store</label>
                    <div class="relative">
                        <select name="store_id" id="store_select" required class="w-full px-8 py-6 text-xl font-bold border-2 border-gray-200 rounded-3xl focus:ring-8 focus:ring-emerald-200 focus:border-emerald-500 transition-all duration-500 shadow-xl hover:shadow-2xl appearance-none bg-white cursor-pointer">
                            <option value="">Choose a store...</option>
                            @foreach(App\Models\Store::all() as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Generate Button -->
            <div class="text-center mb-16">
                <button type="submit" id="generate-link" disabled class="inline-flex items-center gap-4 px-20 py-8 bg-gradient-to-r from-purple-600 via-pink-600 to-indigo-600 hover:from-purple-700 hover:via-pink-700 hover:to-indigo-700 text-white font-black text-3xl rounded-3xl shadow-2xl hover:shadow-3xl transform hover:-translate-y-2 transition-all duration-500 group">
                    <svg class="w-12 h-12 group-hover:rotate-12 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>🔥 Generate Link!</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 -skew-x-12 transform group-hover:translate-x-full transition-transform duration-1000"></div>
                </button>
            </div>

            <!-- Preview Section -->
            <div id="preview-section" class="hidden bg-white rounded-3xl shadow-2xl p-12 border-2 border-dashed border-gray-200 mb-12">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-black text-gray-900 mb-4">✨ Link Preview</h2>
                    <p class="text-2xl text-gray-600 mb-8">Your new form URL is ready!</p>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
                    <!-- URL Preview -->
                    <div class="lg:col-span-2">
                        <div class="bg-gradient-to-r from-slate-900 to-gray-800 text-white p-8 rounded-3xl shadow-2xl">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center shadow-xl">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3 4h18v2H3V4zM3 10h18v2H3v-2zM3 16h18v2H3v-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-3xl font-black">Form URL</p>
                                    <p class="opacity-75 font-light text-lg">Share with customers</p>
                                </div>
                            </div>
                            <div class="relative">
                                <input type="text" id="preview-url" readonly class="w-full bg-black/30 backdrop-blur-md px-8 py-6 text-2xl font-mono rounded-2xl border-2 border-white/20 text-white focus:outline-none focus:border-white/50 transition-all duration-300" value="">
                                <button type="button" id="copy-url" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white px-6 py-3 rounded-xl font-bold text-lg transition-all duration-300">Copy</button>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div>
                        <div class="bg-gradient-to-b from-slate-900 to-gray-800 text-white p-8 rounded-3xl shadow-2xl text-center">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-xl">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-3xl font-black">QR Code</p>
                                    <p class="opacity-75 font-light text-lg">Print & share</p>
                                </div>
                            </div>
                            <div id="qr-preview" class="mx-auto p-6 bg-black/20 backdrop-blur-md rounded-3xl border-4 border-white/30 shadow-2xl max-w-xs">
                                Loading QR...
                            </div>
                            <div class="flex gap-3 mt-8 justify-center">
                                <a href="#" id="qr-download" download class="px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-bold rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 flex items-center gap-3">
                                    Download QR
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mt-16 p-8 bg-white/50 backdrop-blur-md rounded-3xl shadow-xl border border-white/50">
                    <button type="submit" id="save-link" class="bg-gradient-to-r from-emerald-600 via-green-600 to-emerald-700 hover:from-emerald-700 hover:via-green-700 hover:to-emerald-800 text-white font-black text-2xl px-16 py-8 rounded-3xl shadow-2xl hover:shadow-3xl transform hover:-translate-y-2 transition-all duration-500 flex items-center gap-4 mx-auto">
                        Create This Link!
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Existing Links -->
            <div class="mt-24">
                <div class="flex items-center mb-12">
                    <div class="w-20 h-20 bg-gradient-to-r from-green-500 to-emerald-500 rounded-3xl flex items-center justify-center shadow-2xl mr-8">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2h-5v2zM7 20h5v-2H7v2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-4xl font-black text-gray-900 mb-3">Existing Links</h2>
                        <p class="text-xl text-gray-600">All current form-store connections</p>
                    </div>
                </div>
                
                @if($linkedStoreIds->count())
                <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gradient-to-r from-green-50 to-emerald-50">
                                    <th class="px-12 py-8 text-left font-black text-xl text-gray-800">Store Name</th>
                                    <th class="px-12 py-8 text-left font-black text-xl text-gray-800 w-64">Unique Slug</th>
                                    <th class="px-12 py-8 text-left font-black text-xl text-gray-800">Live URL</th>
                                    <th class="px-12 py-8 text-left font-black text-xl text-gray-800">QR Status</th>
                                    <th class="px-12 py-8 text-left font-black text-xl text-gray-800">Created</th>
                                    <th class="px-12 py-8 text-left font-black text-xl text-gray-800">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($form->formStores as $formStore)
                                <tr class="hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 transition-all duration-300 border-b border-gray-100">
                                    <td class="px-12 py-10 font-bold text-2xl text-gray-900">{{ $formStore->store->name }}</td>
                                    <td class="px-12 py-10">
                                        <code class="bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800 font-bold py-4 px-8 rounded-2xl text-xl block shadow-inner">
                                            {{ $formStore->custom_url_slug }}
                                        </code>
                                    </td>
                                    <td class="px-12 py-10">
                                        <a href="/form/{{ $formStore->custom_url_slug }}" target="_blank" class="inline-flex items-center gap-3 bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800 font-bold py-4 px-8 rounded-2xl hover:shadow-lg hover:from-emerald-200 hover:to-green-200 transition-all duration-300">
                                            Open Form
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </td>
                                    <td class="px-12 py-10">
                                        <div class="flex gap-4 items-center">
                                            <img src="{{ route('public.form.store.qr', $formStore->custom_url_slug) }}" alt="QR" class="w-20 h-20 rounded-2xl shadow-lg border-4 border-emerald-100">
                                            <span class="px-4 py-2 bg-emerald-100 text-emerald-800 font-bold rounded-full text-sm">Ready</span>
                                        </div>
                                    </td>
                                    <td class="px-12 py-10 text-xl font-mono text-gray-500">
                                        {{ $formStore->created_at->format('d MMM • HH:mm') }}
                                    </td>
                                    <td class="px-12 py-10">
                                        <form method="POST" action="{{ route('admin.forms.stores.unlink', [$form, $formStore->store_id]) }}" class="inline" onsubmit="return confirm('Unlink {{ $formStore->store->name }} from {{ $form->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-gradient-to-r from-red-500 to-rose-500 hover:from-red-600 hover:to-rose-600 text-white font-bold py-4 px-8 rounded-2xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 flex items-center gap-3">
                                                🗑️ Unlink
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                <div class="text-center py-32 bg-white rounded-3xl shadow-xl border-2 border-dashed border-emerald-200">
                    <svg class="w-32 h-32 mx-auto mb-12 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100-4m0 4v2m0-6V4m6 6v10m0-10v10"></path>
                    </svg>
                    <h3 class="text-4xl font-black text-gray-500 mb-6">No links yet</h3>
                    <p class="text-2xl text-gray-400 mb-12 max-w-2xl mx-auto leading-relaxed">Use the selector above to create your first form-store connection</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const formSelect = document.getElementById('form_select');
    const storeSelect = document.getElementById('store_select');
    const generateBtn = document.getElementById('generate-link');

    // Enable button when both selected
    function checkSelection() {
        generateBtn.disabled = !(formSelect.value && storeSelect.value);
        generateBtn.classList.toggle('opacity-50', generateBtn.disabled);
        generateBtn.classList.toggle('cursor-not-allowed', generateBtn.disabled);
    }

    formSelect.addEventListener('change', checkSelection);
    storeSelect.addEventListener('change', checkSelection);

    // Copy URL
    document.getElementById('copy-url').addEventListener('click', function() {
        navigator.clipboard.writeText(document.getElementById('preview-url').value);
        this.textContent = 'Copied!';
        setTimeout(() => this.textContent = 'Copy', 2000);
    });
});
</script>
@endsection

