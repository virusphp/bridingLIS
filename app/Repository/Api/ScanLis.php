<?php

namespace App\Repository\Api;

use Carbon\Carbon;
use DB;
use Exception;
use File;
use Illuminate\Support\Facades\Storage;

class ScanLis extends ApiRepository
{
    public function getAll()
    {
        $data = DB::table('hasil_pemeriksaan_laboran_file')->get();
        if ($data->count() != 0) {
            $result = $data;
        } else {
            $result = ['kode' => 201, 'pesan' => 'Data belum ada'];
        }
        return  $result;
    }

    public function getChartKlaim($request)
    {
        // dd($request->all());
        $data = DB::table('hasil_pemeriksaan_laborat_file')->select('jns_pelayanan','tgl_sep', 'tgl_pulang')
                    ->where(function($query) use ($request){
                        $year = $request->has('tahun') ? $request->tahun : date('Y');
                        $query->orWhere('jns_pelayanan', $request->pelayanan);
                        $query->whereYear('tgl_pulang', $year);
                    })
                    ->orderBy('tgl_pulang')
                    ->get()
                    ->groupBy(function($date) {
                        return Carbon::parse($date->tgl_pulang)->format('M');
                    })
                    ->map(function($item) {
                        return count($item);
                    });
        return $data;
    }

    public function getKlaim($noSep)
    {
        $data = DB::table('hasil_pemeriksaan_laboran_file')->where('no_sep', $noSep)->first();
        // dd($data);
        if ($data) {
            if (storage::exists('public'. DIRECTORY_SEPARATOR .$this->getDestination($data->tgl_sep) . $data->file_claim)) {
                $data->file_claim = $this->getFile($data->tgl_sep) . $data->file_claim;
            } else {
                $data->file_claim = $this->getFile($data->tgl_pulang) . $data->file_claim;
            }
            // if (file_exists($this->getFile($data->tgl_sep) . $data->file_claim)) {
            // } else {
                // $data->file_claim = $this->getFile($data->tgl_pulang) . $data->file_claim;
            // }
            $meta = ["kode" => 200, "pesan" => "Sukses"];
            $response = $this->remap($meta, $data);
        } else {
            $meta = ["kode" => 201, "pesan" => "Data tidak ditemukan!!!"];
            $response = $this->remap($meta, null);
        }

        return $response;
    }

    public function getLis($noRm)
    {
        return DB::table('hasil_pemeriksaan_laborat_file')->where('no_rm', $noRm)->get();
    }

    public function simpanLis($request)
    {
        try {
            $data = $this->handleFile($request);

            $simpan = DB::table('hasil_pemeriksaan_laborat_file')
                ->insert([
                    'tgl_pemeriksaan' => $data['tgl_pemeriksaan'],
                    'no_rm'           => $data['no_rm'],
                    'no_reg'          => $data['no_reg'],
                    'no_lab'          => $data['no_lab'],
                    'file_hasil'      => $data['file_hasil'],
                    'user_verified'   => $data['user_verified'],
                    'tgl_created'     => date('Y-m-d')
                ]);

            if (!$simpan) {
                return response()->jsonApi(500, "Error Transaction", "Error proses insert data!");
            }
    
            $dataScan = DB::table('hasil_pemeriksaan_laborat_file as lab')
                            ->select('lab.tgl_pemeriksaan','lab.no_reg','lab.no_rm','lab.no_lab','lab.file_hasil','lab.user_verified', 'lab.tgl_created','lab.tgl_updated','p.nama_pasien','pg.nama_pegawai')
                            ->join('DBSIMRS.dbo.pasien as p', 'lab.no_rm', '=', 'p.no_rm')
                            ->join('DBSIMRS.dbo.pegawai as pg', 'lab.user_verified', '=', 'pg.kd_pegawai')
                            ->where([
                                ['no_reg', $data['no_reg']],
                                ['no_lab', $data['no_lab']]
                            ])
                            ->first();

            if ($dataScan) {
                $result = $dataScan;
                $this->sendMessage($dataScan, "Insert");
            }
            return $result;

        }  catch(Exception $e) {
            return $e->getMessage();
        }
      
    }

    public function getDataLis($noReg, $noLab)
    {
        return DB::table('hasil_pemeriksaan_laborat_file as lab')
            ->select('lab.tgl_pemeriksaan','lab.no_reg','lab.no_rm','lab.no_lab','lab.file_hasil','lab.user_verified', 'lab.tgl_created','lab.tgl_updated','p.nama_pasien','pg.nama_pegawai')
            ->join('DBSIMRS.dbo.pasien as p', 'lab.no_rm', '=', 'p.no_rm')
            ->join('DBSIMRS.dbo.pegawai as pg', 'lab.user_verified', '=', 'pg.kd_pegawai')
            ->where([
                ['no_reg', $noReg],
                ['no_lab', $noLab]
            ])
            ->first();
    }

    public function editLis($noReg, $noLab)
    {
        return DB::table('hasil_pemeriksaan_laborat_file')
            ->select('no_reg','no_lab','file_hasil')
            ->where([
                ['no_reg', $noReg],
                ['no_lab', $noLab]
            ])->first();
    }

    public function updateLis($request, $noReg, $noLab)
    {
        try {
            $data = $this->handleFile($request);

            $update = DB::table('hasil_pemeriksaan_laborat_file')
                ->where([
                    ['no_reg',$noReg],
                    ['no_lab',$noLab]
                ])
                ->update([
                    'tgl_pemeriksaan' => $data['tgl_pemeriksaan'],
                    'no_rm'           => $data['no_rm'],
                    'file_hasil'      => $data['file_hasil'],
                    'user_verified'   => $data['user_verified'],
                    'tgl_updated'     => date('Y-m-d')
                ]);

            if (!$update) {
                return response()->jsonApi(500, "Error Transaction", "Error proses insert data!");  
            }

            $dataScan = DB::table('hasil_pemeriksaan_laborat_file')
                            ->select('tgl_pemeriksaan','no_reg','no_rm','no_lab','file_hasil','user_verified', 'tgl_created','tgl_updated')
                            ->where([
                                ['no_reg', $data['no_reg']],
                                ['no_lab', $data['no_lab']]
                            ])
                            ->first();
    
            return $dataScan;
        } catch (Exception $e) {
            return $e->getMessage();
        }

    }

    public function handleFile($request)
    {
        $data = $request->all();

        if ($request->hasFile('file_hasil')) {
            $file =  $request->file('file_hasil');
            $extensi = $file->getClientOriginalExtension();
            $formatName = str_replace(' ', '_', $data['no_reg'] . ' ' . $data['no_lab'] .' '. $data['no_rm'] . '.' . $extensi);
            $pathFile = $this->getDestination() . $formatName;

            Storage::disk('public')->put($pathFile, File::get($file));

            $data['file_hasil'] = $formatName;
        }

        return $data;
    }

    public function getDestination()
    {
        return 'lis' . DIRECTORY_SEPARATOR;
    }
   
    public function delete($request)
    {
        $data = DB::table('sep_claim')->where('no_reg', $request->no_reg)->first();

        if (!empty($data->file_claim)) {
            $this->deleteFile($data);
        }

        $delete = DB::table('sep_claim')->where('no_reg', $request->no_reg)->delete();

        return $this->message($delete, "delete");
    }

    public function deleteLis($noReg, $noLab)
    {
        $data = DB::table('hasil_pemeriksaan_laborat_file')->where([
            ['no_reg', '=', $noReg],
            ['no_lab', '=', $noLab]
        ])->first();

        if (!empty($data->file_hasil)) {
            $this->deleteFile($data);
        }
        
        return DB::table('hasil_pemeriksaan_laborat_file')->where([
            ['no_reg', $noReg],
            ['no_lab', $noLab]
        ])->delete();
    }

    public function deleteFile($data)
    {
        $path = storage_path(). DIRECTORY_SEPARATOR. "app". DIRECTORY_SEPARATOR. "public". DIRECTORY_SEPARATOR. $this->getDestination() .  $data->file_hasil;
        // $path =  'public'. DIRECTORY_SEPARATOR. $this->getDestination() .  $data->file_hasil;
        
        return File::delete($path);
    }
    
  
    
}
