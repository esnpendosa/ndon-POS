-- =====================================================================
-- Migrasi: Tabel stok_manual (fitur Riwayat Stok / Sesuaikan Stok)
-- Aman dijalankan berulang kali - tidak akan error/menghapus data
-- jika tabel sudah pernah dibuat sebelumnya.
--
-- Cara pakai:
--   1. phpMyAdmin  -> pilih database -> tab "Import" -> pilih file ini -> Go
--   2. atau via CLI:
--      mysql -u root -p nama_database < migration_stok_manual.sql
-- =====================================================================

CREATE TABLE IF NOT EXISTS `stok_manual` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_barang` int NOT NULL,
  `jenis` enum('masuk','keluar') NOT NULL,
  `qty` double NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `id_member` int NOT NULL,
  `tgl_input` date DEFAULT NULL,
  `periode` varchar(10) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_barang` (`id_barang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
