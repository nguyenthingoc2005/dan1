<?php
$act = $_GET['act'] ?? '/';
 match ($act) {
            'admin' => (new AdminController())->index(),
            'addtour' => (new AdminController())->formaddtour(),
            'createtour' => (new AdminController())->createtour(),
            'uppdatetour' => (new AdminController())->uppdatetour($_GET['tour_id'] ?? null),
            'uppdatetour1' => (new AdminController())->uppdatetour1($_GET['tour_id'] ?? null),
            'deletetour' => (new AdminController())->deletetour($_GET['tour_id'] ?? null),
            'diadiem' => (new AdminController())->diadiem($_GET['tour_id'] ?? null),
            'gan_diadiem' => (new AdminController())->gan_diadiem($_GET['tour_id'] ?? null),
            'luu_gan_diadiem' => (new AdminController())->luu_gan_diadiem($_GET['tour_id'] ?? null),
            'xoa_diadiem' => (new AdminController())->xoa_diadiem_tour( $_GET['dia_diem_tour_id'] ?? null),
            'sua_diadiemtour'=> (new AdminController())->sua_diadiem_tour($_GET['dia_diem_tour_id'] ?? null, $_GET['tour_id'] ?? null),
            'capnhat_diadiem_tour' => (new AdminController())->capnhat_diadiem_tour($_GET['dia_diem_tour_id'] ?? null),
            default => (new AdminController())->index(),
        };
