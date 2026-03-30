@extends('admin.layout')

@section('content')
<div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl shadow-2xl p-8 text-white mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold">Edit Form</h1>
            <p class="text-blue-100 mt-2">Configure your form settings, notifications, and fields</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.forms.index') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-bold py-3 px-6 rounded-xl transition-all duration-300">
                ← All Forms
            </a>
            <a href="{{ route('admin.form-stores.index') }}" class="bg-white hover:bg-white/90 text-blue-600 font-bold py-3 px-6 rounded-xl shadow-lg transition-all duration-300">
                🔗 Link Stores
            </a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.forms.update', $form->slug) }}" id="form-edit" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @method('PUT')

    <!-- Basic Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h3 class="text-2xl font-bold mb-6 text-gray-800 border-b-2 border-blue-100 pb-4">Basic Information</h3>
            
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-3">Form Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $form->name) }}" 
                           class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 shadow-sm" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-3">Description</label>
                    <textarea name="description" id="description" rows="4" 
                              class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 shadow-sm resize-vertical">{{ old('description', $form->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Status</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-xl border-2 border-green-200 cursor-pointer hover:shadow-md transition-all duration-300">
                            <input type="radio" name="status" value="active" {{ old('status', $form->status) === 'active' ? 'checked' : '' }} class="sr-only">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-3 text-lg font-semibold text-gray-800">Active</span>
                        </label>
                        <label class="inline-flex items-center bg-gradient-to-r from-gray-50 to-gray-100 p-4 rounded-xl border-2 border-gray-300 cursor-pointer hover:shadow-md transition-all duration-300 opacity-70">
                            <input type="radio" name="status" value="inactive" {{ old('status', $form->status) === 'inactive' ? 'checked' : '' }} class="sr-only">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="ml-3 text-lg font-semibold text-gray-600">Inactive</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="text-red-500 text-sm mt-4 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Notification Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Email Card -->
            <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl shadow-xl p-8 border-2 border-emerald-100">
                <div class="flex items-center mb-6">
                    <div class="p-3 bg-emerald-100 rounded-2xl">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.27 4.31c.912.543 2.46.543 3.372 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold ml-4 text-gray-800">Email Notifications</h3>
                </div>

                <div class="space-y-5">
                    <div class="flex items-center p-4 bg-white rounded-xl shadow-sm border">
                        <input type="checkbox" name="enable_email_notification" value="1" {{ old('enable_email_notification', $form->enable_email_notification) ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                        <span class="ml-4 text-lg font-semibold text-gray-800">Enable email notifications</span>
                    </div>

                    <div>
                        <label for="email_subject" class="block text-sm font-semibold text-gray-700 mb-3">Email Subject</label>
                        <input type="text" name="email_subject" id="email_subject" value="{{ old('email_subject', $form->email_subject) }}" 
                               placeholder="New form submission received" 
                               class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-emerald-200 focus:border-emerald-400 transition-all duration-300 shadow-sm">
                        @error('email_subject')
                            <p class="text-red-500 text-sm mt-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="email_template" class="block text-sm font-semibold text-gray-700 mb-3">Email Template</label>
                        <textarea name="email_template" id="email_template" rows="5" 
                                  placeholder="You have received a new form submission..." 
                                  class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-emerald-200 focus:border-emerald-400 transition-all duration-300 shadow-sm resize-vertical">{{ old('email_template', $form->email_template) }}</textarea>
                        <p class="text-xs text-gray-500 mt-2">Variables: <code class="bg-gray-100 px-2 py-1 rounded">{form_name}</code> <code>{email}</code> <code>{store_name}</code> <code>{admin_url}</code> <code>{submission_data}</code></p>
                    </div>
                </div>
            </div>

            <!-- WhatsApp Card -->
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl shadow-xl p-8 border-2 border-indigo-100">
                <div class="flex items-center mb-6">
                    <div class="p-3 bg-indigo-100 rounded-2xl">
                        <svg class="w-8 h-8 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-1.961-.971-.168-.1-.347-.199-.516-.301l-1.2-1.032c-.415-.347-1.006-.787-1.654-.937-.324-.074-.718-.098-.953-.054-1.152.206-2.306.788-3.13 1.739-.824.951-1.296 2.121-1.461 3.372-.165 1.251.187 2.528.704 3.641.518 1.113 1.346 2.041 2.424 2.636 1.078.595 2.324.856 3.564.723 1.24-.133 2.433-.698 3.44-1.535.997-.835 1.706-1.92 2.122-3.224.416-1.303.399-2.727-.12-4.012-.52-.951-1.306-1.73-2.23-2.289zM9.884 14.382c-.297-.149-1.758-.867-1.961-.971-.168-.1-.347-.199-.516-.301l-1.2-1.032c-.415-.347-1.006-.787-1.654-.937-.324-.074-.718-.098-.953-.054-1.152.206-2.306.788-3.13 1.739-.824.951-1.296 2.121-1.461 3.372-.165 1.251.187 2.528.704 3.641.518 1.113 1.346 2.041 2.424 2.636 1.078.595 2.324.856 3.564.723 1.24-.133 2.433-.698 3.44-1.535.997-.835 1.706-1.92 2.122-3.224.416-1.303.399-2.727-.12-4.012-.52-.951-1.306-1.73-2.23-2.289z"></path>
                            <path d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V4a2 2 0 00-2-2H4zm0 1h16v12H4V3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold ml-4 text-gray-800">WhatsApp Notifications</h3>
                </div>

                <div class="space-y-5">
                    <div class="flex items-center p-4 bg-white rounded-xl shadow-sm border">
                        <input type="checkbox" name="enable_whatsapp_notification" value="1" {{ old('enable_whatsapp_notification', $form->enable_whatsapp_notification) ? 'checked' : '' }} class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-4 text-lg font-semibold text-gray-800">Send WhatsApp to store device</span>
                    </div>

                    <div id="whatsapp-notification-fields" class="space-y-5">
                        <div>
                            <label for="whatsapp_template" class="block text-sm font-semibold text-gray-700 mb-3">Message Template</label>
                            <textarea name="whatsapp_template" id="whatsapp_template" rows="5" 
                                      placeholder="Halo! *{customer_name}* terisi dari {store_name}..." 
                                      class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-indigo-200 focus:border-indigo-400 transition-all duration-300 shadow-sm resize-vertical">{{ old('whatsapp_template', $form->whatsapp_template) }}</textarea>
                            <p class="text-xs text-gray-500 mt-2">Variables: <code class="bg-gray-100 px-2 py-1 rounded">{customer_name}</code> <code>{email}</code> <code>{store_name}</code> <code>{form_name}</code><code>{submission_data}</code></p>
                        </div>

                        <div>
                            <label class="flex items-center p-3 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border">
                                <input type="checkbox" name="enable_whatsapp_image" value="1" id="enable_whatsapp_image" {{ old('enable_whatsapp_image', $form->enable_whatsapp_image) ? 'checked' : '' }} class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                <span class="ml-4 font-semibold text-gray-800">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Send image with WhatsApp
                                </span>
                            </label>
                        </div>

                        <div id="whatsapp-image-section" style="display: {{ old('enable_whatsapp_image', $form->enable_whatsapp_image) ? 'block' : 'none' }};" class="mt-4 p-5 bg-white rounded-xl shadow-sm border-2 border-dashed border-orange-200 hover:border-orange-400 transition-colors">
                            <label for="whatsapp_image" class="block text-sm font-semibold text-gray-700 mb-4">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Notification Image (Max 5MB)
                            </label>
                            
                            <div class="space-y-4">
                                <input type="file" name="whatsapp_image" id="whatsapp_image" accept="image/*" class="w-full px-5 py-4 border-2 border-dashed border-gray-300 rounded-xl focus:ring-4 focus:ring-orange-200 focus:border-orange-400 transition-all duration-300 cursor-pointer hover:bg-orange-50 file:mr-5 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700">
                                
                                @if($form->whatsapp_image)
                                <div class="text-center">
                                    <img src="{{ $form->whatsapp_image_url ?? asset('storage/' . $form->whatsapp_image) }}" alt="Current image" class="mx-auto max-w-xs max-h-48 object-cover rounded-2xl shadow-lg ring-4 ring-orange-100 hover:ring-orange-200 transition-all duration-300 cursor-zoom-in mx-auto">
                                    <div class="flex justify-center items-center gap-4 mt-3">
                                        <label class="inline-flex items-center bg-red-100 hover:bg-red-200 text-red-800 font-semibold py-2 px-4 rounded-xl cursor-pointer transition-all duration-300">
                                            <input type="checkbox" name="delete_whatsapp_image" value="1" id="delete_whatsapp_image" class="form-checkbox">
                                            <span class="ml-2">🗑️ Delete</span>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                
                                <div id="image-preview" class="mt-4 p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border-2 border-dashed border-orange-200 hidden">
                                    <img id="image-preview-img" src="" class="max-w-full max-h-64 object-cover rounded-2xl shadow-lg ring-4 ring-orange-100 hover:scale-105 transition-all duration-500 cursor-pointer mx-auto">
                                </div>
                            </div>
                            
                            <p class="text-xs text-gray-500 mt-4 text-center italic">PNG, JPG, GIF, WEBP • Max 5MB • Auto preview</p>
                            
                            @error('whatsapp_image')
                                <p class="text-red-500 text-sm mt-2 flex items-center justify-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="flex items-center p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border">
                                <input type="checkbox" name="whatsapp_template_as_caption" value="1" id="whatsapp_template_as_caption" {{ old('whatsapp_template_as_caption', $form->whatsapp_template_as_caption) ? 'checked' : '' }} class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="ml-4 font-semibold text-gray-800">Use message template as image caption</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-2 ml-8">If enabled, the message template will be used as the image caption instead of sending a separate text message.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Fields -->
    <div class="bg-gradient-to-r from-slate-50 to-gray-50 rounded-2xl shadow-xl p-8">
        <h3 class="text-3xl font-bold mb-8 text-gray-800 flex items-center">
            <svg class="w-10 h-10 mr-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            Form Fields ({{ $form->fields->count() }})
        </h3>
        
        <div id="fields-container" class="space-y-4">
            @foreach($form->fields as $index => $field)
            <div class="field-item bg-white rounded-2xl shadow-lg p-8 border-2 border-gray-100 hover:border-blue-200 transition-all duration-300 group" data-index="{{ $index }}">
                <div class="flex justify-between items-start mb-6">
                    <h4 class="text-xl font-bold text-gray-800">Field #{{ $index + 1 }}</h4>
                    <button type="button" class="remove-field bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-xl shadow-lg transition-all duration-300 opacity-0 group-hover:opacity-100">
                        🗑️ Remove
                    </button>
                </div>
                
                <input type="hidden" name="fields[{{ $index }}][id]" value="{{ $field->id }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Label</label>
                        <input type="text" name="fields[{{ $index }}][label]" value="{{ old('fields.' . $index . '.label', $field->label) }}" 
                               class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Type</label>
                        <select name="fields[{{ $index }}][type]" class="field-type w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 shadow-sm" required>
                            <option value="text" {{ old('fields.' . $index . '.type', $field->type) === 'text' ? 'selected' : '' }}>Text</option>
                            <option value="number" {{ old('fields.' . $index . '.type', $field->type) === 'number' ? 'selected' : '' }}>Number</option>
                            <option value="email" {{ old('fields.' . $index . '.type', $field->type) === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="tel" {{ old('fields.' . $index . '.type', $field->type) === 'tel' ? 'selected' : '' }}>Phone</option>
                            <option value="date" {{ old('fields.' . $index . '.type', $field->type) === 'date' ? 'selected' : '' }}>Date</option>
                            <option value="textarea" {{ old('fields.' . $index . '.type', $field->type) === 'textarea' ? 'selected' : '' }}>Textarea</option>
                            
                            <option value="radio" {{ old('fields.' . $index . '.type', $field->type) === 'radio' ? 'selected' : '' }}>Radio</option>
                            <option value="checkbox" {{ old('fields.' . $index . '.type', $field->type) === 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                            <option value="select" {{ old('fields.' . $index . '.type', $field->type) === 'select' ? 'selected' : '' }}>Dropdown</option>

                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Sort Order</label>
                        <input type="number" name="fields[{{ $index }}][sort_order]" value="{{ old('fields.' . $index . '.sort_order', $field->sort_order) }}" 
                               class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 shadow-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Placeholder</label>
                        <input type="text" name="fields[{{ $index }}][placeholder]" value="{{ old('fields.' . $index . '.placeholder', $field->placeholder) }}" 
                               class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 shadow-sm">
                    </div>
                    <div class="flex items-center">
                        <label class="inline-flex items-center bg-gradient-to-r from-amber-50 to-orange-50 p-4 rounded-xl border-2 cursor-pointer hover:shadow-md transition-all duration-300">
                            <input type="checkbox" name="fields[{{ $index }}][required]" value="1" {{ old('fields.' . $index . '.required', $field->required) ? 'checked' : '' }} class="form-checkbox">
                            <span class="ml-3 text-lg font-semibold text-gray-800">Required Field</span>
                        </label>
                    </div>
                </div>

                <div class="options-container" style="{{ in_array($field->type, ['radio', 'select', 'checkbox']) ? '' : 'display: none;' }}" class="bg-gradient-to-r from-gray-50 to-slate-50 p-6 rounded-xl border-2 border-dashed border-gray-300">
                    <label class="block text-lg font-bold text-gray-800 mb-4">Options (one per line)</label>
                    <textarea name="fields[{{ $index }}][options_text]" rows="4" class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-400 transition-all duration-300 shadow-sm resize-vertical">{{ old('fields.' . $index . '.options_text', is_array($field->options) ? implode("\n", $field->options) : (is_string($field->options) ? implode("\n", json_decode($field->options, true) ?? []) : '')) }}</textarea>
                    <input type="hidden" name="fields[{{ $index }}][options]" value="{{ old('fields.' . $index . '.options', json_encode(is_array($field->options) ? $field->options : json_decode($field->options, true) ?? [])) }}">
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="flex gap-4">
            <button type="button" id="add-field" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold py-4 px-8 rounded-2xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg> Add New Field
            </button>
            <button type="submit" class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold py-4 px-12 rounded-2xl shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                Update Form
            </button>
        </div>
    </div>

    <a href="{{ route('admin.forms.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 font-semibold py-3 px-6 bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border border-gray-200">
        ← Cancel & Back to Forms
    </a>
</form>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let fieldIndex = {{ $form->fields->count() }};

    // WhatsApp notification toggle - Main checkbox
    const enableWhatsappNotification = document.querySelector('input[name="enable_whatsapp_notification"]');
    const whatsappNotificationFields = document.getElementById('whatsapp-notification-fields');
    
    function toggleWhatsappFields() {
        const isEnabled = enableWhatsappNotification.checked;
        whatsappNotificationFields.style.display = isEnabled ? 'block' : 'none';
    }
    
    // Initial state
    toggleWhatsappFields();
    
    // Listen to checkbox change
    enableWhatsappNotification.addEventListener('change', function() {
        toggleWhatsappFields();
        // Jika disable, reset juga image checkbox
        if (!this.checked) {
            document.getElementById('enable_whatsapp_image').checked = false;
            document.getElementById('whatsapp_image-section').style.display = 'none';
        }
    });

    // WhatsApp image functionality
    const enableWhatsappImage = document.getElementById('enable_whatsapp_image');
    const whatsappImageSection = document.getElementById('whatsapp_image-section');
    const whatsappImageInput = document.getElementById('whatsapp_image');
    const imagePreview = document.getElementById('image-preview');
    
    if (enableWhatsappImage) {
        enableWhatsappImage.addEventListener('change', function() {
            whatsappImageSection.style.display = this.checked ? 'block' : 'none';
        });
        
        if (whatsappImageInput) {
            whatsappImageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file && file.size > 5 * 1024 * 1024) {
                    alert('File terlalu besar! Maksimal 5MB.');
                    e.target.value = '';
                    return;
                }
                if (file && imagePreview) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.classList.remove('hidden');
                        document.getElementById('image-preview-img').src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    // Field management
    document.getElementById('add-field').addEventListener('click', function() {
        addField();
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-field')) {
            e.target.closest('.field-item').remove();
            updateFieldIndices();
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('field-type')) {
            const container = e.target.closest('.field-item');
            const optionsContainer = container.querySelector('.options-container');
            if (['radio', 'select','checkbox'].includes(e.target.value)) {
                optionsContainer.style.display = 'block';
            } else {
                optionsContainer.style.display = 'none';
            }
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.name && e.target.name.endsWith('[options_text]')) {
            const container = e.target.closest('.field-item');
            const hiddenInput = container.querySelector('input[name*="[options]"]');
            const options = e.target.value.split('\\n').map(opt => opt.trim()).filter(opt => opt);
            hiddenInput.value = JSON.stringify(options);
        }
    });

    function addField() {
        const container = document.getElementById('fields-container');
        const fieldHtml = `
            <div class="field-item bg-white rounded-2xl shadow-lg p-8 border-2 border-gray-100 hover:border-blue-200 transition-all duration-300 group" data-index="${fieldIndex}">
                <div class="flex justify-between items-start mb-6">
                    <h4 class="text-xl font-bold text-gray-800">Field #${fieldIndex + 1}</h4>
                    <button type="button" class="remove-field bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-xl shadow-lg transition-all duration-300 opacity-0 group-hover:opacity-100">
                        🗑️ Remove
                    </button>
                </div>
                
                <input type="hidden" name="fields[${fieldIndex}][id]" value="">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Label</label>
                        <input type="text" name="fields[${fieldIndex}][label]" class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Type</label>
                        <select name="fields[${fieldIndex}][type]" class="field-type w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 shadow-sm" required>
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="email">Email</option>
                            <option value="tel">Phone</option>
                            <option value="date">Date</option>
                            <option value="textarea">Textarea</option>
                            <option value="radio">Radio</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="select">Dropdown</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Sort Order</label>
                        <input type="number" name="fields[${fieldIndex}][sort_order]" value="${fieldIndex + 1}" class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 shadow-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Placeholder</label>
                        <input type="text" name="fields[${fieldIndex}][placeholder]" class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-400 transition-all duration-300 shadow-sm">
                    </div>
                    <div class="flex items-center">
                        <label class="inline-flex items-center bg-gradient-to-r from-amber-50 to-orange-50 p-4 rounded-xl border-2 cursor-pointer hover:shadow-md transition-all duration-300">
                            <input type="checkbox" name="fields[${fieldIndex}][required]" value="1" class="form-checkbox">
                            <span class="ml-3 text-lg font-semibold text-gray-800">Required Field</span>
                        </label>
                    </div>
                </div>

                <div class="options-container" style="display: none;" class="bg-gradient-to-r from-gray-50 to-slate-50 p-6 rounded-xl border-2 border-dashed border-gray-300">
                    <label class="block text-lg font-bold text-gray-800 mb-4">Options (one per line)</label>
                    <textarea name="fields[${fieldIndex}][options_text]" rows="4" class="w-full px-5 py-4 border border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-200 focus:border-purple-400 transition-all duration-300 shadow-sm resize-vertical"></textarea>
                    <input type="hidden" name="fields[${fieldIndex}][options]" value="[]">
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', fieldHtml);
        fieldIndex++;
        updateFieldIndices();
    }

    function updateFieldIndices() {
        const fieldItems = document.querySelectorAll('.field-item');
        fieldItems.forEach((item, index) => {
            item.setAttribute('data-index', index);
            item.querySelector('h4').textContent = `Field #${index + 1}`;
            const inputs = item.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/fields\[\d+\]/, `fields[${index}]`);
                }
            });
        });
    }
});
</script>
@endsection
