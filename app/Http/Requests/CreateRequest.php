<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
       return [
            'tgl_pemeriksaan' => 'required|date',
            'no_reg'     => 'required|min:12,max:13',
            'no_rm'      => 'required',
            'no_lab'     => 'required',
            'file_hasil'    => 'required|dokumen',
            'user_verified' => 'required'

        ];

    }

    public function messages()
    {
        return [
            'no_reg.required' => 'No Reg harus di isi!',
            'no_rm.required' => 'No RM harus di isi!',
            'no_sep.required' => 'No SEP Harus di isi!',
            'tgl_pemeriksaan.required' => 'Tanggal SEP harus di isi!!',
            'file_hasil.dokumen' => 'File tidak sesuai dengan aturan format (PDF,pdf)!'
		];
    }

    protected function getValidatorInstance()
	{
        $validator = parent::getValidatorInstance();

        $validator->addImplicitExtension('dokumen', function($attribute, $value, $parameters) {
            if($value) {
                return in_array($value->getClientOriginalExtension(), ['xls', 'xlsx', 'doc', 'docx', 'pdf', 'PDF']);
            }
            
            return false;
        });

        return $validator;
	}
}
