<?php

namespace App\Validation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ValidationCreateLis
{
    public function rules($request)
    {
        return Validator::make($request->all(),[
            'tgl_pemeriksaan' => 'required|date',
            // 'no_reg'          => [
            //     'required',
            //     Rule::unique('hasil_pemeriksaan_laborat_file')->where(function($query) use ($request) {
            //         return $query->where('no_rm', $request['no_rm'])
            //                     ->where('no_lab', $request['no_lab']);
            //     }),
            // ],
            // 'no_rm'          => 'required|min:6|max:7',
            // 'no_lab'          => 'required|max:13|unique:hasil_pemeriksaan_laborat_file,no_lab,NULL,no_id,no_reg,'.$request['no_lab'],
            'file_hasil'      => 'required|mimes:pdf',
            'user_verified'   => 'required',
        ],[
            'required' => ':attribute Tidak boleh kosong atau NULL!',
            'date'     => ':attribute Tidak sesuai tanggal NASIONAl! atau Tidak Valid',
            'dokumen'  => 'Format :attribute tidak valid!!',
            'mimes'    => 'Format :attribute tidak sesuai (format pdf)',
            'unique'    => 'No Reg dan No Lab Tidak boleh sama'
        ]);
    }

    public function messages($errors)
    {
        $error = [];
        foreach($errors->getMessages() as $key => $value)
        {
                $error[$key] = $value[0];
        }
        return $error;
        
    }

    // protected function getValidatorInstance()
	// {
    //     $validator = FormRequest::getValidatorInstance();

    //     $validator->addImplicitExtension('dokumen', function($attribute, $value, $parameters) {
    //         if($value) {
    //             return in_array($value->getClientOriginalExtension(), ['xls', 'xlsx', 'doc', 'docx', 'pdf', 'PDF']);
    //         }
            
    //         return false;
    //     });

    //     return $validator;
	// }

}