<?php
    class AdminController{
        public $modelGet;
        public $modelCreate;
        public $modelDelete;
        public $modelUpdate;
        public function __construct()
        {
            $this->modelGet = new getDataModule();
            $this->modelCreate = new creatDataModule();
            $this->modelDelete = new deleteDataModule();
            $this->modelUpdate = new uppDateDataModuleDataModule();
            
        }
        public function index(){
            $data1= $this->modelGet->getAllTours();
            require_once  './views/admin/tourlist.php';
        }
        public function formaddtour(){
            $data= $this->modelGet->getAllDanh_muc_tour();
            require_once './views/admin/tourcreat.php';
        }
       public function createtour(){
    $ten = $_POST['ten'];
    $danh_muc_id = $_POST['danh_muc_id'];
    $mo_ta_ngan = $_POST['mo_ta_ngan'];
    $mo_ta = $_POST['mo_ta'];
    $gia = $_POST['gia_co_ban'];
    $thoi_luong = $_POST['thoi_luong_mac_dinh'];
    $diem_khoi_hanh = $_POST['diem_khoi_hanh'];
    $hoat_dong = $_POST['hoat_dong'];

    $data = [
        'ten' => $ten,
        'danh_muc_id' => $danh_muc_id,
        'mo_ta_ngan' => $mo_ta_ngan,
        'mo_ta' => $mo_ta,
        'gia_co_ban' => $gia,
        'thoi_luong_mac_dinh' => $thoi_luong,
        'diem_khoi_hanh' => $diem_khoi_hanh,
        'hoat_dong' => $hoat_dong
    ];

    $this->modelCreate->createTour($data);
    header('Location: '.BASEURL.'?act=admin');
}
        public function deletetour($tour_id){
            if ($tour_id !== null) {
                $this->modelDelete->deleteTour($tour_id);
            }
            header('Location: '.BASEURL.'?act=admin');
        }
        public function uppdatetour($tour_id){
            $data= $this->modelGet->getAllDanh_muc_tour();
            $tour= $this->modelGet->getTourById($tour_id);
            require_once './views/admin/tourupdate.php';
        }
        public function uppdatetour1($tour_id){
            $ten = $_POST['ten'];
            $danh_muc_id = $_POST['danh_muc_id'];
            $mo_ta_ngan = $_POST['mo_ta_ngan'];
            $mo_ta = $_POST['mo_ta'];
            $gia = $_POST['gia_co_ban'];
            $thoi_luong = $_POST['thoi_luong_mac_dinh'];
            $diem_khoi_hanh = $_POST['diem_khoi_hanh'];
            $hoat_dong = $_POST['hoat_dong'];
        
            $data = [
                'ten' => $ten,
                'danh_muc_id' => $danh_muc_id,
                'mo_ta_ngan' => $mo_ta_ngan,
                'mo_ta' => $mo_ta,
                'gia_co_ban' => $gia,
                'thoi_luong_mac_dinh' => $thoi_luong,
                'diem_khoi_hanh' => $diem_khoi_hanh,
                'hoat_dong' => $hoat_dong
            ];
        
            $this->modelUpdate->uppDateTour($tour_id, $data);
            header('Location: '.BASEURL.'?act=admin');
        }
        public function diadiem($tour_id){
           $diadiemList= $this->modelGet->getDiaDiemByTourId($tour_id);
              require_once './views/admin/diadiemtour.php';
        }
        public function gan_diadiem($tour_id){
            $data=$this->modelGet->getAllDiaDiem();
            require_once './views/admin/gan_diadiemtour.php';
        }
        public function luu_gan_diadiem($tour_id){
            $dia_diem_id = $_POST['dia_diem_id'];
            $ghi_chu = $_POST['ghi_chu'] ?? null;
            $this->modelCreate->ganDiaDiemChoTour($tour_id, $dia_diem_id, $ghi_chu);
            header('Location: '.BASEURL.'?act=diadiem&tour_id='.$tour_id);
        }
        public function xoa_diadiem_tour( $dia_diem_tour_id){
            if ( $dia_diem_tour_id !== null) {
                $this->modelDelete->xoaDiaDiemKhoiTour( $dia_diem_tour_id);
            }
            header('Location: '.BASEURL.'?act=diadiem&tour_id='.$_GET['tour_id']);
        }
        public function sua_diadiem_tour($dia_diem_tour_id, $tour_id){
            $info= $this->modelGet->getDiaDiemTourById($dia_diem_tour_id);
            $data=$this->modelGet->getAllDiaDiem();

            require_once './views/admin/suadiadiemtour.php';
    }
        public function capnhat_diadiem_tour($dia_diem_tour_id){
            $ghi_chu = $_POST['ghi_chu'] ?? null;
            $dia_diem_id = $_POST['dia_diem_id'];
            $this->modelUpdate->capNhatDiaDiemTour($dia_diem_tour_id, $dia_diem_id, $ghi_chu);
            header('Location: '.BASEURL.'?act=diadiem&tour_id='.$_POST['tour_id']);
        }

}
?>