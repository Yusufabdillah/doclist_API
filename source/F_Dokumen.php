<?php
/**
 * Created by PhpStorm.
 * User: Yusuf Abdillah Putra
 * Date: 17/01/2019
 * Time: 14:26
 */

use Slim\Http\Response;
use Slim\Http\Request;

class F_Dokumen extends Library {

    /**
     * @param $function
     * Tujuan : Digunakan untuk memanggil fungsi yang ada di kelas ini
     *          Konsep pemanggilannya diatur sesuai inputan url yang
     *          dimasukkan pengguna.
     * Eksekusi : permit_API/index.php
     *            $Run = new $__CLASS_API__($__FUNCTION_API__)
     *            $__CLASS_API__ : REQUEST_URI[2]
     *            $__FUNCTION_API__ : REQUEST_URI[3]
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     *
     * Cara memakai middleware(Pengecekan API Key) cukup tambahkan saja ->add(parent::middleware());
     */
    public function __construct($function)
    {
        parent::__construct();
        self::deklarasi($this->deklarasi);
        self::$function();
        return $this->app->run();
    }

    private function deklarasi($deklarasi)
    {
        $deklarasi['view'] = 'vw_mstdokumen';
        $deklarasi['tabel'] = 'tbl_mstdokumen';
        $deklarasi['pk'] = 'idDokumen';
    }

    protected function getAll() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $Fetch = $this->qb->table($this->view)->get();
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    private function getByDepartemen() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Fetch = $this->qb
                          ->table($this->view)
                          ->where('idDepartemen', $dataParsed['idDepartemen'])
                          ->get();
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    private function getDataPengajuanEdit() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Fetch = $this->qb
                ->table($this->view)
                ->where('idDepartemen', $dataParsed['idDepartemen'])
                ->where('se_ajuan_statusDokumen', true)
                ->get();
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }

    private function cekCaseNumber() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Fetch = $this->qb
                ->table($this->view)
                ->select('idDokumen', 'casenumberDokumen')
                ->where('casenumberDokumen', $dataParsed['casenumberDokumen'])
                ->get();
            if ($Fetch == '[]') {
                return $response->withJson('tidak_ada_yang_sama', 200);
            } else if ($Fetch !== '[]') {
                return $response->withJson('ada_yang_sama', 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        })->add(parent::middleware());
    }

    protected function getData() {
        $this->app->get($this->pattern.'/{VALUE_DATA}[/{KOLOM}]', function(Request $request, Response $response, $args) {
            $value_data = $args['VALUE_DATA'];
            if (empty($args['KOLOM'])) {
                $Fetch = $this->qb
                    ->table($this->view)
                    ->where($this->pk, $value_data)
                    ->first();
            } if (!empty($args['KOLOM'])) {
                $kolom = $args['KOLOM'];
                $Fetch = $this->qb
                    ->table($this->view)
                    ->where($kolom, $value_data)
                    ->orWhere($kolom, 'like', '%'.$value_data.'%')
                    ->first();
            }
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }



    //-------------------TAMBAHAN BARU TOHA-------------------------------
        private function getAllExpired() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            // $Fetch = $this->qb
            //     ->table($this->view)
            //     ->where('tglAwal','<>',0)
            //     ->where('tglAwal','=>','2019-02-28')
            //     ->get();
            $tanggal=date('Y-m-d');
            // $Query = "Select * from vw_mstdokumen where tglAwal != 0 and tglAwal <= NOW() and durasiReminder != '0' or (NOW() BETWEEN (tgl_habisDokumen - INTERVAL(90) day) AND tgl_habisDokumen )and tgl_habisDokumen is not null";
             $Query = "Select * from vw_mstdokumen where tglAwal is not null and tgl_habisDokumen is not null and ((awalReminder !=0 and tglAwal <= NOW()) or(awalReminder =0 and tglAwalDefault <= NOW()))";
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        });
    }

    private function getByDepartemenExpired() {
        $this->app->get($this->pattern, function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $tanggal=date('Y-m-d');
            // $Fetch = $this->qb
            //               ->table($this->view)
            //               ->where('idDepartemen', $dataParsed['idDepartemen'])
            //               ->get();
            // $Query = "Select * from vw_mstdokumen where tglAwal != 0 and (tglAwal >= '$tanggal' or tglAwalDefault >= '$tanggal') and durasiReminder != '0' and idDepartemen=$dataParsed[idDepartemen] or (NOW() BETWEEN (tgl_habisDokumen - INTERVAL(90) day) AND tgl_habisDokumen and idDepartemen=$dataParsed[idDepartemen]) and tgl_habisDokumen is not null";
            $Query = "Select * from vw_mstdokumen where tglAwal is not null and tgl_habisDokumen is not null and ((awalReminder !=0 and tglAwal <= NOW()) or(awalReminder =0 and tglAwalDefault <= NOW())) and idDepartemen = $dataParsed[idDepartemen]";
            $Fetch = $this->db->query($Query)->fetchAll(PDO::FETCH_OBJ);
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        });
    }

    protected function getDataExpired() {
        // $tanggal=date('Y-m-d');
        $this->app->get($this->pattern.'/{VALUE_DATA}[/{KOLOM}]', function(Request $request, Response $response, $args) {
            $value_data = $args['VALUE_DATA'];
            if (empty($args['KOLOM'])) {
                $Fetch = $this->qb
                    ->table($this->view)
                    ->where($this->pk, $value_data)
                    ->first();
            } if (!empty($args['KOLOM'])) {
                $kolom = $args['KOLOM'];
                $Fetch = $this->qb
                    ->table($this->view)
                    ->where($kolom, $value_data)
                    ->orWhere($kolom, 'like', '%'.$value_data.'%')
                    ->first();
            }
            if ($Fetch) {
                return $response->withJson($Fetch, 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        })->add(parent::middleware());
    }


    protected function post() {
        $this->app->post($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Post = $this->qb
                ->table($this->tabel)
                ->insertGetId($dataParsed);
            if ($Post) {
                return $response->withJson(["status" => "success", 'idDokumen' => $Post], 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        });
    }

    protected function put() {
        $this->app->put($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            /**
             * Recovery data yang di set null
             */
            foreach ($dataParsed as $Key => $data) {
                if ($data == 'NULL') {
                    $dataParsed[$Key] = null;
                }
            }

            $Update = $this->qb
                ->table($this->tabel)
                ->where($this->pk, $dataParsed[$this->pk])
                ->update($dataParsed);
            if ($Update) {
                return $response->withJson(["status" => "success"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 200);
            }
        });
    }

    protected function delete() {
        $this->app->delete($this->pattern.'/', function(Request $request, Response $response) {
            $dataParsed = $request->getParsedBody();
            $Delete = $this->qb
                ->table($this->tabel)
                ->where($this->pk, $dataParsed[$this->pk])
                ->delete();
            if ($Delete) {
                return $response->withJson(["status" => "success"], 200);
            } else {
                return $response->withJson(["status" => "failed"], 500);
            }
        });
    }

}