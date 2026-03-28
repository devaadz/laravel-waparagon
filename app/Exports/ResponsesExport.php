<?php

namespace App\Exports;

use App\Models\Response;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResponsesExport implements FromCollection, WithHeadings
{
    protected $responses;

    public function __construct($responses)
    {
        $this->responses = $responses;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->responses as $response) {
            $row = [
                'Email' => $response->email,
                'Store' => $response->store->name,
                'Form' => $response->form->name,
                'Submitted At' => $response->created_at->format('Y-m-d H:i:s'),
            ];

            // Add dynamic field answers
            foreach ($response->answers as $answer) {
                $row[$answer->field->label] = $answer->value;
            }

            $data[] = $row;
        }

        return collect($data);
    }

    public function headings(): array
    {
        if ($this->responses->isEmpty()) {
            return ['Email', 'Store', 'Form', 'Submitted At'];
        }

        $headings = ['Email', 'Store', 'Form', 'Submitted At'];

        // Get all unique field labels
        $fieldLabels = [];
        foreach ($this->responses as $response) {
            foreach ($response->answers as $answer) {
                $fieldLabels[$answer->field->label] = true;
            }
        }

        $headings = array_merge($headings, array_keys($fieldLabels));

        return $headings;
    }
}
