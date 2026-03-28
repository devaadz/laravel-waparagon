<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\Store;
use App\Models\FormStore;
use App\Models\FormField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $forms = Form::all();
        return view('admin.forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.forms.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'enable_email_notification' => 'boolean',
            'email_subject' => 'nullable|string|max:255',
            'email_template' => 'nullable|string',
        ]);

        // "id" is a UUID primary key populated by HasUuids
         $form = Form::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->status,
        ]);
        
        return redirect()->route('admin.forms.index')->with('success', 'Form created successfully.');


    }

    /**
     * Display the specified resource.
     */
    public function show(Form  $form)
    {
        // $form = Form::with('slug')->findOrFail($id);
        // dd('FormController@show is not implemented yet', $form);
        return view('admin.forms.show', compact('form'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Form $form)
    {
        return view('admin.forms.edit', compact('form'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Form $form)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'enable_whatsapp_image' => 'boolean',
            'whatsapp_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'delete_whatsapp_image' => 'boolean',
            'fields' => 'array',
        ]);

        // =========================
        // HANDLE IMAGE DULU 🔥
        // =========================
        $imagePath = $form->whatsapp_image;

        // delete image
        if ($request->boolean('delete_whatsapp_image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        // upload image baru
        if ($request->hasFile('whatsapp_image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            $imagePath = $request->file('whatsapp_image')->store('whatsapp_images', 'public');
        }

        // =========================
        // UPDATE SEKALI AJA 🔥
        // =========================
        $form->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . $form->id,
            'description' => $request->description,
            'status' => $request->status,

            'enable_email_notification' => $request->boolean('enable_email_notification'),
            'email_subject' => $request->email_subject,
            'email_template' => $request->email_template,

            'enable_whatsapp_notification' => $request->boolean('enable_whatsapp_notification'),
            'whatsapp_template' => $request->whatsapp_template,

            'enable_whatsapp_image' => $request->boolean('enable_whatsapp_image'),
            'whatsapp_image' => $imagePath,
        ]);

        // =========================
        // HANDLE FIELDS (BIAR LO GAK RUSAK LOGIC LO)
        // =========================
        $existingFieldIds = $form->fields->pluck('id')->toArray();
        $updatedFieldIds = [];

        if ($request->has('fields')) {
            foreach ($request->fields as $index => $fieldData) {

                $options = [];
                if (!empty($fieldData['options_text'])) {
                    $options = array_filter(array_map('trim', explode("\n", $fieldData['options_text'])));
                }

                if (!empty($fieldData['id'])) {
                    $field = FormField::find($fieldData['id']);

                    if ($field && $field->form_id == $form->id) {
                        $field->update([
                            'label' => $fieldData['label'],
                            'type' => $fieldData['type'],
                            'placeholder' => $fieldData['placeholder'] ?? null,
                            'required' => $fieldData['required'] ?? false,
                            'options' => json_encode($options),
                            'sort_order' => $fieldData['sort_order'] ?? $index + 1,
                        ]);

                        $updatedFieldIds[] = $field->id;
                    }
                } else {
                    $field = FormField::create([
                        'form_id' => $form->id,
                        'label' => $fieldData['label'],
                        'type' => $fieldData['type'],
                        'placeholder' => $fieldData['placeholder'] ?? null,
                        'required' => $fieldData['required'] ?? false,
                        'options' => json_encode($options),
                        'sort_order' => $fieldData['sort_order'] ?? $index + 1,
                    ]);

                    $updatedFieldIds[] = $field->id;
                }
            }
        }

        FormField::whereIn('id', array_diff($existingFieldIds, $updatedFieldIds))->delete();

        return redirect()->route('admin.forms.edit', $form)
            ->with('success', 'Form updated successfully.');
    }
//     public function update(Request $request, Form $form)
//     {
//         // dd($form, $request);
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'description' => 'nullable|string',
//             'status' => 'required|in:active,inactive',
//             'enable_whatsapp_image' => 'boolean',
// 'whatsapp_image' => 'nullable|file|max:5120',
//             'delete_whatsapp_image' => 'boolean',
//             'fields' => 'array',
//         ]);

//         $form->update([
//             'name' => $request->name,
//             'slug' => Str::slug($request->name) . '-' . $form->id,
//             'description' => $request->description,
//             'status' => $request->status,
//             'enable_email_notification' => $request->boolean('enable_email_notification'),
//             'email_subject' => $request->email_subject,
//             'email_template' => $request->email_template,
//             'enable_whatsapp_notification' => $request->boolean('enable_whatsapp_notification'),
// 'whatsapp_template' => $request->whatsapp_template,
//             'enable_whatsapp_image' => $request->boolean('enable_whatsapp_image'),
//             'whatsapp_image' => $form->whatsapp_image,
//         ]);

//         // Handle image upload/delete
//         if ($request->hasFile('whatsapp_image')) {
//             // Delete old image if exists
//             if ($form->whatsapp_image && Storage::disk('public')->exists($form->whatsapp_image)) {
//                 Storage::disk('public')->delete($form->whatsapp_image);
//             }
//             $path = $request->file('whatsapp_image')->store('whatsapp_images', 'public');
//             $form->whatsapp_image = $path;
//         } elseif ($request->boolean('delete_whatsapp_image')) {
//             if ($form->whatsapp_image && Storage::disk('public')->exists($form->whatsapp_image)) {
//                 Storage::disk('public')->delete($form->whatsapp_image);
//             }
//             $form->whatsapp_image = null;
//         }

//         $form->save();

//         $existingFieldIds = $form->fields->pluck('id')->toArray();
//         $updatedFieldIds = [];

//         if ($request->has('fields')) {
//             foreach ($request->fields as $index => $fieldData) {

//                 $options = [];
//                 if (!empty($fieldData['options_text'])) {
//                     $options = array_filter(array_map('trim', explode("\n", $fieldData['options_text'])));
//                 }

//                 if (!empty($fieldData['id'])) {
//                     $field = FormField::find($fieldData['id']);

//                     if ($field && $field->form_id == $form->id) {
//                         $field->update([
//                             'label' => $fieldData['label'],
//                             'type' => $fieldData['type'],
//                             'placeholder' => $fieldData['placeholder'] ?? null,
//                             'required' => $fieldData['required'] ?? false,
//                             'options' => json_encode($options),
//                             'sort_order' => $fieldData['sort_order'] ?? $index + 1,
//                         ]);

//                         $updatedFieldIds[] = $field->id;
//                     }
//                 } else {
//                     $field = FormField::create([
//                         'form_id' => $form->id,
//                         'label' => $fieldData['label'],
//                         'type' => $fieldData['type'],
//                         'placeholder' => $fieldData['placeholder'] ?? null,
//                         'required' => $fieldData['required'] ?? false,
//                         'options' => json_encode($options),
//                         'sort_order' => $fieldData['sort_order'] ?? $index + 1,
//                     ]);

//                     $updatedFieldIds[] = $field->id;
//                 }
//             }
//         }

//         FormField::whereIn('id', array_diff($existingFieldIds, $updatedFieldIds))->delete();

//         return redirect()->route('admin.forms.edit', $form)
//             ->with('success', 'Form updated successfully.');
//     }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $form = Form::findOrFail($id);
        $form->delete();

        return redirect()->route('admin.forms.index')->with('success', 'Form deleted successfully.');
    }

    /**
     * Manage linked stores for a form.
     */
    public function manageStores(string $id)
    {
        $form = Form::with('formStores.store')->findOrFail($id);
        $stores = Store::all();
        $linkedStoreIds = $form->formStores->pluck('store_id')->toArray();
        
        return view('admin.forms.stores', compact('form', 'stores', 'linkedStoreIds'));
    }

    /**
     * Link a store to a form.
     */
    public function linkStore(Request $request, string $id)
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
        ]);

        $form = Form::findOrFail($id);
        $store = Store::findOrFail($request->store_id);

        // Check if already linked
        $existingLink = FormStore::where('form_id', $form->id)
            ->where('store_id', $store->id)
            ->first();

        if ($existingLink) {
            return back()->with('error', 'Store is already linked to this form.');
        }

        // Generate name: store_name_form_name (spaces → _)
        $name = Str::slug($store->name . ' ' . $form->name, '_');
        
        // Generate slug: store_name_form_name (spaces → _)
        $slug = Str::slug($store->name . '_' . $form->name, '_');
        
        // Check if slug already exists, if so, add random suffix
        $existingSlug = FormStore::where('custom_url_slug', $slug)->first();
        if ($existingSlug) {
            $slug = $slug . '_' . substr(uniqid(), -6);
        }
        
        // Check if name already exists for this form-store combo, if so, add suffix
        $existingName = FormStore::where('form_id', $form->id)
                                ->where('store_id', $store->id)
                                ->where('name', $name)->first();
        if ($existingName) {
            $name = $name . '_' . substr(uniqid(), -4);
        }

        // Create the link
        FormStore::create([
            'form_id' => $form->id,
            'store_id' => $store->id,
            'name' => $name,
            'custom_url_slug' => $slug,
        ]);

        return redirect()->route('admin.forms.stores', $form->id)
            ->with('success', 'Store linked successfully!');
    }

    /**
     * Unlink a store from a form.
     */
    public function unlinkStore(string $id, string $storeId)
    {
        $form = Form::findOrFail($id);
        
        $formStore = FormStore::where('form_id', $form->id)
            ->where('store_id', $storeId)
            ->first();

        if ($formStore) {
            $formStore->delete();
            return redirect()->route('admin.forms.stores', $form->id)
                ->with('success', 'Store unlinked successfully!');
        }

        return redirect()->route('admin.forms.stores', $form->id)
            ->with('error', 'Link not found.');
    }
}
