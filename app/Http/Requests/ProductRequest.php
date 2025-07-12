<?php
namespace App\Http\Requests;

use App\Http\Services\HelperService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProductRequest extends FormRequest
{
    public function __construct(private HelperService $helper)
    {
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $data = [
            'store_type'  => 'required|in:ENTRY,FETCH',
            'category_id' => 'sometimes',
            'name'        => 'required_if:store_type,=,ENTRY',
            'barcode'     => 'required',
            'status'      => 'sometimes|boolean',
        ];

        if (request()->method() === "PATCH") {
            unset($data['store_type']);
            $data['name']    = 'sometimes|required';
            $data['barcode'] = 'sometimes|required';
        }

        return $data;
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->wantsJson() || $this->ajax()) {
            throw new HttpResponseException($this->helper->validateError($validator->errors()));
        }
        parent::failedValidation($validator);
    }
}
