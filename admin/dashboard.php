<?php 
$dash = new lsp();

// Mengambil jumlah data
$dis  = $dash->getCountRows("table_distributor");
$mer  = $dash->getCountRows("table_merek");
$bar  = $dash->selectCount("table_barang","kd_barang");

// Cek hak akses admin
if ($_SESSION['level'] != "Admin") {
    header("location:../index.php");
    exit;
}
?>


<section class="au-breadcrumb m-t-75">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="au-breadcrumb-content">
                        <div class="au-breadcrumb-left">
                            <ul class="list-unstyled list-inline au-breadcrumb__list">
                                <li class="list-inline-item active">
                                    <a href="#">Home</a>
                                </li>
                                <li class="list-inline-item seprate">
                                    <span>/</span>
                                </li>
                                <li class="list-inline-item">Dashboard</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Informasi Level Login -->
<div style="
    background:#eef3ff;
    padding:10px 20px;              /* kecil */
    border-left:5px solid #4e73df;
    border-radius:10px;
    margin-top:25px;               /* turun dikit */
    margin-bottom:20px;
    margin-left:20px;
    margin-right:20px;
">
    <h4 style="
        margin:0;
        font-size:18px;            /* kecil */
        font-weight:600;
        color:#1a4ccc;
    ">
        Anda login sebagai: <?= strtoupper($_SESSION['level']); ?>
    </h4>
</div>


<div class="main-content">
    <div class="section__content section__content--p30">
        <div class="container-fluid">

            <div class="row" style="margin-top: -30px;">

                <!-- Total Barang -->
                <div class="col-sm-6 col-lg-4">
                    <div class="overview-item overview-item--c1">
                        <div class="overview__inner">
                            <div class="overview-box clearfix">
                                <div class="icon">
                                    <i class="zmdi zmdi-shopping-cart"></i>
                                </div>
                                <div class="text">
                                    <h2><?= $bar['count'] ?></h2>
                                    <span>Barang</span>
                                </div>
                            </div>
                            <div class="overview-chart">
                                <canvas id="widgetChart1"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Merek -->
                <div class="col-sm-6 col-lg-4">
                    <div class="overview-item overview-item--c2">
                        <div class="overview__inner">
                            <div class="overview-box clearfix">
                                <div class="icon">
                                    <i class="zmdi zmdi-calendar-note"></i>
                                </div>
                                <div class="text">
                                    <h2><?= $mer; ?></h2>
                                    <span>Merek</span>
                                </div>
                            </div>
                            <div class="overview-chart">
                                <canvas id="widgetChart2"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Distributor -->
                <div class="col-sm-6 col-lg-4">
                    <div class="overview-item overview-item--c3">
                        <div class="overview__inner">
                            <div class="overview-box clearfix">
                                <div class="icon">
                                    <i class="zmdi zmdi-account-o"></i>
                                </div>
                                <div class="text">
                                    <h2><?= $dis; ?></h2>
                                    <span>Distributor</span>
                                </div>
                            </div>
                            <div class="overview-chart">
                                <canvas id="widgetChart3"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- end row -->

        </div>
    </div>
</div>
