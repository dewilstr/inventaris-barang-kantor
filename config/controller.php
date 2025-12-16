<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$username = "root";
$password = "";
$database = "inventaris_barang_kantor";
$hostname = "localhost";
$con = mysqli_connect($hostname,$username,$password,$database) or die("Connection Corrupt");

$rg = new lsp();

class lsp{

    /* ===================== LOGIN ===================== */
    public function login($username,$password){
        global $con;

        // Hindari SQL Injection
        $username = mysqli_real_escape_string($con, $username);

        $sql   = "SELECT * FROM table_user WHERE username ='$username'";
        $query = mysqli_query($con,$sql);

        if(!$query){
            return ['response'=>'negative','alert'=>'Query Error: '.mysqli_error($con)];
        }

        $rows  = mysqli_num_rows($query);
        $assoc = mysqli_fetch_assoc($query);

        if($rows > 0){
            // Verifikasi password hash bcrypt
            if(password_verify($password, $assoc['password'])){
                // simpan session
               $_SESSION['username'] = $assoc['username'];
               $_SESSION['level_user'] = $assoc['level'];     // FIX
               $_SESSION['nama_user']  = $assoc['nama_user']; // FIX
               $_SESSION['foto_user']  = $assoc['foto_user']; // FIX

                return ['response'=>'positive','alert'=>'Berhasil Login','level'=>$assoc['level']];
            }else{
                return ['response'=>'negative','alert'=>'Password Salah'];    
            }
        }else{
            return ['response'=>'negative','alert'=>'Username atau Password Salah'];
        }
    }

    public function redirect($redirect){
        return ['response'=>'positive','alert'=>'Login Berhasil','redirect'=>$redirect];   
    }

    public function logout(){
        session_destroy();
        header("Location:index.php");
        exit;
    }

    public function logout2(){
        session_destroy();
        header("Location:index.php");
        exit;
    }

    /* ===================== SELECT / COUNT / SUM ===================== */
    public function selectSum($table,$namaField){
        global $con;
        $sql = "SELECT SUM($namaField) as sum FROM $table";
        $query = mysqli_query($con,$sql);
        return mysqli_fetch_assoc($query);
    }

    public function selectSumWhere($table,$namaField,$where){
        global $con;
        $sql = "SELECT SUM($namaField) as sum FROM $table WHERE $where";
        $query = mysqli_query($con,$sql);
        return mysqli_fetch_assoc($query);
    }

    public function selectCount($table,$namaField){
        global $con;
        $sql = "SELECT COUNT($namaField) as count FROM $table";
        $query = mysqli_query($con,$sql);
        return mysqli_fetch_assoc($query);
    }

    public function selectBetween($table,$whereparam,$param,$param1){
        global $con;
        $sql = "SELECT * FROM $table WHERE $whereparam BETWEEN '$param' AND '$param1'";
        $query = mysqli_query($con,$sql);

        $sqls = "SELECT SUM(stok_barang) as count FROM $table WHERE $whereparam BETWEEN '$param' AND '$param1'";
        $querys = mysqli_query($con,$sqls);
        $assocs = mysqli_fetch_assoc($querys);
        $data = [];
        while($bigData = mysqli_fetch_assoc($query)){
            $data[] = $bigData;
        }
        return ['data'=>$data,'jumlah'=>$assocs];
    }

    public function AuthUser($sessionUser){
        global $con;
        $sql = "SELECT * FROM table_user WHERE username = '$sessionUser'";
        $query = mysqli_query($con,$sql);
        return mysqli_fetch_assoc($query);
    }

    /* ===================== REGISTER ===================== */
    public function register($kd_user,$name,$username,$password,$confirm, $foto, $level,$redirect){
        global $con;

        if(empty($kd_user) || empty($name) || empty($username) || empty($password)){
            return ['response'=>'negative','alert'=>'Lengkapi Form'];
        }
        
        $sql     = "SELECT * FROM table_user WHERE username = '$username'";
        $query   = mysqli_query($con,$sql);
        $rows    = mysqli_num_rows($query);

        if(strlen($username) < 6){
            return ['response'=>'negative','alert'=>'Username minimal 6 Huruf']; 
        }

        if($rows == 0){
            $name     = htmlspecialchars($name);
            $username = strtolower(htmlspecialchars($username));
            $password = htmlspecialchars($password);
            $confirm  = htmlspecialchars($confirm);
            
            if($password == $confirm){
                $password = password_hash($password, PASSWORD_BCRYPT);
                $response = $this->validateImage();
                $fotoName = isset($response['image']) ? $response['image'] : 'default.png';

                $sql = "INSERT INTO table_user (kd_user,nama_user,username,password,foto_user,level) 
                        VALUES('$kd_user','$name','$username','$password','$fotoName','$level')";
                $query   = mysqli_query($con,$sql);
                if($query){
                    return ['response'=>'positive','alert'=>'Registrasi Berhasil','redirect'=>$redirect];
                }else{
                    return ['response'=>'negative','alert'=>'Registrasi Error: '.mysqli_error($con)];
                }
            }else{
                return ['response'=>'negative','alert'=>'Password Tidak Cocok'];
            }
        }else{
            return ['response'=>'negative','alert'=>'Username telah digunakan'];
        }
    }

    /* ===================== AUTO KODE ===================== */
    public function autokode($table,$field,$pre){
        global $con;
        $sqlc   = "SELECT COUNT($field) as jumlah FROM $table";
        $querys = mysqli_query($con,$sqlc);
        $number = mysqli_fetch_assoc($querys);
        if($number['jumlah'] > 0){
            $sql    = "SELECT MAX($field) as kode FROM $table";
            $query  = mysqli_query($con,$sql);
            $number = mysqli_fetch_assoc($query);
            $strnum = substr($number['kode'], 2,3);
            $strnum = $strnum + 1;
            if(strlen($strnum) == 3){ 
                $kode = $pre.$strnum;
            }else if(strlen($strnum) == 2){ 
                $kode = $pre."0".$strnum;
            }else if(strlen($strnum) == 1){ 
                $kode = $pre."00".$strnum;
            }
        }else{
            $kode = $pre."001";
        }

        return $kode;
    }

    /* ===================== QUERY / SELECT ===================== */
    public function querySelect($sql){
        global $con;
        $query = mysqli_query($con,$sql);
        $data = [];
        while($bigData = mysqli_fetch_assoc($query)){
            $data[] = $bigData;
        }
        return $data;
    }

    public function selectWhere($table,$where,$whereValues){
        global $con;
        $sql = "SELECT * FROM $table WHERE $where = '$whereValues'";
        $query = mysqli_query($con,$sql);
        return mysqli_fetch_assoc($query);
    }

    public function edit($table,$where,$whereValues){
        global $con;
        $sql = "SELECT * FROM $table WHERE $where = '$whereValues'";
        $query = mysqli_query($con,$sql);
        $data = [];
        while($bigData = mysqli_fetch_assoc($query)){
            $data[] = $bigData;
        }
        return $data;
    }  

    public function getCountRows($table){
        global $con;
        $sql   = "SELECT * FROM $table";
        $query = mysqli_query($con,$sql);
        return mysqli_num_rows($query);
    }
    
    public function sessionCheck(){
        return isset($_SESSION['username']) ? "true" : "false";
    }

    /* ===================== INSERT / UPDATE / DELETE ===================== */
public function insert($table, $columns, $values, $redirect = null)
{
    global $con;

    $sql = "INSERT INTO $table ($columns) VALUES ($values)";
    $query = mysqli_query($con, $sql);

    if ($query) {
        if (!empty($redirect)) {
            header("Location: $redirect");
            exit;
        }
        return ['response'=>'positive','alert'=>'Berhasil menambah data'];
    } else {
        return ['response'=>'negative','alert'=>'Gagal tambah data: '.mysqli_error($con)];
    }
}


    public function update($table,$values,$where,$whereValues,$redirect){
        global $con;
        $sql   = "UPDATE $table SET $values WHERE $where = '$whereValues'";
        $query = mysqli_query($con,$sql);
        if($query){
            if (!empty($redirect)) {
                header("Location: $redirect");
                exit;
            }
            return ['response'=>'positive','alert'=>'Berhasil update data'];
        }else{
            return ['response'=>'negative','alert'=>'Gagal Update Data: '.mysqli_error($con)];
        }
    }   

    public function delete($table,$where,$whereValues,$redirect){
        global $con;
        $sql = "DELETE FROM $table WHERE $where = '$whereValues'";
        $query = mysqli_query($con,$sql);
        if($query){
            if (!empty($redirect)) {
                header("Location: $redirect");
                exit;
            }
            return ['response'=>'positive','alert'=>'Berhasil Menghapus Data'];
        }else{
            return ['response'=>'negative','alert'=>'Gagal Menghapus Data: '.mysqli_error($con)];
        }
    }

    /* ===================== VALIDATOR ===================== */
    public function select($table){
        global $con;
        $sql = "SELECT * FROM $table";
        $query = mysqli_query($con,$sql);
        $data = [];
        while($bigData = mysqli_fetch_assoc($query)){
            $data[] = $bigData;
        }
        return $data;
    }

    public function selectCountWhere($table,$namaField,$where){
        global $con;
        $sql = "SELECT COUNT($namaField) as count FROM $table WHERE $where";
        $query = mysqli_query($con,$sql);
        return mysqli_fetch_assoc($query);
    }

    public function validateHtml($field){ 
        return htmlspecialchars($field);
    }

    public function escape($str){
        global $con;
        return mysqli_real_escape_string($con, $str);
    }

    public function validateImage(){
        $name       = $_FILES['foto']['name']; 
        $ukuranFile = $_FILES['foto']['size']; 
        $tmpName    = $_FILES['foto']['tmp_name']; 
        $folder     = 'img/'; 

        if(empty($name)){
            return ['types'=>'false','alert'=>'Tidak ada file diupload'];
        }

        $ekstensiGambar = explode('.',$name); 
        $ekstensiBelakang = strtolower(end($ekstensiGambar)); 
        $ekstensi = ['jpg','jpeg','png','gif']; 

        if (!in_array($ekstensiBelakang, $ekstensi)) { 
            return ['types'=>'false','alert'=>'Gambar hanya boleh jpg, jpeg, png']; 
        }

        if ($ukuranFile > 4000000) { 
            return ['types'=>'false','alert'=>'Ukuran gambar terlalu besar']; 
        }

        if (!file_exists($folder)) { 
            mkdir($folder, 0755, true);
        }

        $newName = time().rand(100,999).".".$ekstensiBelakang;
        if (!move_uploaded_file($tmpName, $folder.$newName)) {
            return ['types'=>'false','alert'=>'Gagal menyimpan file gambar'];
        }

        return ['types'=>'true','image'=>$newName];
    }
// ========================================
// FUNGSi INSERT PRE-TRANSAKSI (KHUSUS KASIR)
// ========================================
public function insertPreTransaksi($kd_pretransaksi, $kd_transaksi, $kd_barang, $jumlah, $sub_total)
{
    global $con;

    $query = "
        INSERT INTO table_pretransaksi
        (kd_pretransaksi, kd_transaksi, kd_barang, jumlah, sub_total)
        VALUES
        ('$kd_pretransaksi', '$kd_transaksi', '$kd_barang', '$jumlah', '$sub_total')
    ";

    return mysqli_query($con, $query);
}

public function updateProfileKasir($kd_user, $nama, $username, $foto_lama)
{
    global $con;

    // Cek apakah ada foto baru
    if (!empty($_FILES['foto']['name'])) {
        $upload = $this->validateImage();
        if ($upload['types'] == 'true') {
            $fotoBaru = $upload['image'];

            // Hapus foto lama jika bukan default
            if ($foto_lama !== 'default.png' && file_exists("img/".$foto_lama)) {
                unlink("img/".$foto_lama);
            }
        } else {
            return $upload; // error dari validateImage
        }
    } else {
        $fotoBaru = $foto_lama;
    }

    // Update data user
    $sql = "
        UPDATE table_user SET 
            nama_user = '$nama',
            username = '$username',
            foto_user = '$fotoBaru'
        WHERE kd_user = '$kd_user'
    ";

    if (mysqli_query($con, $sql)) {
        return ['response'=>'positive','alert'=>'Profil berhasil diperbarui'];
    } else {
        return ['response'=>'negative','alert'=>'Gagal update: '.mysqli_error($con)];
    }
}


}

?>
