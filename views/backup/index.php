<?php
if (empty($_SESSION['codekop_session']) || (int)$_SESSION['codekop_session']['akses'] !== 1) {
    redirect($baseURL . 'index.php');
    exit;
}

// Ambil statistik database
$stmtTables = $connectdb->query("SHOW TABLE STATUS");
$tableStats = $stmtTables->fetchAll(PDO::FETCH_ASSOC);

$totalTables = count($tableStats);
$totalRows = 0;
$totalSizeBytes = 0;

foreach ($tableStats as $tbl) {
    $totalRows += (int)($tbl['Rows'] ?? 0);
    $totalSizeBytes += (int)($tbl['Data_length'] ?? 0) + (int)($tbl['Index_length'] ?? 0);
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-database mr-2"></i> Backup Database
                </h3>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-info"><i class="fas fa-table"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Tabel</span>
                                <span class="info-box-number"><?= $totalTables; ?> Tabel</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-success"><i class="fas fa-list-ol"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Baris Data</span>
                                <span class="info-box-number"><?= number_format($totalRows, 0, ',', '.'); ?> Baris</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-12">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-warning"><i class="fas fa-hdd"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Estimasi Ukuran DB</span>
                                <span class="info-box-number"><?= formatBytes($totalSizeBytes); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <h5><i class="icon fas fa-info-circle"></i> Informasi Backup</h5>
                    Fitur ini akan mengekspor seluruh struktur tabel dan data transaksi ke dalam format file standar <b>.sql</b>. File backup ini dapat di-import kembali kapan saja melalui <b>phpMyAdmin</b> di cPanel / Laragon / XAMPP.
                </div>

                <div class="text-center my-4 py-3">
                    <a href="<?= $baseURL; ?>views/backup/proses.php?act=download" class="btn btn-primary btn-lg px-4 shadow">
                        <i class="fas fa-download mr-2"></i> Download Backup Database (.sql)
                    </a>
                </div>

                <hr>

                <h5 class="mt-4 mb-3 font-weight-bold"><i class="fas fa-list mr-1"></i> Rincian Tabel Database:</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nama Tabel</th>
                                <th class="text-center">Engine</th>
                                <th class="text-right">Jumlah Baris</th>
                                <th class="text-right">Ukuran Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($tableStats as $tbl): ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><i class="fas fa-table text-muted mr-2"></i><b><?= htmlspecialchars($tbl['Name'] ?? ''); ?></b></td>
                                <td class="text-center"><span class="badge badge-secondary"><?= htmlspecialchars($tbl['Engine'] ?? 'InnoDB'); ?></span></td>
                                <td class="text-right"><?= number_format((int)($tbl['Rows'] ?? 0), 0, ',', '.'); ?></td>
                                <td class="text-right"><?= formatBytes((int)($tbl['Data_length'] ?? 0) + (int)($tbl['Index_length'] ?? 0)); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
