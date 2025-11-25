<?php 
// Require toàn bộ các file khai báo môi trường, thực thi,...(không require view)


session_start();
const BASEURL='http://localhost/DAN1/';
require_once './commons/env.php';
require_once './commons/function.php';

//Controller
require_once './controllers/AdminController.php';
require_once './controllers/HdvController.php';


//model
require_once './models/creatDataModule.php';
require_once './models/uppDateDataModule.php';
require_once './models/deleteDataModule.php';
require_once './models/getDataModule.php';

// // Route

// require_once './route/admin.php';

// if(isset($_SESSION['user'])&&$_SESSION['user']['vai_tro_id']==1){
//     require_once './route/admin.php';
// }
// else{
//     require_once './route/hdv.php';
// }


require_once './route/admin.php';

