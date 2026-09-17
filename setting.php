<?php 
/*
  |--------------------------------------------------------------------------
  | Setting Aplikasi
  |--------------------------------------------------------------------------
  |
  | Disini adalah tempat untuk mengatur aplikasi anda.
  |
*/
    // Database Configuration
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "ndonfood";

    try {
        $connectdb = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $connectdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connectdb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        // [SECURITY] Jangan tampilkan detail error ke pengguna — log ke file
        $log_dir = __DIR__ . '/logs';
        if (!is_dir($log_dir)) { @mkdir($log_dir, 0755, true); }
        error_log(date('[Y-m-d H:i:s] ') . "DB Error: " . $e->getMessage() . PHP_EOL, 3, $log_dir . '/error.log');
        die("Terjadi kesalahan sistem. Silahkan hubungi administrator.");
    }

    // Project Title Name (Loaded dynamically from settings database)
    try {
        $sql_toko_name = "SELECT nama_toko FROM toko WHERE id = 1";
        $stmt_toko_name = $connectdb->prepare($sql_toko_name);
        $stmt_toko_name->execute();
        $toko_name_res = $stmt_toko_name->fetch(PDO::FETCH_OBJ);
        $title_apl = !empty($toko_name_res->nama_toko) ? $toko_name_res->nama_toko : 'POS Kasir';
    } catch (Exception $e) {
        $title_apl = 'POS Kasir';
    }

    // Date Default Timezone
    date_default_timezone_set("Asia/Jakarta");

    // Base URL Setup
    // Dihitung dari posisi file setting.php ini terhadap document root, BUKAN dengan
    // menebak-nebak nama folder (mis. mencari "/transaksi") — cara lama itu salah untuk
    // folder apa pun yang namanya mengandung kata itu sebagai substring, seperti
    // "transaksi_beli", sehingga $baseURL jadi rusak dan AJAX gagal (error timeout palsu).
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
    $appRoot = str_replace('\\', '/', __DIR__);
    $appUrlPath = substr($appRoot, strlen($docRoot));
    $baseURL = $protocol . "://" . $host . rtrim($appUrlPath, '/') . '/';
?>