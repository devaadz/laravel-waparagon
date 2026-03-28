<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormField;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $formId = $request->query('form_id');
        $fields = FormField::where('form_id', $formId)->orderBy('sort_order')->get();
        $form = Form::findOrFail($formId);
        return view('admin.fields.index', compact('fields', 'form'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $formId = $request->query('form_id');
        $form = Form::findOrFail($formId);
        return view('admin.fields.create', compact('form'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'form_id' => 'required|exists:forms,id',
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,number,date,radio,select,textarea,email,tel',
            'placeholder' => 'nullable|string',
            'required' => 'boolean',
            'options' => 'nullable|array',
            'sort_order' => 'integer',
        ]);

        $maxSortOrder = FormField::where('form_id', $request->form_id)->max('sort_order') ?? 0;

        FormField::create([
            'form_id' => $request->form_id,
            'label' => $request->label,
            'type' => $request->type,
            'placeholder' => $request->placeholder,
            'required' => $request->required ?? false,
            'options' => $request->options ? json_encode($request->options) : null,
            'sort_order' => $request->sort_order ?? $maxSortOrder + 1,
        ]);

        return redirect()->route('admin.forms.fields.index', ['form_id' => $request->form_id])->with('success', 'Field created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $field = FormField::with('form')->findOrFail($id);
        return view('admin.fields.show', compact('field'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $field = FormField::findOrFail($id);
        return view('admin.fields.edit', compact('field'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,number,date,radio,select,textarea,email,tel',
            'placeholder' => 'nullable|string',
            'required' => 'boolean',
            'options' => 'nullable|array',
            'sort_order' => 'integer',
        ]);

        $field = FormField::findOrFail($id);
        $field->update([
            'label' => $request->label,
            'type' => $request->type,
            'placeholder' => $request->placeholder,
            'required' => $request->required ?? false,
            'options' => $request->options ? json_encode($request->options) : null,
            'sort_order' => $request->sort_order,
        ]);

        return redirect()->route('admin.forms.fields.index', ['form_id' => $field->form_id])->with('success', 'Field updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $field = FormField::findOrFail($id);
        $formId = $field->form_id;
        $field->delete();

        return redirect()->route('admin.forms.fields.index', ['form_id' => $formId])->with('success', 'Field deleted successfully.');
    }
}
