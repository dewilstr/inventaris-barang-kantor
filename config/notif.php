<?php
header('Content-Type: application/json');
session_start();

$con = mysqli_connect("localhost", "root", "", "inventaris_barang_kantor");
if (mysqli_connect_errno()) {
    echo json_encode([]);
    exit;
}

// CEK LOGIN
if (!isset($_SESSION['username'])) {
    echo json_encode([]);
    exit;
}

$notif = [];
$role  = strtolower($_SESSION['level_user'] ?? "");


/* ==========================================================
   1. NOTIFIKASI STOK MENIPIS  (PASTI PAKAI TABLE: table_barang)
   ========================================================== */

$q1 = mysqli_query($con, "
    SELECT nama_barang, stok_barang 
    FROM table_barang
    WHERE stok_barang < 10
    ORDER BY stok_barang ASC
");

if ($q1) {
    while ($r = mysqli_fetch_assoc($q1)) {
        $notif[] = [
            "pesan"  => "Stok menipis: {$r['nama_barang']}",
            "detail" => "Sisa {$r['stok_barang']} pcs"
        ];
    }
}


/* ==========================================================
   2. NOTIFIKASI TRANSAKSI (PASTI ADA TABLE: table_notifikasi)
   ========================================================== */

$q2 = mysqli_query($con, "
    SELECT pesan, tanggal, status
    FROM table_notifikasi
    ORDER BY id_notif DESC
    LIMIT 10
");

if ($q2) {
    while ($t = mysqli_fetch_assoc($q2)) {
        $notif[] = [
            "pesan"  => $t['pesan'],
            "detail" => $t['tanggal'],
            "status" => $t['status']
        ];
    }
}

echo json_encode($notif);
