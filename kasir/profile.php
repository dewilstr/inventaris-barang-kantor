<?php
if (!isset($_SESSION)) {
    session_start();
}

include "../config/controller.php";
$function = new lsp();

// Ambil user berdasarkan username di session
$user = $function->AuthUser($_SESSION['username']);
if (!$user) {
    echo "<p>Gagal mengambil data user.</p>";
    exit;
}

// Handle update profile
if (isset($_POST['updateProfile'])) {

    $nama = $_POST['nama_user'];
    $username = $_POST['username'];
    $fileName = $user['foto_user'];

    // Jika ada upload foto baru
    if (!empty($_FILES['foto_user']['name'])) {
        $allowed = ['jpg','jpeg','png'];
        $foto = $_FILES['foto_user'];
        $ext = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));

        if (in_array($ext,$allowed)) {
            $newName = time() . "_" . $foto['name'];
            move_uploaded_file($foto['tmp_name'], "../img/" . $newName);
            $fileName = $newName;
        }
    }

    // Update ke DB
    $query = $function->updateProfileKasir(
        $user['kd_user'],
        $nama,
        $username,
        $fileName
    );

    if ($query) {
        echo "<script>alert('Profil berhasil diperbarui'); window.location='?page=profile';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil');</script>";
    }
}
?>
<div class="container p-4">
    <h3>Profil Kasir</h3>
    <div class="card p-4" style="max-width:500px;">
        <form method="POST" enctype="multipart/form-data">

            <center>
                <img src="../img/<?= $user['foto_user'] ?>" 
                     style="width:120px;height:120px;border-radius:50%;object-fit:cover;">
            </center>

            <div class="form-group mt-3">
                <label>Nama</label>
                <input type="text" name="nama_user" class="form-control" 
                       value="<?= $user['nama_user'] ?>">
            </div>

            <div class="form-group mt-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" 
                       value="<?= $user['username'] ?>">
            </div>

            <div class="form-group mt-3">
                <label>Foto Profil</label>
                <input type="file" name="foto_user" class="form-control">
            </div>

            <button type="submit" name="updateProfile" class="btn btn-primary mt-4">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
