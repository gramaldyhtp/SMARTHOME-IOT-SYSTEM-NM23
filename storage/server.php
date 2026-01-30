<?php
// Konfigurasi Folder Upload
$uploadDir = 'uploads/';

// Cek apakah folder uploads ada, jika tidak buat baru
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// --- LOGIKA UTAMA ---
$method = $_SERVER['REQUEST_METHOD'];

// 1. Handle Request Upload dari ESP32 (POST)
if ($method === 'POST') {
    // Ambil Header Custom dari ESP32
    // Di PHP, header "X-Filename" otomatis menjadi "HTTP_X_FILENAME"
    $filename = isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : 'unknown_image.jpg';
    $photoNum = isset($_SERVER['HTTP_X_PHOTO_NUMBER']) ? $_SERVER['HTTP_X_PHOTO_NUMBER'] : '0';
    
    // Tentukan lokasi simpan
    $destination = $uploadDir . $filename;
    
    // Baca Data Mentah (Raw Binary) dari Body Request
    // Kita tidak bisa pakai $_FILES karena ESP32 kirim raw bytes
    $data = file_get_contents('php://input');
    
    if (file_put_contents($destination, $data)) {
        http_response_code(200);
        echo "Sukses Upload Foto #$photoNum: $filename";
        
        // Log sederhana ke layar server (jika dijalankan via terminal)
        error_log("?? Menerima: $filename (Ukuran: " . strlen($data) . " bytes)");
    } else {
        http_response_code(500);
        echo "Gagal menyimpan file.";
        error_log("? Gagal menyimpan $filename");
    }
}

// 2. Handle Cek Koneksi (GET)
// Ini untuk bagian setup() di ESP32 yang mengecek server hidup/mati
elseif ($method === 'GET') {
    http_response_code(200);
    echo "CCTV PHP Server Ready!";
}

else {
    http_response_code(405); // Method Not Allowed
    echo "Hanya menerima GET dan POST.";
}
?>
