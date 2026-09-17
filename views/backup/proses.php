<?php
@ob_start();
@session_start();

if (empty($_SESSION['codekop_session']) || (int)$_SESSION['codekop_session']['akses'] !== 1) {
    header("Location: ../../index.php");
    exit;
}

require_once __DIR__ . '/../../setting.php';
require_once __DIR__ . '/../../helper.php';

$act = $_GET['act'] ?? '';

if ($act === 'download') {
    try {
        set_time_limit(300); // 5 menit batas eksekusi untuk DB besar
        ini_set('memory_limit', '256M');

        // Ambil daftar seluruh tabel
        $tables = [];
        $stmt = $connectdb->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $dbName = $db ?? 'database';
        $fileName = 'backup_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $dbName) . '_' . date('Y-m-d_H-i-s') . '.sql';

        $out = "-- ========================================================\n";
        $out .= "-- Database Backup: " . $dbName . "\n";
        $out .= "-- Tanggal Backup : " . date('d-m-Y H:i:s') . "\n";
        $out .= "-- POS Application Backup System\n";
        $out .= "-- ========================================================\n\n";
        $out .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $out .= "SET AUTOCOMMIT = 0;\n";
        $out .= "START TRANSACTION;\n";
        $out .= "SET time_zone = \"+07:00\";\n";
        $out .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        foreach ($tables as $table) {
            $out .= "-- --------------------------------------------------------\n";
            $out .= "-- Struktur tabel `$table`\n";
            $out .= "-- --------------------------------------------------------\n";
            $out .= "DROP TABLE IF EXISTS `$table`;\n";

            $stmtCreate = $connectdb->query("SHOW CREATE TABLE `$table`");
            $rowCreate = $stmtCreate->fetch(PDO::FETCH_NUM);
            $out .= $rowCreate[1] . ";\n\n";

            // Ambil data tabel
            $stmtData = $connectdb->query("SELECT * FROM `$table`");
            $rowCount = $stmtData->rowCount();

            if ($rowCount > 0) {
                $out .= "-- Data untuk tabel `$table` ($rowCount baris)\n";
                $batch = [];
                $batchSize = 0;

                while ($row = $stmtData->fetch(PDO::FETCH_NUM)) {
                    $escaped = array_map(function ($val) use ($connectdb) {
                        if ($val === null) {
                            return 'NULL';
                        }
                        return $connectdb->quote($val);
                    }, $row);

                    $batch[] = "(" . implode(", ", $escaped) . ")";
                    $batchSize++;

                    // Pecah insert per 100 baris agar file rapi & performa import optimal
                    if ($batchSize >= 100) {
                        $out .= "INSERT INTO `$table` VALUES\n" . implode(",\n", $batch) . ";\n";
                        $batch = [];
                        $batchSize = 0;
                    }
                }

                if (!empty($batch)) {
                    $out .= "INSERT INTO `$table` VALUES\n" . implode(",\n", $batch) . ";\n\n";
                } else {
                    $out .= "\n";
                }
            }
        }

        $out .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        $out .= "COMMIT;\n";

        // Bersihkan output buffer sebelum kirim file
        if (ob_get_length()) {
            ob_end_clean();
        }

        // Header download file .sql
        header('Content-Description: File Transfer');
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . strlen($out));

        echo $out;
        exit;
    } catch (Exception $e) {
        if (ob_get_length()) {
            ob_end_clean();
        }
        die("Gagal membuat backup database: " . htmlspecialchars($e->getMessage()));
    }
} else {
    header("Location: index.php");
    exit;
}
