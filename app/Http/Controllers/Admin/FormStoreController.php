<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormStore;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Form;
use App\Models\Store;

class FormStoreController extends Controller
{
    public function index()
    {
        $formStores = FormStore::with(['form', 'store'])->latest()->get();
        return view('admin.form-stores.index', compact('formStores'));
    }

    public function create()
    {
        $forms = Form::where('status', 'active')->get();
        $stores = Store::all();
        return view('admin.form-stores.create', compact('forms', 'stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'form_id' => 'required|exists:forms,id',
            'store_id' => 'required|exists:stores,id',
        ]);

        // Check if already linked
        if (FormStore::where('form_id', $request->form_id)->where('store_id', $request->store_id)->exists()) {
            return back()->with('error', 'This form and store are already linked.');
        }

        $form = Form::findOrFail($request->form_id);
        $store = Store::findOrFail($request->store_id);

        $name = Str::slug($store->name . ' ' . $form->name, '_');
        $slug = Str::slug($store->name . '_' . $form->name, '_');

        // Make unique
        $counter = 1;
        while (FormStore::where('custom_url_slug', $slug)->exists()) {
            $slug = Str::slug($store->name . '_' . $form->name . '_' . $counter, '_');
            $counter++;
        }

        FormStore::create([
            'form_id' => $form->id,
            'store_id' => $store->id,
            'name' => $name,
            'custom_url_slug' => $slug,
        ]);

        return redirect()->back()->with('success', "Link created! URL: /form/$slug");
    }
}

