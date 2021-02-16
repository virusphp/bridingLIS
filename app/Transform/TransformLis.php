<?php

namespace App\Transform;

class TransformLis
{
    protected $pathFile = "storage/lis";

    public function mapLis($table)
    {
        // dd($table);
        $data["lis"] = [
                'no_rm'           => $table->no_rm,
                'no_reg'          => $table->no_reg,
                'no_lab'          => $table->no_lab,
                'file_hasil'      => asset($this->pathFile."/". $table->file_hasil),
                'user_verified'   => $table->user_verified, 
                'tgl_pemeriksaan' => formatTgl($table->tgl_pemeriksaan),
                'jam_pemeriksaan' => formatJam($table->tgl_pemeriksaan),
                'tgl_created'     => $table->tgl_created,
                'tgl_updated'     => $table->tgl_updated == null ? "-" : $table->tgl_updated,
        ];

        return $data;
    }

    public function mapDataLis($table)
    {
        foreach ($table as $value) {
            $data['lis'][] = [
                'no_rm'           => $value->no_rm,
                'no_reg'          => $value->no_reg,
                'no_lab'          => $value->no_lab,
                'file_hasil'      => asset($this->pathFile. "/". $value->file_hasil),
                'user_verified'   => $value->user_verified,
                'tgl_pemeriksaan' => formatTgl($value->tgl_pemeriksaan),
                'jam_pemeriksaan' => formatJam($value->tgl_pemeriksaan),
                'tgl_created'     => $value->tgl_created,
                'tgl_updated'     => $value->tgl_updated == null ? "-" : $value->tgl_updated,
            ];
        }

        return $data;
    }

}