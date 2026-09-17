/*
SQLyog Professional v12.5.1 (64 bit)
MySQL - 10.6.11-MariaDB-log : Database - codekop_premium_posv1
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `barang` */

DROP TABLE IF EXISTS `barang`;

CREATE TABLE `barang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_barang` varchar(255) NOT NULL,
  `id_supplier` int(11) DEFAULT NULL,
  `id_kategori` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `nama_barang` varchar(255) DEFAULT NULL,
  `merk` varchar(255) NOT NULL,
  `harga_beli` int(11) NOT NULL,
  `harga_jual` int(11) NOT NULL,
  `satuan_barang` varchar(255) NOT NULL,
  `stok` double NOT NULL,
  `tgl_input` date DEFAULT NULL,
  `tgl_update` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `barang` */

insert  into `barang`(`id`,`id_barang`,`id_supplier`,`id_kategori`,`gambar`,`nama_barang`,`merk`,`harga_beli`,`harga_jual`,`satuan_barang`,`stok`,`tgl_input`,`tgl_update`) values 
(1,'BR001',1,1,NULL,'Pensil','Fabel Castel',1000,1500,'PCS',13,'2023-09-10',NULL),
(2,'1694344814222',1,1,NULL,'Indomie Goreng','Indomie',2000,3000,'PCS',30,'2023-09-10',NULL);

/*Table structure for table `barang_kategori` */

DROP TABLE IF EXISTS `barang_kategori`;

CREATE TABLE `barang_kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(255) DEFAULT NULL,
  `tgl_input` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `barang_kategori` */

insert  into `barang_kategori`(`id`,`nama_kategori`,`tgl_input`) values 
(1,'Uncategory','2021-09-07'),
(2,'Kategori 1','2023-09-10');

/*Table structure for table `barang_satuan` */

DROP TABLE IF EXISTS `barang_satuan`;

CREATE TABLE `barang_satuan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `satuan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `barang_satuan` */

insert  into `barang_satuan`(`id`,`satuan`) values 
(1,'PCS');

/*Table structure for table `hak_akses` */

DROP TABLE IF EXISTS `hak_akses`;

CREATE TABLE `hak_akses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hak_akses` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `hak_akses` */

insert  into `hak_akses`(`id`,`hak_akses`) values 
(1,'Admin'),
(5,'Kasir'),
(6,'Gudang');

/*Table structure for table `keranjang` */

DROP TABLE IF EXISTS `keranjang`;

CREATE TABLE `keranjang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_barang` varchar(255) NOT NULL,
  `id_member` int(11) NOT NULL,
  `id_supplier` int(11) DEFAULT NULL,
  `nama_supplier` varchar(255) DEFAULT NULL,
  `nama_barang` varchar(255) DEFAULT NULL,
  `diskon` int(11) NOT NULL,
  `jumlah` varchar(255) NOT NULL,
  `beli` int(11) NOT NULL,
  `jual` int(11) NOT NULL,
  `tanggal_input` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `keranjang` */

insert  into `keranjang`(`id`,`id_barang`,`id_member`,`id_supplier`,`nama_supplier`,`nama_barang`,`diskon`,`jumlah`,`beli`,`jual`,`tanggal_input`) values 
(131,'2',1,NULL,NULL,'Indomie Goreng',0,'1',2000,3000,'2023-12-25');

/*Table structure for table `keranjang_beli` */

DROP TABLE IF EXISTS `keranjang_beli`;

CREATE TABLE `keranjang_beli` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_barang` varchar(255) NOT NULL,
  `id_member` int(11) NOT NULL,
  `id_supplier` int(11) DEFAULT NULL,
  `nama_supplier` varchar(255) DEFAULT NULL,
  `nama_barang` varchar(255) DEFAULT NULL,
  `jumlah` varchar(255) NOT NULL,
  `beli` int(11) NOT NULL,
  `jual` int(11) NOT NULL,
  `tanggal_input` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `keranjang_beli` */

insert  into `keranjang_beli`(`id`,`id_barang`,`id_member`,`id_supplier`,`nama_supplier`,`nama_barang`,`jumlah`,`beli`,`jual`,`tanggal_input`) values 
(91,'4',11,NULL,NULL,'Indomie Goreng','1',2000,3000,'2022-02-13');

/*Table structure for table `operasional` */

DROP TABLE IF EXISTS `operasional`;

CREATE TABLE `operasional` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_operasional` varchar(255) DEFAULT NULL,
  `status_operasional` varchar(255) DEFAULT NULL,
  `harga_operasional` int(11) NOT NULL,
  `ket_operasional` text DEFAULT NULL,
  `tgl_input` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `id_users` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `operasional` */

/*Table structure for table `pelanggan` */

DROP TABLE IF EXISTS `pelanggan`;

CREATE TABLE `pelanggan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_pelanggan` varchar(255) DEFAULT NULL,
  `nama_pelanggan` varchar(255) DEFAULT NULL,
  `alamat_pelanggan` text DEFAULT NULL,
  `telepon_pelanggan` varchar(25) DEFAULT NULL,
  `email_pelanggan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `pelanggan` */

/*Table structure for table `pembelian` */

DROP TABLE IF EXISTS `pembelian`;

CREATE TABLE `pembelian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nm_supplier` varchar(255) DEFAULT NULL,
  `no_trx` varchar(255) DEFAULT NULL,
  `id_member` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `beli` int(11) DEFAULT NULL,
  `tanggal_input` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `periode` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `pembelian` */

insert  into `pembelian`(`id`,`nm_supplier`,`no_trx`,`id_member`,`jumlah`,`beli`,`tanggal_input`,`created_at`,`periode`) values 
(1,'-','OR001',1,2,3000,'2023-09-10','2023-09-10 20:50:26','2023-09');

/*Table structure for table `pembelian_detail` */

DROP TABLE IF EXISTS `pembelian_detail`;

CREATE TABLE `pembelian_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_trx` varchar(255) DEFAULT NULL,
  `id_supplier` int(11) DEFAULT NULL,
  `nama_supplier` varchar(255) DEFAULT NULL,
  `id_barang` int(11) NOT NULL,
  `idb` varchar(255) DEFAULT NULL,
  `nama_barang` varchar(255) DEFAULT NULL,
  `beli` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `tgl_input` date DEFAULT NULL,
  `periode` varchar(255) DEFAULT NULL,
  `id_member` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `pembelian_detail` */

insert  into `pembelian_detail`(`id`,`no_trx`,`id_supplier`,`nama_supplier`,`id_barang`,`idb`,`nama_barang`,`beli`,`qty`,`total`,`tgl_input`,`periode`,`id_member`,`created_at`) values 
(1,'OR001',1,'Others',2,'1694344814222','Indomie Goreng',2000,1,2000,'2023-09-10','2023-09',1,'2023-09-10 20:50:26'),
(2,'OR001',1,'Others',1,'BR001','Pensil',1000,1,1000,'2023-09-10','2023-09',1,'2023-09-10 20:50:26');

/*Table structure for table `penjualan` */

DROP TABLE IF EXISTS `penjualan`;

CREATE TABLE `penjualan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_trx` varchar(255) DEFAULT NULL,
  `id_member` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `beli` int(11) DEFAULT NULL,
  `total` int(11) NOT NULL,
  `bayar` int(11) NOT NULL,
  `tanggal_input` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `periode` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `penjualan` */

insert  into `penjualan`(`id`,`no_trx`,`id_member`,`id_pelanggan`,`jumlah`,`beli`,`total`,`bayar`,`tanggal_input`,`created_at`,`periode`) values 
(1,'TR001',1,0,3,5000,7500,8000,'2023-09-10','2023-09-10 20:58:45','2023-09');

/*Table structure for table `penjualan_detail` */

DROP TABLE IF EXISTS `penjualan_detail`;

CREATE TABLE `penjualan_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_trx` varchar(255) DEFAULT NULL,
  `id_barang` int(11) NOT NULL,
  `idb` varchar(255) DEFAULT NULL,
  `id_supplier` int(11) DEFAULT NULL,
  `nama_supplier` varchar(255) DEFAULT NULL,
  `nama_barang` varchar(255) DEFAULT NULL,
  `beli` int(11) NOT NULL,
  `jual` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `diskon` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `tgl_input` date DEFAULT NULL,
  `periode` varchar(255) DEFAULT NULL,
  `id_member` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `penjualan_detail` */

insert  into `penjualan_detail`(`id`,`no_trx`,`id_barang`,`idb`,`id_supplier`,`nama_supplier`,`nama_barang`,`beli`,`jual`,`qty`,`diskon`,`total`,`tgl_input`,`periode`,`id_member`,`created_at`) values 
(1,'TR001',2,'1694344814222',1,'Others','Indomie Goreng',2000,3000,2,0,6000,'2023-09-10','2023-09',1,'2023-09-10 20:58:45'),
(2,'TR001',1,'BR001',1,'Others','Pensil',1000,1500,1,0,1500,'2023-09-10','2023-09',1,'2023-09-10 20:58:45');

/*Table structure for table `supplier` */

DROP TABLE IF EXISTS `supplier`;

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_supplier` varchar(255) DEFAULT NULL,
  `alamat_supplier` varchar(255) DEFAULT NULL,
  `telepon_supplier` varchar(25) DEFAULT NULL,
  `email_supplier` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `supplier` */

insert  into `supplier`(`id`,`nama_supplier`,`alamat_supplier`,`telepon_supplier`,`email_supplier`,`created_at`) values 
(1,'Others','-','-','-','2021-10-20 12:56:37');

/*Table structure for table `toko` */

DROP TABLE IF EXISTS `toko`;

CREATE TABLE `toko` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_toko` varchar(255) NOT NULL,
  `alamat_toko` text NOT NULL,
  `tlp` varchar(255) NOT NULL,
  `nama_pemilik` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

/*Data for the table `toko` */

insert  into `toko`(`id`,`nama_toko`,`alamat_toko`,`tlp`,`nama_pemilik`,`logo`) values 
(1,'FOTOCOPY','Bahagia Bekasi','081234567890','Fauzan','1687705807.png');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telepon` varchar(15) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `akses` varchar(11) DEFAULT NULL,
  `active` varchar(11) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`telepon`,`alamat`,`avatar`,`user`,`pass`,`akses`,`active`,`created_at`) values 
(1,'administrator','','','','1660141131.png','adminkasir','$2y$10$yMfjZvgkfu8yXyyFR8WSheJapD7kRSuexMrigrnVFsCHgIB68jt9.','1','1','2022-09-01 22:52:53'),
(3,'Fauzan Falah','dummy@gmail.com','081234567890','Bekasi','1719103551.png','fauzan','$2y$10$hyoqf9ixMQ//qmLXH0OZ5eBE1dzW0TgnHHur9KqOCWfKmbK5VDwqm','5','1','2024-06-23 07:45:51'),
(11,'Anang','','','','avatar.jpg','anang','$2y$10$P3m1LqV3jRf3oBt3e4cCI.bwxUu4TFka4nWV95Of2pk7CHEG6A8Y.','6','1','2023-06-25 22:12:22');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
