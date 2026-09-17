-- Migration untuk Fitur Harga Grosir Otomatis
-- Jalankan query ini di phpMyAdmin pada database Anda:

ALTER TABLE `barang` 
ADD COLUMN `min_grosir` INT NOT NULL DEFAULT 0 AFTER `harga_jual`,
ADD COLUMN `harga_grosir` VARCHAR(20) NOT NULL DEFAULT '0' AFTER `min_grosir`;
