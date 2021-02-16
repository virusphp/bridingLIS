<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repository\Api\ScanLis;
use App\Transform\TransformLis;
use App\Validation\ValidationCreateLis;
use App\Validation\ValidationUpdateLis;

class LisController extends Controller
{
    protected $lis;
    protected $transform;

    public function __construct()
    {
        $this->lis = new ScanLis;
        $this->transform = new TransformLis;
    }

    public function getLis($noRm)
    {
        $dataLis = $this->lis->getDataLis($noRm);

        if ($dataLis->count() == 0) {
           $message = "Data LIS tidak di temukan!"; 
           return response()->jsonApi(201, $message);
        }

        $transform = $this->transform->mapDataLis($dataLis);
        return response()->jsonApi(200, "OK", $transform);
    }

    public function create(Request $r, ValidationCreateLis $valid)
    {
        $validate = $valid->rules($r);

        if ($validate->fails()) {
            $message = $valid->messages($validate->errors());
            return response()->jsonApi(422, implode(",",$message));    
        }

        $dataScan = $this->lis->simpanLis($r);

        if (!$dataScan) {   
            $message = "Gagal Menyimpan data, Error Server hubungi administrator!";
            return response()->jsonApi(500, $message);
        }

        $transform = $this->transform->mapLis($dataScan);
        return response()->jsonApi(200, "Sukses Simpana data!", $transform);

    }

    public function update(Request $r, ValidationUpdateLis $valid, $noReg, $noLab)
    {
        $validate = $valid->rules($r);
      
        if ($validate->fails()) {
            $message = $valid->messages($validate->errors());
            return response()->jsonApi(422, implode(",",$message));    
        }

        $editLis = $this->lis->editLis($noReg, $noLab);

        $oldFile = $editLis->file_hasil;
        $newFile = $r->file_hasil->getClientOriginalName();

        if ($oldFile !== $newFile) {
           $this->lis->deleteFile($editLis);
        }

        if (!$editLis) {
            $message = "Data Lis yang tidak di temukan!";
            return response()->jsonApi(201, $message);
        }

        $updateLis = $this->lis->updateLis($r, $noReg, $noLab);

        if (!$updateLis) {
            $message = "Gagal Update data, Error Server hubungi administrator!";
            return response()->jsonApi(500, $message);
        }

        $transform = $this->transform->mapLis($updateLis);
        return response()->jsonApi(200, "Sukses Update data!", $transform);
    }

    public function delete(Request $request, $noReg, $noLab)
    {
        $deleteLis = $this->lis->deleteLis($noReg, $noLab);

        if (!$deleteLis) {
            return response()->jsonApi(500, "Gagal Delete, Error Server hubungi administrator!");
        }

        return response()->jsonApi(200, "Sukses Delete data!");
    }
}
