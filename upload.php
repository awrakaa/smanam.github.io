<?php
session_start(); // Mulai sesi

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = __DIR__ . '/'; // Lokasi upload file

    // Mengambil informasi file yang di-upload
    $fileName = $_FILES['file']['name'];
    $tempFile = $_FILES['file']['tmp_name'];

    // Mendapatkan ekstensi file
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

    // Memeriksa apakah file yang di-upload adalah file zip atau rar
    if ($fileExt == 'zip') {
        // Membaca isi file zip untuk mendapatkan nama folder
        $zip = new ZipArchive;
        if ($zip->open($tempFile) === true) {
            // Cari nama folder di dalam zip
            $folderName = findFolderName($zip);

            // Jika ditemukan, ekstrak isi zip ke dalam folder ekstraksi
            if ($folderName !== false) {
                $extractDir = $uploadDir . '/';

                // Membuat folder jika belum ada
                if (!file_exists($extractDir)) {
                    mkdir($extractDir, 0777, true);
                }

                // Menjalankan proses ekstraksi file zip
                $zip->extractTo($extractDir);
                $zip->close();

                // Simpan nama folder dalam variabel sesi
                $_SESSION['uploadedFolder'] = $folderName;

                // Redirect to the HTML page with success parameter and folderName
                header("Location: index.html?success=true&folderName=" . urlencode($folderName));
                exit;
            } else {
                echo 'Tidak dapat menemukan folder di dalam file zip.';
            }
        } else {
            echo 'Gagal membuka file zip.';
        }
    } else {
        echo 'Hanya file zip yang diizinkan.';
    }
} else {
    echo 'Metode request tidak valid.';
}

// Fungsi untuk mencari nama folder di dalam file zip
function findFolderName($zip) {
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $filename = $zip->getNameIndex($i);
        // Jika nama file diakhiri dengan '/', kembalikan nama file (direktori)
        if (substr($filename, -1) == '/') {
            return $filename;
        }
    }
    // Jika tidak ditemukan, kembalikan false
    return false;
}
?>
