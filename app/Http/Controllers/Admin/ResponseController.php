<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Response;
use App\Models\Store;
use App\Models\Form;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ResponsesExport;

class ResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Response::with(['form', 'store', 'answers.field']);

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $responses = $query->paginate(20);
        $stores = Store::all();

        return view('admin.responses.index', compact('responses', 'stores'));
    }

    /**
     * Export responses to Excel
     */
    public function export(Request $request)
    {
        $query = Response::with(['form', 'store', 'answers.field']);

        // Filters
        if ($request->filled('form_id')) {
            $query->where('form_id', $request->form_id);
        }
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->method() === 'GET') {
            $forms = Form::pluck('name', 'id')->toArray();
            $stores = Store::select('id', 'name', 'address')->get();
            return view('admin.responses.export', compact('forms', 'stores'));
        }

        // POST: Export
        $limit = $request->limit;
        $responses = $query->get();

        if ($limit && $limit !== 'all') {
            if ($limit === 'custom' && $request->filled('custom_limit')) {
                $limit = (int) $request->custom_limit;
            }
            $responses = $query->limit($limit)->get();
        }

        $filename = 'responses_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new ResponsesExport($responses), $filename);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $response = Response::with(['form', 'store', 'answers.field'])->findOrFail($id);
        return view('admin.responses.show', compact('response'));
    }

    // Other methods not needed for responses
}
