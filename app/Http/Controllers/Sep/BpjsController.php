<?php

namespace App\Http\Controllers\Sep;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use DB;
use GuzzleHttp\Psr7;
use Guzzle\Http\Message\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class BpjsController extends Controller
{
    protected $client = null;
    protected $api_url;

    public function __construct()
    {
        $this->client = new Client(['cookies' => true, 'verify' => false]);    
        $this->api_url = config('bpjs.api.endpoint');   
    }

    public function getRujukan(Request $req)
    {
        // return $req->rujukan;
        try {
            $url = $this->api_url . "rujukan/".$req->rujukan;
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        } 
    }

    public function getRujukanRS(Request $req)
    {
        //  dd($req->rujukan);
        try {
            $url = $this->api_url . "rujukan/rs/".$req->rujukan;
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        } 
    }

    public function listRujukan($req)
    {
        // dd($req->no_kartu);
        try {
            $url = $this->api_url . "rujukan/list/peserta/".$req->no_kartu;
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        } 
    }

    public function listRujukanRS($req)
    {
        // dd($req->no_kartu);
        try {
            $url = $this->api_url . "rujukan/rs/list/peserta/".$req->no_kartu;
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        } 
    }

    public function getPeserta(Request $req)
    {
        try { 
            $url = $this->api_url . "peserta/nokartu/".$req->no_kartu."/tglsep"."/".$req->tgl_sep;
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        }
    }

    public function getSep(Request $req)
    {
        // dd($req->sep);
        try { 
            $url = $this->api_url . "sep/".$req->sep;
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        }
    }

    public function getPpkRujukan(Request $req)
    {
        $jns_faskes = "1";
        $dataAwal = $this->ppkRujukan($req->ppk_rujukan, $jns_faskes);
        $rujukan = json_decode($dataAwal);
        // dd($rujukan);
        if ($rujukan->response == null) {
            $jns_faskes = "2";
            $data  = $this->ppkRujukan($req->ppk_rujukan, $jns_faskes);
            $rujukan = json_decode($data);
            $rujukan->response->faskes[0]->jenis_faskes = "2";
        } else {
            $rujukan->response->faskes[0]->jenis_faskes = "1";
            $rujukan = $rujukan;
        }
        return response()->json($rujukan);
    }

   

    public function ppkRujukan($kode, $jns)
    {
        try { 
            $url = $this->api_url . "referensi/faskes/".$kode."/".$jns;
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        }
    }

    public function getDiagnosa(Request $req)
    {
        if ($req->ajax()) {
            $kode = $req->get('term');
            $diagnosa = $this->Diagnosa($kode);
            $data = json_decode($diagnosa);
            $diagAwal = $data->response->diagnosa;
            return $diagAwal;
        }
      
    }

    public function getFaskes(Request $req)
    {
        if ($req->ajax()) {
            $kode = $req->all();
            $faskes = $this->Faskes($kode);
            $data = json_decode($faskes);
            // dd($data);
            if ($data->response != null) {
                $faskes = $data->response->faskses;
            } else {
                $faskes = [];
            }
            return $faskes;
        }
    }
    
    public function getDpjp(Request $req)
    {
        if ($req->ajax()) {
            $kode = $req->all();
            $dpjp = $this->Dpjp($kode);
            $data = json_decode($dpjp);
            if ($data->response != null) {
                $dpjp = $data->response->list;
            } else {
                $dpjp = [];
            }
            return $dpjp;
        }
    }

    public function getListDpjp(Request $req)
    {
        if ($req->ajax()) {
            $kode = $req->all();
            $dpjp = $this->Dpjp($kode);
            $data = json_decode($dpjp);
            if ($data->response != null) {
                $dpjp = $data->response->list;
                // dd($dpjp);
                $dokter="<option value='00000'>--Silahkan Pilih Dokter/Kota--</pilih>";
                foreach($dpjp as $d)
                {
                    $dokter.= "<option value='$d->kode'>$d->nama</option>";
                }
            } else {
                $dokter = [];
            }
            return $dokter;
        }
    }

    public function getPoli(Request $req)
    {
        if ($req->ajax()) {
            $kode = $req->get('term');
            $poli = $this->Poli($kode);
            $data = json_decode($poli);
            $poliTujuan = $data->response->poli;
            return $poliTujuan;
        }
    }

    public function getProvinsi()
    {
        $provinsi = $this->Provinsi();
        $data = json_decode($provinsi, true);
        // dd($data['response']['list']);
        $prov="<option value='0'>--Silahkan Pilih Provinsi--</pilih>";
        foreach($data['response']['list'] as $d)
        {
            $prov.= "<option value='$d[kode]'>$d[nama]</option>";
        }
        return $prov;
    }

    public function getListRujukan(Request $req)
    {
        // dd($req->all());
        if ($req->ajax()) {
            $no = 1;
            $rujukan = $this->listRujukan($req);
            $data = json_decode($rujukan);
            if ($data->response == null) {
                $query = [];
            } else {
                foreach($data->response->rujukan as $val) {
                    $query[] = [
                        'no' => $no++,
                        'noKunjungan' => '
                            <div class="btn-group">
                                <button data-rujukan="'.$val->noKunjungan.'" id="h-rujukan" class="btn btn-sencodary btn-xs btn-cus">'.$val->noKunjungan.'</button>
                            </div> ',
                        'tglKunjungan' => $val->tglKunjungan,
                        'noKartu' => $val->peserta->noKartu,
                        'nama' => $val->peserta->nama,
                        'ppkPerujuk' => $val->provPerujuk->kode,
                        'poli' => $val->poliRujukan->kode
                    ];
                }
            }
            $result = isset($query) ? ['data' => $query] : ['data' => 0];
            // dd($result);
            return json_encode($result);
        }
    }

    public function getListRujukanRS(Request $req)
    {
        if ($req->ajax()) {
            $no = 1;
            $rujukan = $this->listRujukanRS($req);
            $data = json_decode($rujukan);
            if ($data->response == null) {
                $query = [];
            } else {
                foreach($data->response->rujukan as $val) {
                    $query[] = [
                        'no' => $no++,
                        'noKunjungan' => '
                            <div class="btn-group">
                                <button data-rujukanrs="'.$val->noKunjungan.'" id="h-rujukan-rs" class="btn btn-sencodary btn-xs btn-cus">'.$val->noKunjungan.'</button>
                            </div> ',
                        'tglKunjungan' => $val->tglKunjungan,
                        'noKartu' => $val->peserta->noKartu,
                        'nama' => $val->peserta->nama,
                        'ppkPerujuk' => $val->provPerujuk->kode,
                        'poli' => $val->poliRujukan->kode
                    ];
                }
            }
            $result = isset($query) ? ['data' => $query] : ['data' => 0];
            // dd($result);
            return json_encode($result);
        }
    }

    public function getHistory(Request $req)
    {
        // dd($req->all());
        if ($req->ajax()) {
            $no = 1;
            $rujukan = $this->monHistory($req);
            $data = json_decode($rujukan);
            if ($data->response == null) {
                $query = [];
            } else {
                foreach($data->response->histori as $val) {
                    $query[] = [
                        'no' => $no++,
                        'noSep' => '
                            <div class="btn-group">
                                <button data-sep="'.$val->noSep.'" id="h-sep" class="btn btn-sencodary btn-xs btn-cus">'.$val->noSep.'</button>
                            </div> ',
                        'tglSep' => $val->tglSep,
                        'noKartu' => $val->noKartu,
                        'namaPeserta' => $val->namaPeserta,
                        'ppkPerujuk' => substr($val->ppkPelayanan, 0,10)
                    ];
                }
            }
            $result = isset($query) ? ['data' => $query] : ['data' => 0];
            // dd($result);
            return json_encode($result);
        }
    }

    public function getcekHistory(Request $req)
    {
        // dd($req->all());
        if ($req->ajax()) {
            $histori = $this->monHistory($req);
            $data = json_decode($histori, true);
            if ($data['response'] == null) {
                $result = [];
            } else {
                $result = $data['response']['histori'][0];
            }
            // dd($result);
            return response()->json($result);
        }
    }

    public function monHistory($req)
    {
        $tglAwal = date_format(date_sub(date_create($req->akhir), date_interval_create_from_date_string('30 days')), 'Y-m-d');
        try {
            $url = $this->api_url . "mon/hislayanan/nokartu/".$req->no_kartu."/"."tglawal/".$tglAwal."/"."tglakhir/".$req->akhir;
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        } 
    }
    public function getKabupaten(Request $req)
    {
        $kd_prov = $req->kd_prov;
        $kabupaten = $this->Kabupaten($kd_prov);
        $data = json_decode($kabupaten, true);
        $kab="<option value='0'>--Silahkan Pilih Kabupaten/Kota--</pilih>";
        foreach($data['response']['list'] as $d)
        {
            $kab.= "<option value='$d[kode]'>$d[nama]</option>";
        }
        return $kab;
    }

    public function getKecamatan(Request $req)
    {
        $kd_kab = $req->kd_kab;
        $kecamatan = $this->Kecamatan($kd_kab);
        $data = json_decode($kecamatan, true);
        // dd($data['response']);
        $kec="<option value='0'>--Silahkan Pilih Kecamatan/Kota--</pilih>";
        foreach($data['response']['list'] as $d)
        {
            $kec.= "<option value='$d[kode]'>$d[nama]</option>";
        }
        return $kec;
    }

    public function Diagnosa($kode)
    {
        try { 
            $url = $this->api_url . "referensi/diagnosa/".$kode;
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        }
    }

    public function Poli($kode)
    {
        try { 
            $url = $this->api_url . "referensi/poli/".$kode;
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        }
    }

    public function Faskes($kode)
    {
        try { 
            $url = $this->api_url . "referensi/faskes/".$kode['term']."/".$kode['asalRujukan'];
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        }
    }

    public function Dpjp($kode)
    {
        $tgl = date('Y-m-d');
        try { 
            $url = $this->api_url . "referensi/dokter/pelayanan/".$kode['jnsPel']."/tglpelayanan/".$tgl."/spesialis/".$kode['term'];
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        }
    }

    public function Provinsi()
    {
        try { 
            $url = $this->api_url . "referensi/provinsi";
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        }
    }

    public function Kabupaten($kd_prov)
    {
        try { 
            $url = $this->api_url . "referensi/kabupaten/provinsi/".$kd_prov;
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        }
    }

    public function Kecamatan($kd_kab)
    {
        try { 
            $url = $this->api_url . "referensi/kecamatan/kabupaten/".$kd_kab;
            $response = $this->client->get($url);
            $result = $response->getBody();
            return $result;
        } catch (RequestException $e) {
            $result = Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                $result = Psr7\str($e->getResponse());
            }
        }
    }
}
