<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
	{
		return [
			'category'   => ['nullable', 'integer', 'exists:categories,id'],
			'year_from'  => ['nullable', 'integer', 'digits:4'],
			'year_to'    => ['nullable', 'integer', 'digits:4'],
			'q'          => ['nullable', 'string', 'max:100'],
			'sort'       => ['nullable', 'in:year_asc,year_desc,name_asc,name_desc,created_desc'],
		];
	}
}
