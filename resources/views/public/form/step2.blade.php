@extends('public.layout')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-4">{{ $form->name }}</h1>

    {{-- Determine form action based on whether store is present --}}
@if(isset($store) && $store)
        <form method="POST" action="/form/{{ request()->segment(2) }}">
        @csrf
    @else
        <form method="POST" action="{{ route('public.form.submit', $form->id) }}">
        @csrf
    @endif

    {{-- Store Info Section (only show when store is provided via URL) --}}
    @if(isset($store) && $store)
        <div class="mb-6 bg-blue-50 p-4 rounded border border-blue-200">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <div>
                    <p class="text-sm text-gray-600">Store</p>
                    <p class="font-semibold text-lg">{{ $store->name }}</p>
                    @if($store->address)
                        <p class="text-sm text-gray-500">{{ $store->address }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Email Field (required for all form submissions) --}}
    <div class="mb-4">
        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
            Email *
        </label>
        <input type="email" name="email" id="email" value="{{ old('email') }}"
               placeholder="Masukkan email Anda"
               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
               required>
        @error('email')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- WhatsApp Field (auto-show when enabled) --}}
    @if($form->enable_whatsapp_notification)
    <div class="mb-4">
        <label for="whatsapp_phone" class="block text-gray-700 text-sm font-bold mb-2">
            <svg class="w-4 h-4 inline mr-1 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-1.961-.971-.168-.1-.347-.199-.516-.301l-1.2-1.032c-.415-.347-1.006-.787-1.654-.937-.324-.074-.718-.098-.953-.054-1.152.206-2.306.788-3.13 1.739-.824.951-1.296 2.121-1.461 3.372-.165 1.251.187 2.528.704 3.641.518 1.113 1.346 2.041 2.424 2.636 1.078.595 2.324.856 3.564.723 1.24-.133 2.433-.698 3.44-1.535.997-.835 1.706-1.92 2.122-3.224.416-1.303.399-2.727-.12-4.012-.52-.951-1.306-1.73-2.23-2.289z"/>
            </svg>
            Nomor WhatsApp *
        </label>
        <div class="flex items-center">
            <span class="bg-gray-100 border border-gray-300 rounded-l py-2 px-3 text-gray-700 font-semibold">+62</span>
            <input type="tel" name="whatsapp_phone" id="whatsapp_phone" 
                   value="{{ old('whatsapp_phone') }}"
                   placeholder="812345678 (tanpa 0)"
                   pattern="[0-9]{9,12}"
                   class="shadow appearance-none border-t border-b border-r rounded-r w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                   required>
        </div>
        <p class="text-xs text-gray-500 mt-1">Contoh: 812345678 (gunakan format internasional tanpa +62 atau 0)</p>
        @error('whatsapp_phone')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
    @endif

    {{-- Form Fields --}}
    @foreach($form->fields->sortBy('sort_order') as $field)
    <div class="mb-4">
        <label for="field_{{ $field->id }}" class="block text-gray-700 text-sm font-bold mb-2">
            {{ $field->label }}
            @if($field->required) * @endif
        </label>

        {{-- Check if this field should be auto-filled with store data --}}
        @php
            $autoValue = '';
            $isReadOnly = false;
            
            if (isset($store) && $store) {
                // Auto-fill store name ONLY for fields that explicitly mention store/toko
                // Must contain both "nama"/"name" AND "store"/"toko" to avoid matching customer fields
                if (preg_match('/\b(nama|name)\b.*\b(store|toko)\b|\b(store|toko)\b.*\b(nama|name)\b/i', $field->label)) {
                    $autoValue = $store->name;
                    $isReadOnly = true;
                }
                // Auto-fill store address ONLY for fields that explicitly mention store/toko
                elseif (preg_match('/\b(alamat|address)\b.*\b(store|toko)\b|\b(store|toko)\b.*\b(alamat|address)\b/i', $field->label)) {
                    $autoValue = $store->address;
                    $isReadOnly = true;
                }
            }
        @endphp

        @if($field->type === 'textarea')
            <textarea name="field_{{ $field->id }}" id="field_{{ $field->id }}" rows="3"
                      placeholder="{{ $field->placeholder }}"
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                      {{ $field->required ? 'required' : '' }}
                      {{ $isReadOnly ? 'readonly' : '' }}>{{ $isReadOnly ? $autoValue : old("field_{$field->id}", $autoValue) }}</textarea>
@elseif($field->type === 'radio' && $field->options)
            @php $options = is_string($field->options) ? json_decode($field->options, true) : $field->options @endphp
            @foreach($options as $option)
                <div class="mb-2">
                    <label class="inline-flex items-center">
                        <input type="radio" name="field_{{ $field->id }}" value="{{ $option }}"
                               {{ old("field_{$field->id}") == $option ? 'checked' : '' }}
                               class="form-radio" {{ $field->required ? 'required' : '' }}>
                        <span class="ml-2">{{ $option }}</span>
                    </label>
                </div>
            @endforeach
        @elseif($field->type === 'select' && $field->options)
            @php $options = is_string($field->options) ? json_decode($field->options, true) : $field->options @endphp
            <select name="field_{{ $field->id }}" id="field_{{ $field->id }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    {{ $field->required ? 'required' : '' }}>
                <option value="">Choose an option</option>
                @foreach($options as $option)
                    <option value="{{ $option }}" {{ old("field_{$field->id}") == $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            </select>
        @else
            <input type="{{ $field->type }}" name="field_{{ $field->id }}" id="field_{{ $field->id }}"
                   value="{{ $isReadOnly ? $autoValue : old("field_{$field->id}", $autoValue) }}"
                   placeholder="{{ $field->placeholder }}"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                   {{ $field->required ? 'required' : '' }}
                   {{ $isReadOnly ? 'readonly' : '' }}>
        @endif

        @if($isReadOnly)
            <p class="text-xs text-gray-500 mt-1">* Data ini diisi otomatis dari store</p>
        @endif

        @error("field_{$field->id}")
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
    </div>
    @endforeach

    {{-- Include detailed policies --}}
    @include('public.policies')
    <div class="mb-6 bg-gray-50 p-4 rounded">
        <h3 class="text-lg font-semibold mb-4">Persetujuan dan Kebijakan Privasi</h3>

        <div class="mb-4">
            <label class="inline-flex items-start">
                <input type="checkbox" name="consent_personal_data" value="1" class="form-checkbox mt-1" required>
                <span class="ml-2 text-sm">
                    Saya menyetujui pengumpulan dan pemrosesan data pribadi saya sesuai dengan <button type="button" onclick="openPrivacyModal()" class="text-blue-600 hover:text-blue-800 underline">Kebijakan Privasi</button> yang berlaku. Data saya akan digunakan untuk keperluan survei ini dan tidak akan dibagikan kepada pihak ketiga tanpa izin saya.
                </span>
            </label>
            @error('consent_personal_data')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="inline-flex items-start">
                <input type="checkbox" name="consent_terms" value="1" class="form-checkbox mt-1" required>
                <span class="ml-2 text-sm">
                    Saya telah membaca dan menyetujui <button type="button" onclick="openTermsModal()" class="text-blue-600 hover:text-blue-800 underline">Syarat dan Ketentuan</button> penggunaan layanan ini.
                </span>
            </label>
            @error('consent_terms')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="inline-flex items-start">
                <input type="checkbox" name="consent_privacy_policy" value="1" class="form-checkbox mt-1" required>
                <span class="ml-2 text-sm">
                    Saya menyetujui <button type="button" onclick="openPrivacyModal()" class="text-blue-600 hover:text-blue-800 underline">Kebijakan Privasi</button> dan memahami bagaimana data saya akan dilindungi dan digunakan.
                </span>
            </label>
            @error('consent_privacy_policy')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex items-center justify-between">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Submit
        </button>
    </div>
</form>
</div>
@endsection
