<?php 
include_once "config/controller.php";

if ($_SESSION['level'] != "Admin") {
    header("location:../index.php");
    exit;
}

$con = mysqli_connect("localhost","root","","inventaris_barang_kantor");

$bulan = $_GET['bulan'] ?? "";
$tahun = $_GET['tahun'] ?? "";
?>

<!-- BREADCRUMB -->
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
                                <li class="list-inline-item">Laporan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MAIN CONTENT -->
<div class="main-content" style="margin-top: -60px;">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row">

                <!-- CARD FILTER -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title mb-3">Laporan Transaksi</strong>
                        </div>

                        <div class="card-body">
                            <form method="GET" action="">
                                <input type="hidden" name="page" value="laporan">

                                <div class="form-row">

                                    <div class="form-group col-md-4">
                                        <label>Bulan:</label>
                                        <select name="bulan" class="form-control" required>
                                            <option value="">-- Pilih Bulan --</option>
                                            <?php 
                                            for ($i = 1; $i <= 12; $i++) {
                                                $sel = ($bulan == $i) ? "selected" : "";
                                                echo "<option value='$i' $sel>$i</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Tahun:</label>
                                        <select name="tahun" class="form-control" required>
                                            <option value="">-- Pilih Tahun --</option>
                                            <?php
                                            for ($t = date("Y"); $t >= 2020; $t--) {
                                                $selTahun = ($tahun == $t) ? "selected" : "";
                                                echo "<option value='$t' $selTahun>$t</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4" style="margin-top:30px;">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-filter"></i> Filter
                                        </button>

                                        <!-- Tombol Reload -->
                                        <a href="pageAdmin.php?page=laporan" class="btn btn-danger">
                                            Reload
                                        </a>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- TAMPILKAN LAPORAN JIKA SUDAH FILTER -->
                <?php if ($bulan != "" && $tahun != ""): ?>

                <?php
                $query = "
                    SELECT * FROM table_transaksi
                    WHERE MONTH(tanggal_beli) = '$bulan'
                    AND YEAR(tanggal_beli) = '$tahun'
                ";

                $result = mysqli_query($con, $query);
                ?>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title mb-3">
                                Rekap Bulan <?= $bulan ?> Tahun <?= $tahun ?>
                            </strong>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless table-striped table-earning">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Transaksi</th>
                                            <th>Tanggal</th>
                                            <th>Jumlah</th>
                                            <th>Total Harga</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($result)): 
                                        ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= $row['kd_transaksi']; ?></td>
                                            <td><?= $row['tanggal_beli']; ?></td>
                                            <td><?= $row['jumlah_beli']; ?></td>
                                            <td>Rp <?= number_format($row['total_harga']); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
