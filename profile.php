<?php
// Pastikan file controller / koneksi sudah di-include di atas file ini.
// mis: include 'config/controller.php';
// jika sudah di-include di layout, jangan include lagi supaya gak duplikat class/conn

// Debug mode biar error kelihatan (hapus di production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Mulai session jika belum
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// helper untuk aman mengambil value dari array
function get_val($arr, $key, $default = '') {
    return (is_array($arr) && isset($arr[$key])) ? $arr[$key] : $default;
}

// Pastikan $my instance ada (kalau sudah dideklarasi di luar, bagian ini tidak akan menimpa)
if (!isset($my) || !is_object($my)) {
    $my = new lsp();
}

// Ambil data user berdasarkan session username
$auth = $my->selectWhere("table_user", "username", $_SESSION['username'] ?? null);
if (!is_array($auth)) {
    $auth = [];
}

// NOTE: $response akan kita gunakan untuk alert (array dengan key 'response' dan 'alert')
// Jangan timpa $response saat proses lain (mis: upload image) -> gunakan variabel lain seperti $imgResp

/* ================== UPDATE PROFILE ================== */
if (isset($_POST['btnUpdate'])) {
    $nama = $my->validateHtml($_POST['nama']);

    if (empty($_FILES['foto']['name'])) {
        // kalau tidak upload foto
        $values = "nama_user='" . $my->escape($nama) . "'";
        $response = $my->update("table_user", $values, "username", $_SESSION['username'], $redirectPage);
    } else {
        // jika ada upload foto
        $imgResp = $my->validateImage();
        if (!empty($imgResp['types']) && $imgResp['types'] == "true") {
            $values = "nama_user='" . $my->escape($nama) . "', foto_user='" . $my->escape($imgResp['image']) . "'";
        $response = $my->update("table_user", $values, "username", $_SESSION['username'], $redirectPage);
        } else {
            $msg = $imgResp['alert'] ?? 'Gagal upload gambar';
            $response = ['response' => 'negative', 'alert' => $msg];
        }
    }
}

/* ================== UPDATE PASSWORD ================== */
if (isset($_POST['ubahPassword'])) {
    $password     = $_POST['password'] ?? '';
    $passwordbaru = $_POST['passwordbaru'] ?? '';
    $confirm      = $_POST['confirm'] ?? '';

    if (!isset($con)) {
        $response = ['response' => 'negative', 'alert' => 'Database connection not found.'];
    } else {
        $sql  = "SELECT username, password FROM table_user WHERE username = '" . mysqli_real_escape_string($con, $_SESSION['username']) . "' LIMIT 1";
        $exec = mysqli_query($con, $sql);

        if ($exec && mysqli_num_rows($exec) > 0) {
            $asso   = mysqli_fetch_assoc($exec);
            $stored = $asso['password'] ?? '';

            $old_matches = false;
            if (isset($imgResp['types']) && $imgResp['types'] === "true") {
                $old_matches = true;
            } elseif (!empty($stored) && base64_decode($stored) === $password) {
                $old_matches = true;
            }

            if ($old_matches) {
                if (strlen($passwordbaru) < 6) {
                    $response = ['response' => 'negative', 'alert' => 'Password minimal 6 karakter'];
                } elseif ($passwordbaru !== $confirm) {
                    $response = ['response' => 'negative', 'alert' => 'Password baru dan konfirmasi tidak sama'];
                } else {
                    $hashed = password_hash($passwordbaru, PASSWORD_BCRYPT);
                    $value  = "password='" . mysqli_real_escape_string($con, $hashed) . "'";
                    $response = $my->update("table_user", $value, "username", $_SESSION['username'], $redirectPage);
                }
            } else {
                $response = ['response' => 'negative', 'alert' => 'Password lama tidak benar'];
            }
        } else {
            $response = ['response' => 'negative', 'alert' => 'User tidak ditemukan'];
        }
    }
}
?>

<!-- CSS tambahan untuk foto profil -->
<style>
.profile-img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: contain;
    background-color: #f0f0f0;
}
</style>

<!-- HTML view -->
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
                                <li class="list-inline-item">Profile</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="main-content" style="margin-top: -70px;">
    <div class="section__content section__content--p30">
        <div class="container-fluid">
            <div class="row">
                <!-- Form update profile -->
                <div class="col-md-6">
                    <form method="post" enctype="multipart/form-data">
                        <div class="card">
                            <div class="card-header">
                                <h3>Your Profile</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Kode User</label>
                                    <input type="text" class="form-control form-control-sm" 
                                        value="<?= htmlspecialchars(get_val($auth, 'kd_user')) ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" class="form-control form-control-sm" 
                                        value="<?= htmlspecialchars(get_val($auth, 'username')) ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" class="form-control form-control-sm" 
                                        value="<?= htmlspecialchars(get_val($auth, 'nama_user')) ?>" name="nama">
                                </div>
                                <div class="form-group">
                                    <label for="foto">Foto</label>
                                    <div style="padding-bottom: 15px;">
                                        <?php if (get_val($auth, 'foto_user') != ''): ?>
                                            <img alt="Profile Picture" class="profile-img" id="pict" 
                                                src="<?= 'img/' . htmlspecialchars(get_val($auth, 'foto_user')) ?>">
                                        <?php else: ?>
                                            <p>No file chosen</p>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" name="foto" id="gambar" class="form-control-file">
                                </div>
                                <hr>
                                <button name="btnUpdate" class="btn btn-warning">
                                    <i class="fa fa-check"></i> Update Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Form update password -->
                <div class="col-md-6">
                    <form method="post">
                        <div class="card">
                            <div class="card-header">
                                <h3>Your Password</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Password Lama</label>
                                    <input type="password" class="form-control form-control-sm" name="password">
                                </div>
                                <div class="form-group">
                                    <label>Password Baru</label>
                                    <input type="password" class="form-control form-control-sm" name="passwordbaru">
                                </div>
                                <div class="form-group">
                                    <label>Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control form-control-sm" name="confirm">
                                </div>
                                <hr>
                                <button name="ubahPassword" class="btn btn-warning">
                                    <i class="fa fa-check"></i> Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Alert jika ada response -->
<?php if (isset($response) && is_array($response)): ?>
    <?php include 'config/alert.php'; ?>
<?php endif; ?>
