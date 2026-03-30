@extends('admin.layout')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 to-blue-50 py-16">
    <div class="max-w-4xl mx-auto px-6">
        <!-- Hero Header -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl shadow-2xl p-12 text-center mb-16 border border-white/50">
            <div class="inline-flex items-center gap-6 bg-gradient-to-r from-emerald-500 to-teal-500 px-12 py-6 rounded-3xl shadow-2xl mb-12">
                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2h-5v2zM7 20h5v-2H7v2zM7 3v2h5V3H7z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.71 4.71l-1.42 1.42L17 7.29l1.29 1.29 1.42-1.42L20.71 4.71zM4 10h2v8H4zm10 0h2v8h-2zm-5 0h2v8H9z"></path>
                </svg>
                <div>
                    <h1 class="text-5xl font-black bg-gradient-to-r from-gray-900 via-slate-800 to-black bg-clip-text text-transparent">Create Form-Store Link</h1>
                    <p class="text-2xl mt-4 text-gray-700 font-light">Connect one form to one store → unique URL + QR code instantly</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.form-stores.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Dual Selectors -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
                <!-- Forms -->
                <div class="group">
                    <label class="block text-3xl font-black text-gray-900 mb-8">
                        📋 Choose Form
                    </label>
                    <div class="relative">
                        <select name="form_id" id="form_select" required 
                                class="w-full px-12 py-12 text-2xl font-bold border-4 border-dashed border-gray-300 rounded-4xl bg-white/50 backdrop-blur-xl shadow-2xl hover:border-blue-400 focus:ring-8 focus:ring-blue-200 focus:border-blue-500 transition-all duration-500 appearance-none cursor-pointer">
                            <option value="">Select active form...</option>
                            @foreach(App\Models\Form::where('status', 'active')->orderBy('name')->get() as $form)
                            <option value="{{ $form->id }}">
                                {{ $form->name }} {{ $form->fields->count() }} fields
                            </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-6 text-gray-500">
                            <svg class="w-10 h-10 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('form_id')
                        <p class="mt-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded-2xl font-semibold">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Stores -->
                <div class="group">
                    <label class="block text-3xl font-black text-gray-900 mb-8">
                         Choose Store
                    </label>
                    <div class="relative">
                        <select name="store_id" id="store_select" required 
                                class="w-full px-12 py-12 text-2xl font-bold border-4 border-dashed border-gray-300 rounded-4xl bg-white/50 backdrop-blur-xl shadow-2xl hover:border-emerald-400 focus:ring-8 focus:ring-emerald-200 focus:border-emerald-500 transition-all duration-500 appearance-none cursor-pointer">
                            <option value="">Select store...</option>
                            @foreach(App\Models\Store::orderBy('name')->get() as $store)
                            <option value="{{ $store->id }}">
                                {{ $store->name }} {{ $store->phone }}
                            </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-6 text-gray-500">
                            <svg class="w-10 h-10 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('store_id')
                        <p class="mt-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded-2xl font-semibold">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Generate Button -->
            <div class="text-center mb-20">
                <button type="submit" id="generate-btn" disabled 
                        class="inline-flex items-center gap-6 px-24 py-12 bg-gradient-to-r from-emerald-600 via-green-600 to-emerald-700 hover:from-emerald-700 hover:via-green-700 hover:to-emerald-800 disabled:from-gray-400 disabled:to-gray-500 text-white font-black text-4xl rounded-4xl shadow-3xl hover:shadow-4xl transform hover:-translate-y-4 disabled:cursor-not-allowed disabled:hover:translate-y-0 transition-all duration-700 group relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/20 blur opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                    <span class="relative z-10 flex items-center gap-4">
                        <svg class="w-16 h-16 group-hover:rotate-12 transition-transform duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Generate Unique Link!
                    </span>
                </button>
            </div>

            <!-- Live Preview (Hidden initially) -->
            <div id="preview-panel" class="hidden bg-white/90 backdrop-blur-2xl rounded-4xl shadow-4xl p-12 border border-white/50 mb-20">
                <div class="text-center mb-16">
                    <h2 class="text-5xl font-black bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent mb-6">✅ Link Ready!</h2>
                    <p class="text-3xl text-gray-700 font-light mb-12">Your customers can fill this form now</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
                    <!-- Unique URL -->
                    <div class="lg:col-span-2">
                        <div class="bg-gradient-to-r from-slate-900 to-gray-800 p-12 rounded-4xl text-white shadow-3xl text-center">
                            <h3 class="text-3xl font-bold mb-8 flex items-center justify-center gap-4">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                </svg>
                                Share this URL
                            </h3>
                            <div class="relative mb-8">
                                <input type="text" id="live-url" readonly 
                                       class="w-full bg-black/50 backdrop-blur-md px-12 py-8 text-3xl font-mono rounded-4xl border-4 border-white/30 text-white focus:outline-none focus:border-white/50 shadow-2xl" value="">
                                <button type="button" id="copy-live-url" class="absolute right-6 top-1/2 -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white px-8 py-4 rounded-3xl font-bold text-xl transition-all duration-300 shadow-lg">
                                    📋 Copy
                                </button>
                            </div>
                            <div class="flex gap-6 justify-center">
                                <a href="#" id="test-url" target="_blank" class="px-12 py-6 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-3xl shadow-2xl hover:shadow-3xl transform hover:-translate-y-2 transition-all duration-500">
                                    🧪 Test Form
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div>
                        <div class="bg-gradient-to-b from-slate-900 to-gray-800 p-12 rounded-4xl text-white shadow-3xl text-center">
                            <h3 class="text-3xl font-bold mb-8 flex items-center justify-center gap-4">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2v-4a2 2 0 00-2-2H8a2 2 0 00-2 2v4a2 2 0 002 2zm6-14h.01M8 7h8a2 2 0 012-2V3a2 2 0 00-2-2H8a2 2 0 00-2 2v2a2 2 0 002 2z"></path>
                                </svg>
                                QR Ready
                            </h3>
                            <div id="qr-container" class="mx-auto p-8 bg-black/30 backdrop-blur-xl rounded-4xl border-8 border-white/40 shadow-3xl mb-8 w-80 h-80 flex items-center justify-center">
                                Loading QR...
                            </div>
                            <div class="flex gap-4 justify-center flex-wrap">
                                <a href="#" id="download-qr" download class="px-12 py-6 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-bold rounded-3xl shadow-2xl hover:shadow-3xl transition-all duration-500 flex items-center gap-3">
                                    ⬇️ Download PNG
                                </a>
                                <button type="button" id="print-qr" class="px-12 py-6 bg-gradient-to-r from-gray-600 to-slate-700 text-white font-bold rounded-3xl shadow-2xl hover:shadow-3xl transition-all duration-500 flex items-center gap-3">
                                    🖨️ Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8 mt-16 text-center">
                    <div class="p-8 bg-white/70 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/50">
                        <p class="text-5xl font-black text-emerald-600 mb-4">1</p>
                        <p class="text-xl font-bold text-gray-800">New Links</p>
                    </div>
                    <div class="p-8 bg-white/70 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/50">
                        <p class="text-5xl font-black text-blue-600 mb-4">{{ App\Models\FormStore::count() }}</p>
                        <p class="text-xl font-bold text-gray-800">Total Links</p>
                    </div>
                    <div class="p-8 bg-white/70 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/50">
                        <p class="text-5xl font-black text-purple-600 mb-4">{{ App\Models\Form::where('status', 'active')->count() }}</p>
                        <p class="text-xl font-bold text-gray-800">Active Forms</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const formSelect = document.getElementById('form_select');
    const storeSelect = document.getElementById('store_select');
    const generateBtn = document.getElementById('generate-btn');
    const previewPanel = document.getElementById('preview-panel');

    function toggleButton() {
        const enabled = formSelect.value && storeSelect.value;
        generateBtn.disabled = !enabled;
        generateBtn.classList.toggle('opacity-50', !enabled);
        generateBtn.classList.toggle('cursor-not-allowed', !enabled);
    }

    formSelect.addEventListener('change', toggleButton);
    storeSelect.addEventListener('change', toggleButton);

    document.getElementById('copy-live-url').onclick = () => {
        navigator.clipboard.writeText(document.getElementById('live-url').value);
        this.textContent = '✅ Copied!';
        setTimeout(() => this.textContent = '📋 Copy', 2000);
    };

    // QR generation on form submit success (AJAX-ready)
    // Implementation continues...
});
</script>
@endpush
@endsection

