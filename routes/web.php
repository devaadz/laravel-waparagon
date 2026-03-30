<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\FormStoreController;
use App\Http\Controllers\Admin\FieldController;
use App\Http\Controllers\Admin\WhatsappMessageController;
use App\Http\Controllers\Admin\WhatsappDeviceController;
use App\Http\Controllers\Admin\ResponseController;
use App\Http\Controllers\Admin\NotificationLogController;
use App\Http\Controllers\Public\FormSubmitController;
use App\Http\Controllers\Webhook\GoWAWebhookController;
// use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\PublicFormController;


Route::get('/form/{slug}/qr/download', [PublicFormController::class, 'downloadQr'])
->name('public.form.store.qr.download');

Route::get('/form/{slug}/qr', [PublicFormController::class, 'qr'])
    ->name('public.form.store.qr');

    Route::get('/', function () {
    return view('welcome');
});

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('documentation', function () {
        return view('documentation');
    })->name('documentation');
    Route::resource('stores', StoreController::class);
Route::resource('forms', FormController::class);
    
    Route::resource('form-stores', FormStoreController::class);
    
    // Form-Store linking routes
    Route::get('forms/{form}/stores', [FormController::class, 'manageStores'])->name('forms.stores');
    Route::post('forms/{form}/stores', [FormController::class, 'linkStore'])->name('forms.stores.link');
    Route::delete('forms/{form}/stores/{store}', [FormController::class, 'unlinkStore'])->name('forms.stores.unlink');
    
    Route::resource('forms.fields', FieldController::class)->shallow();
    Route::match(['get', 'post'], 'responses/export', [ResponseController::class, 'export'])->name('responses.export');
    Route::resource('responses', ResponseController::class)->only(['index', 'show']);
    Route::resource('whatsapp', WhatsappMessageController::class)->only(['index', 'show', 'destroy']);
    Route::get('whatsapp-export', [WhatsappMessageController::class, 'export'])->name('whatsapp.export');
    Route::resource('devices', WhatsappDeviceController::class)->except(['edit', 'update']);
    Route::get('devices/{device}/login', [WhatsappDeviceController::class, 'login'])->name('devices.login');
    Route::post('devices/sync', [WhatsappDeviceController::class, 'sync'])->name('devices.sync');
    Route::post('devices/{device}/link-store', [WhatsappDeviceController::class, 'linkToStore'])->name('devices.link-store');
    Route::post('devices/{device}/unlink-store', [WhatsappDeviceController::class, 'unlinkFromStore'])->name('devices.unlink-store');
    
    // Notification Logs
    Route::resource('notification-logs', NotificationLogController::class)->only(['index', 'show']);
    Route::get('notification-logs-export', [NotificationLogController::class, 'export'])->name('notification-logs.export');
    Route::post('notification-logs/{log}/retry', [NotificationLogController::class, 'retry'])->name('notification-logs.retry');
});

// Public routes - store-specific URL (e.g., /form/survey-form-toko-a)
Route::get('form/{slug}', [FormSubmitController::class, 'showBySlug'])->name('form.store');
Route::post('form/{slug}', [FormSubmitController::class, 'storeBySlug'])->name('form.store.submit');

// Public routes - original form routes
Route::prefix('form')->name('public.')->group(function () {
    // use implicit model binding so $form is resolved by primary key (uuid id)
    Route::get('{form}', [FormSubmitController::class, 'show'])->name('form.show');
    Route::post('{form}', [FormSubmitController::class, 'store'])->name('form.submit');
    Route::get('{form}/qr', [FormSubmitController::class, 'qr'])->name('form.qr');
    Route::get('{form}/preview', [FormSubmitController::class, 'preview'])->name('form.preview');
    // QR code for store-specific link
    Route::get('store/{slug}/qr', [FormSubmitController::class, 'qrBySlug'])->name('form.store.qr');
});

// API routes
Route::prefix('api')->name('api.')->group(function () {
    Route::post('send-message', [App\Http\Controllers\Api\MessageController::class, 'send'])->name('send-message');
});

// Webhook routes (no CSRF protection)
Route::post('/webhook/gowa', [GoWAWebhookController::class, 'handle'])->withoutMiddleware('VerifyCsrfToken');

