<?php
// Pastikan file ini di-include setelah $response di-set oleh controller.
// Contoh: $response = ['response' => 'positive', 'alert' => 'Sukses!', 'redirect' => 'pageKasir.php'];

if (isset($response) && is_array($response) && isset($response['response'])) {
    $resp = $response['response'];

    if ($resp === 'negative') {
        // pesan error (fallback kalau kosong)
        $alert = isset($response['alert']) ? $response['alert'] : 'Terjadi kesalahan';
        // json_encode supaya aman untuk JS (meng-escape kutip dan karakter lain)
        echo "<script>swal('Error', " . json_encode($alert) . ", 'error');</script>";
    } elseif ($resp === 'positive') {
        $redirect = isset($response['redirect']) ? $response['redirect'] : '';

        if (!empty($redirect)) {
            // swal dengan callback yang me-redirect jika user klik OK
            echo "<script>
                swal({
                    title: 'Berhasil',
                    text: '',
                    type: 'success',
                    showCancelButton: false,
                    confirmButtonText: 'Yes',
                    closeOnConfirm: false,
                    closeOnCancel: true
                }, function(isConfirm) {
                    if (isConfirm) {
                        window.location.href = " . json_encode($redirect) . ";
                    }
                });
            </script>";
        } else {
            // swal tanpa redirect
            echo "<script>
                swal({
                    title: 'Berhasil',
                    text: '',
                    type: 'success',
                    showCancelButton: false,
                    confirmButtonText: 'Yes'
                });
            </script>";
        }
    }
}
// Jika $response tidak di-set atau bukan array, file ini tidak menampilkan apa-apa — aman.
?>
