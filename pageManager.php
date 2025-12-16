<?php
// ======================================
// FIX HEADER ERROR + SESSION SAFETY
// ======================================

// Tahan semua output supaya header() aman
ob_start();

// Mulai session hanya jika belum jalan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load controller
include "config/controller.php";
$function = new lsp();

// Jika belum login → redirect
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location:index.php");
    exit;
}

// Ambil data user
$auth = $function->AuthUser($_SESSION['username']);

// Logout handler
if (isset($_GET['logout'])) {
    $function->logout(); // sudah ada header + exit di dalamnya
    exit;
}

// Cek session valid
if ($function->sessionCheck() === "false") {
    header("Location:index.php");
    exit;
}
$role = $_SESSION['level_user'] ?? '';

if ($role == "admin") {
    $profileLink = "pageAdmin.php?page=profile";
} elseif ($role == "manager") {
    $profileLink = "pageManager.php?page=profile";
} elseif ($role == "kasir") {
    $profileLink = "pageKasir.php?page=profile";
} else {
    $profileLink = "index.php";
}


?>

<!DOCTYPE html>
<html>
<head>
	<!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
	<title>Manager</title>
	<!-- Fontfaces CSS-->
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
    <link href="vendor/vector-map/jqvmap.min.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="css/sweet-alert.css">

    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
</head>
<body>

	<div class="page-wrapper">
		<aside class="menu-sidebar2">
			<div class="logo">
                <a href="#">
                    <img src="images/icon/logo-white.png" alt="Cool Admin" />
                </a>
            </div>
            <div class="menu-sidebar2__content js-scrollbar1">
                <div class="account2">
                    <div class="image img-cir img-120">
                        <img src="img/<?= $auth['foto_user'] ?>" />
                    </div>
                    <h4 class="name"><?= $auth['nama_user']; ?></h4>
                </div>
                <nav class="navbar-sidebar2">
                    <ul class="list-unstyled navbar__list">
                        <li>
                            <a href="?page">
                            <i class="fas fa-tachometer-alt"></i>Dashboard</a>
                        </li>
                        <li>
                            <a href="?page=kelPegawai">
                            <i class="fas fa-users"></i>Kelola Pegawai</a>
                        </li>
                        <li>
                            <a href="?page=kelTransaksi">
                            <i class="fas fa-shopping-basket"></i>Kelola Transaksi</a>
                        </li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                            <i class="fas fa-archive"></i>Data Barang</a>
                            <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">
                                <li>
                                    <a href="?page=kelBarang">Semua Barang</a>
                                </li>
                                <li>
                                    <a href="?page=periode">Lihat Barang per Periode</a>
                                </li>
                                <li>
                                    <a href="?page=barangHabis">Barang Habis</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
		</aside>

<div class="page-container2">
    <header class="header-desktop2">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="header-wrap2">

                    <!-- LOGO MOBILE -->
                    <div class="logo d-block d-lg-none">
                        <a href="#">
                            <img src="images/icon/logo-white.png" alt="CoolAdmin" />
                        </a>
                    </div>

                    <!-- HEADER BUTTON WRAPPER -->
                    <div class="header-button2">

                        <!-- SEARCH -->
                        <div class="header-button-item js-item-menu">
                            <i class="zmdi zmdi-search"></i>
                            <div class="search-dropdown js-dropdown">
                                <form action="">
                                    <input class="au-input au-input--full au-input--h65"
                                           type="text"
                                           placeholder="Search for datas &amp; reports..." />
                                    <span class="search-dropdown__icon">
                                        <i class="zmdi zmdi-search"></i>
                                    </span>
                                </form>
                            </div>
                        </div>

                        <!-- NOTIFIKASI AJAX -->
                            <div class="header-button-item js-item-menu" id="notifBell">
                            <i class="zmdi zmdi-notifications"></i>

                            <div class="notifi-dropdown js-dropdown" id="notifDropdown">
                                <div class="notifi__title">
                                    <p>Notifications</p>
                                </div>

                                <!-- AJAX CONTENT -->
                                <div id="notifContent"></div>
                            </div>
                        </div>

                        <!-- HAMBURGER MENU (INI YANG HILANG) -->
                        <div class="header-button-item mr-0 js-sidebar-btn">
                            <i class="zmdi zmdi-menu"></i>
                        </div>

                        <!-- ACCOUNT MENU -->
                        <div class="setting-menu js-right-sidebar d-none d-lg-block">
                            <div class="account-dropdown__body">

                                <div class="account-dropdown__item">
                                    <a href="?page=profile">
                                        <i class="zmdi zmdi-account"></i>Account
                                    </a>
                                </div>

                                <div class="account-dropdown__item">
                               <a href="<?= $profileLink ?>">
                                         <i class="zmdi zmdi-settings"></i>Setting
                                      </a>
                               </div>
                                <div class="account-dropdown__item">
                                    <a href="homepage.php?logout" id="forLogout">
                                        <i class="zmdi zmdi-power"></i>Logout
                                    </a>
                                </div>

                            </div>
                        </div>

                    </div> <!-- END header-button2 -->

                </div> <!-- END header-wrap2 -->
            </div>
        </div>
    </header>
            <aside class="menu-sidebar2 js-right-sidebar d-block d-lg-none">
                <div class="logo">
                    <a href="#">
                        <img src="images/icon/logo-white.png" alt="Cool Admin" />
                    </a>
                </div>
                <div class="menu-sidebar2__content js-scrollbar2">
                    <div class="account2">
                        <div class="image img-cir img-120">
                            <img src="img/<?= $auth['foto_user'] ?>" alt="John Doe" />
                        </div>
                        <h4 class="name"><?= $auth['nama_user'] ?></h4>
                        <a href="#">Sign out</a>
                    </div>
                    <nav class="navbar-sidebar2">
                        <ul class="list-unstyled navbar__list">
                        	<li>
                            	<a href="?page">
                                <i class="fas fa-tachometer-alt"></i>Dashboard</a>
                        	</li>
                        	<li>
	                            <a href="?page=kelPegawai">
	                            <i class="fas fa-users"></i>Kelola Pegawai</a>
                        	</li>
                            <li>
                                <a href="?page=kelTransaksi">
                                <i class="fas fa-shopping-basket"></i>Kelola Transaksi</a>
                            </li>
                            <li class="has-sub">
                                <a class="js-arrow" href="#">
                                    <i class="fas fa-tachometer-alt"></i>Data Barang</a>
                                <ul class="navbar-mobile-sub__list list-unstyled js-sub-list">
                                    <li>
                                        <a href="?page=kelBarang">Semua Barang</a>
                                    </li>
                                    <li>
                                        <a href="?page=periode">Lihat Barang per Periode</a>
                                    </li>
                                    <li>
                                        <a href="?page=barangHabis">Barang Habis</a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </aside>

			<?php 

				@$page = $_GET['page'];
				switch($page){
					case 'kelPegawai':
						include "manager/kelolaPegawai.php";
						break;
                    case 'profile':
                        include "profile.php";
                        break;
                    case 'kelBarang':
                        include "manager/viewManagerBarang.php";
                        break;
                    case 'viewBarangDetail':
                        include "manager/viewBarangDetail.php";
                        break;
                    case 'periode':
                        include "manager/BarangPeriode.php";
                        break;
                    case 'barangHabis':
                        include "manager/BarangHabis.php";
                        break;
                    case 'kelTransaksi':
                        include "manager/Transaksi.php";
                        break;
					default:
						$page = "dashboard";
						include "manager/dashboard.php";
						break;
				}

			 ?>

		</div>

	</div>

	<!-- Jquery JS-->
    <script src="vendor/jquery-3.2.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="vendor/slick/slick.min.js">
    </script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="vendor/circle-progress/circle-progress.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="vendor/select2/select2.min.js">
    </script>
    <script src="vendor/vector-map/jquery.vmap.js"></script>
    <script src="vendor/vector-map/jquery.vmap.min.js"></script>
    <script src="vendor/vector-map/jquery.vmap.sampledata.js"></script>
    <script src="vendor/vector-map/jquery.vmap.world.js"></script>

    <!-- Main JS-->
    <script src="js/main.js"></script>
    <script src="js/sweetalert.min.js"></script>
    <script src="js/bootstrap-datepicker.min.js"></script>
    <script>
      $(document).ready(function(){
          function preview(input){
            if(input.files && input.files[0]){
              var reader = new FileReader();

              reader.onload = function (e){
                $('#pict').attr('src', e.target.result);
              }

              reader.readAsDataURL(input.files[0]);
            }
          }
          $('#gambar').change(function(){
            preview(this);
          })
      });
    </script>
    <script>
      $(document).ready(function(){
          function preview(input){
            if(input.files && input.files[0]){
              var reader = new FileReader();

              reader.onload = function (e){
                $('#pict2').attr('src', e.target.result);
              }

              reader.readAsDataURL(input.files[0]);
            }
          }
          $('#gambar2').change(function(){
            preview(this);
          })
      });
    </script>
    <script>
      $(document).ready(function(){
        $('#forLogout').click(function(e){
          e.preventDefault();
            swal({
            title: "Logout",
            text: "Yakin Logout?",
            type: "info",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: false,
            closeOnCancel: true
          }, function(isConfirm) {
            if (isConfirm) {
              window.location.href="?logout";
            }
          });
        });



      })
    </script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        } );
    </script>
	<?php include "config/alert.php"; ?>
    <script>
function loadNotif() {
    $.getJSON("config/notif.php", function(data) {
        let html = "";

        if (data.length === 0) {
            html += `
                <div class="notifi__item">
                    <div class="bg-c3 img-cir img-40">
                        <i class="zmdi zmdi-info"></i>
                    </div>
                    <div class="content">
                        <p>Tidak ada notifikasi</p>
                    </div>
                </div>`;

            // ❗ HAPUS tanda merah kalau tidak ada notifikasi
            $("#notifBell").removeClass("has-noti");

        } else {
            data.forEach(item => {
                html += `
                <div class="notifi__item">
                    <div class="bg-c1 img-cir img-40">
                        <i class="zmdi zmdi-alert-circle-o"></i>
                    </div>
                    <div class="content">
                        <p>Stok menipis: ${item.nama_barang}</p>
                        <span class="date">Sisa ${item.stok_barang} pcs</span>
                    </div>
                </div>`;
            });

            // ❗ TAMBAHKAN tanda merah kalau ada notif
            $("#notifBell").addClass("has-noti");
        }

        $("#notifContent").html(html);
    });
}

setInterval(loadNotif, 6000);
loadNotif();
</script>
<script>
// Simpan jumlah notif lama
let lastNotifCount = localStorage.getItem("notifCount") || 0;

// Load Notifikasi
function loadNotif() {
    $.getJSON("config/notif.php", function(data) {

        let html = "";
        let count = data.length;

        // =====================
        // TAMPILKAN ISI NOTIF
        // =====================
        if (count === 0) {
            html += `
                <div class="notifi__item">
                    <div class="bg-c3 img-cir img-40">
                        <i class="zmdi zmdi-info"></i>
                    </div>
                    <div class="content">
                        <p>Tidak ada notifikasi</p>
                    </div>
                </div>`;
        } else {
            data.forEach(item => {
                html += `
                    <div class="notifi__item">
                        <div class="bg-c1 img-cir img-40">
                            <i class="zmdi zmdi-alert-circle-o"></i>
                        </div>
                        <div class="content">
                            <p>${item.pesan}</p>
                            <span class="date">${item.detail}</span>
                        </div>
                    </div>`;
            });
        }

        $("#notifContent").html(html);

        // =====================
        // TANDA MERAH LOGIKA
        // =====================
        if (count > lastNotifCount) {
            // Ada notifikasi baru → tampilkan bulat merah
            $("#notifBell").addClass("has-noti");
        } 
        else if (count == 0) {
            $("#notifBell").removeClass("has-noti");
        }

        // Simpan jumlah notif
        localStorage.setItem("notifCount", count);
    });
}

// ==========================
// KETIKA NOTIF DIBUKA
// ==========================
$("#notifBell").on("click", function() {
    // Hapus bulat merah saat dibuka
    $("#notifBell").removeClass("has-noti");

    // Update notif
    let count = $("#notifContent .notifi__item").length;
    localStorage.setItem("notifCount", count);
});

// AUTO REFRESH NOTIF
$(document).ready(function() {
    loadNotif();
    setInterval(loadNotif, 6000);
});
</script>

</body>
</html>