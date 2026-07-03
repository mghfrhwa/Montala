/*
SQLyog Community v13.3.0 (64 bit)
MySQL - 10.4.32-MariaDB-log : Database - sitalang_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`sitalang_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `sitalang_db`;

/*Table structure for table `bkph` */

DROP TABLE IF EXISTS `bkph`;

CREATE TABLE `bkph` (
  `id_bkph` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kode_bkph` varchar(20) NOT NULL,
  `nama_bkph` varchar(100) NOT NULL,
  `luas_ha` decimal(12,2) NOT NULL DEFAULT 0.00,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_bkph`),
  UNIQUE KEY `kode_bkph` (`kode_bkph`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `bkph` */

insert  into `bkph`(`id_bkph`,`kode_bkph`,`nama_bkph`,`luas_ha`,`latitude`,`longitude`,`created_at`,`updated_at`) values 
(1,'BKPH-KDR','Kediri',12653.70,-7.8167000,111.9333000,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(2,'BKPH-PAC','Pace',11614.14,-7.7333000,111.9000000,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(3,'BKPH-PRE','Pare',10001.49,-7.7500000,112.1333000,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(4,'BKPH-TGA','Tulungagung',14266.60,-8.0667000,111.9000000,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(5,'BKPH-BDG','Bandung',16376.45,-8.1667000,111.7833000,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(6,'BKPH-DNK','Dongko',14786.77,-8.2167000,111.6833000,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(7,'BKPH-KPK','Kampak',15453.90,-8.2333000,111.6167000,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(8,'BKPH-KRG','Karangan',10013.30,-8.1000000,111.7333000,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(9,'BKPH-TRK','Trenggalek',12667.87,-8.0500000,111.7167000,'2026-07-01 13:25:40','2026-07-01 13:25:40');

/*Table structure for table `dokumentasi_foto` */

DROP TABLE IF EXISTS `dokumentasi_foto`;

CREATE TABLE `dokumentasi_foto` (
  `id_foto` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_realisasi` int(10) unsigned NOT NULL,
  `path_foto` varchar(255) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL COMMENT 'Contoh: "Foto sampel pohon" / "Foto Buku Saku Mandor"',
  `latitude` decimal(10,7) DEFAULT NULL COMMENT 'Disiapkan untuk GPS lapangan (rencana pengembangan)',
  `longitude` decimal(10,7) DEFAULT NULL,
  `tanggal_upload` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_foto`),
  KEY `fk_foto_realisasi` (`id_realisasi`),
  CONSTRAINT `fk_foto_realisasi` FOREIGN KEY (`id_realisasi`) REFERENCES `realisasi` (`id_realisasi`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `dokumentasi_foto` */

/*Table structure for table `log_aktivitas` */

DROP TABLE IF EXISTS `log_aktivitas`;

CREATE TABLE `log_aktivitas` (
  `id_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `tanggal_waktu` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_log`),
  KEY `fk_log_user` (`id_user`),
  CONSTRAINT `fk_log_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `log_aktivitas` */

/*Table structure for table `mandor` */

DROP TABLE IF EXISTS `mandor`;

CREATE TABLE `mandor` (
  `id_mandor` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_rph` int(10) unsigned NOT NULL,
  `nama_mandor` varchar(100) NOT NULL,
  `alur_kerja` varchar(10) NOT NULL COMMENT 'Contoh: Alur A, Alur B, Alur C',
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_mandor`),
  KEY `fk_mandor_rph` (`id_rph`),
  CONSTRAINT `fk_mandor_rph` FOREIGN KEY (`id_rph`) REFERENCES `rph` (`id_rph`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `mandor` */

/*Table structure for table `petak` */

DROP TABLE IF EXISTS `petak`;

CREATE TABLE `petak` (
  `id_petak` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_rph` int(10) unsigned NOT NULL,
  `kode_petak` varchar(20) NOT NULL,
  `total_pohon` int(10) unsigned DEFAULT NULL COMMENT 'Perkiraan jumlah pohon di petak (bisa dioverride saat input realisasi)',
  `latitude` decimal(10,7) DEFAULT NULL COMMENT 'Disiapkan untuk GPS per petak (rencana pengembangan)',
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_petak`),
  UNIQUE KEY `uq_petak_per_rph` (`id_rph`,`kode_petak`),
  CONSTRAINT `fk_petak_rph` FOREIGN KEY (`id_rph`) REFERENCES `rph` (`id_rph`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `petak` */

/*Table structure for table `realisasi` */

DROP TABLE IF EXISTS `realisasi`;

CREATE TABLE `realisasi` (
  `id_realisasi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_petak` int(10) unsigned NOT NULL,
  `id_mandor` int(10) unsigned DEFAULT NULL,
  `id_user` int(10) unsigned NOT NULL COMMENT 'KRPH/Asper yang menginput',
  `jumlah_pohon_realisasi` int(10) unsigned NOT NULL,
  `total_pohon_petak` int(10) unsigned NOT NULL,
  `persentase_capaian` decimal(5,2) GENERATED ALWAYS AS (case when `total_pohon_petak` > 0 then round(`jumlah_pohon_realisasi` / `total_pohon_petak` * 100,2) else 0 end) STORED,
  `tanggal_update` date NOT NULL,
  `catatan_lapangan` text DEFAULT NULL,
  `status_validasi` enum('Menunggu','Valid','Tidak Valid') NOT NULL DEFAULT 'Menunggu',
  `alasan_tidak_valid` varchar(255) DEFAULT NULL COMMENT 'Contoh: "Tanpa dokumentasi foto"',
  `versi_input` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'Naik tiap kali data diperbaiki ulang',
  `id_realisasi_sebelumnya` int(10) unsigned DEFAULT NULL COMMENT 'Referensi ke versi sebelumnya jika ini hasil perbaikan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_realisasi`),
  KEY `fk_realisasi_petak` (`id_petak`),
  KEY `fk_realisasi_mandor` (`id_mandor`),
  KEY `fk_realisasi_user` (`id_user`),
  KEY `fk_realisasi_prev` (`id_realisasi_sebelumnya`),
  CONSTRAINT `fk_realisasi_mandor` FOREIGN KEY (`id_mandor`) REFERENCES `mandor` (`id_mandor`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_realisasi_petak` FOREIGN KEY (`id_petak`) REFERENCES `petak` (`id_petak`) ON UPDATE CASCADE,
  CONSTRAINT `fk_realisasi_prev` FOREIGN KEY (`id_realisasi_sebelumnya`) REFERENCES `realisasi` (`id_realisasi`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_realisasi_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `realisasi` */

insert  into `realisasi`(`id_realisasi`,`id_petak`,`id_mandor`,`id_user`,`jumlah_pohon_realisasi`,`total_pohon_petak`,`tanggal_update`,`catatan_lapangan`,`status_validasi`,`alasan_tidak_valid`,`versi_input`,`id_realisasi_sebelumnya`,`created_at`,`updated_at`) values 
(1,1,1,2,92,100,'2026-06-29',NULL,'Valid',NULL,1,NULL,'2026-07-01 13:21:11','2026-07-01 13:21:11'),
(2,2,2,2,45,100,'2026-06-28',NULL,'Valid',NULL,1,NULL,'2026-07-01 13:21:11','2026-07-01 13:21:11'),
(3,3,3,2,88,100,'2026-06-27',NULL,'Valid',NULL,1,NULL,'2026-07-01 13:21:11','2026-07-01 13:21:11'),
(4,4,1,2,60,100,'2026-06-28',NULL,'Tidak Valid','Capaian dimasukkan tanpa dokumentasi foto',1,NULL,'2026-07-01 13:21:11','2026-07-01 13:21:11');

/*Table structure for table `rph` */

DROP TABLE IF EXISTS `rph`;

CREATE TABLE `rph` (
  `id_rph` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_bkph` int(10) unsigned NOT NULL,
  `kode_rph` varchar(20) NOT NULL,
  `nama_rph` varchar(100) NOT NULL,
  `luas_ha` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_rph`),
  UNIQUE KEY `kode_rph` (`kode_rph`),
  KEY `fk_rph_bkph` (`id_bkph`),
  CONSTRAINT `fk_rph_bkph` FOREIGN KEY (`id_bkph`) REFERENCES `bkph` (`id_bkph`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `rph` */

insert  into `rph`(`id_rph`,`id_bkph`,`kode_rph`,`nama_rph`,`luas_ha`,`created_at`,`updated_at`) values 
(1,1,'RPH-KDR-01','Kalipang',2531.00,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(2,1,'RPH-KDR-02','Kanyoran',3484.70,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(3,1,'RPH-KDR-03','Pamongan',1411.30,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(4,1,'RPH-KDR-04','Parang',1776.60,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(5,1,'RPH-KDR-05','Pojok',1303.90,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(6,1,'RPH-KDR-06','Sambiroto',2146.20,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(7,2,'RPH-PAC-01','Bajulan',1516.70,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(8,2,'RPH-PAC-02','Gedangklutuk',2281.80,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(9,2,'RPH-PAC-03','Makuto',2574.10,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(10,2,'RPH-PAC-04','Plangkat',1837.06,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(11,2,'RPH-PAC-05','Salam Judeg',1923.18,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(12,2,'RPH-PAC-06','Sugihan',1481.30,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(13,3,'RPH-PRE-01','Besowo',3537.59,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(14,3,'RPH-PRE-02','Jatirejo',2195.70,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(15,3,'RPH-PRE-03','Kandangan',908.20,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(16,3,'RPH-PRE-04','Manggis',1434.40,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(17,3,'RPH-PRE-05','Pandantoyo',1925.60,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(18,4,'RPH-TGA-01','Gondang',2807.10,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(19,4,'RPH-TGA-02','Jatiwekas',1861.20,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(20,4,'RPH-TGA-03','Karangrejo',2253.00,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(21,4,'RPH-TGA-04','Pagerwejo',5172.20,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(22,4,'RPH-TGA-05','Sendang',2173.10,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(23,5,'RPH-BDG-01','Bandung',2308.70,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(24,5,'RPH-BDG-02','Besuki',4448.60,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(25,5,'RPH-BDG-03','Prigi',5376.14,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(26,5,'RPH-BDG-04','Watulimo',4243.01,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(27,6,'RPH-DNK-01','Banjar',4065.65,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(28,6,'RPH-DNK-02','Dongko Selatan',2428.00,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(29,6,'RPH-DNK-03','Dongko Utara',2160.00,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(30,6,'RPH-DNK-04','Panggul',3456.12,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(31,6,'RPH-DNK-05','Sumberbening',2676.30,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(32,7,'RPH-KPK-01','Kampak Selatan',3313.80,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(33,7,'RPH-KPK-02','Kampak Utara',2474.90,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(34,7,'RPH-KPK-03','Munjungan Barat',3244.90,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(35,7,'RPH-KPK-04','Munjungan Timur',6420.30,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(36,8,'RPH-KRG-01','Gandusari',2394.10,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(37,8,'RPH-KRG-02','Karangan',2669.60,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(38,8,'RPH-KRG-03','Pule',2755.60,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(39,8,'RPH-KRG-04','Tugu',2194.00,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(40,9,'RPH-TRK-01','Bendungan',3740.69,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(41,9,'RPH-TRK-02','Durenan',3191.50,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(42,9,'RPH-TRK-03','Sumurup',2207.40,'2026-07-01 13:25:40','2026-07-01 13:25:40'),
(43,9,'RPH-TRK-04','Trenggalek',3528.28,'2026-07-01 13:25:40','2026-07-01 13:25:40');

/*Table structure for table `target` */

DROP TABLE IF EXISTS `target`;

CREATE TABLE `target` (
  `id_target` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `level_target` enum('BKPH','RPH') NOT NULL,
  `id_bkph` int(10) unsigned DEFAULT NULL COMMENT 'Diisi jika level_target = BKPH',
  `id_rph` int(10) unsigned DEFAULT NULL COMMENT 'Diisi jika level_target = RPH',
  `id_user` int(10) unsigned NOT NULL COMMENT 'KPH yang menetapkan target',
  `periode_bulan` tinyint(3) unsigned NOT NULL COMMENT '1-12',
  `periode_tahun` smallint(5) unsigned NOT NULL,
  `target_persen` decimal(5,2) NOT NULL COMMENT 'Contoh: 70.00 (%)',
  `status_periode` enum('Berlalu','Berjalan','Mendatang') NOT NULL DEFAULT 'Mendatang',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_target`),
  UNIQUE KEY `uq_target_bkph_periode` (`id_bkph`,`periode_bulan`,`periode_tahun`),
  UNIQUE KEY `uq_target_rph_periode` (`id_rph`,`periode_bulan`,`periode_tahun`),
  KEY `fk_target_user` (`id_user`),
  CONSTRAINT `fk_target_bkph` FOREIGN KEY (`id_bkph`) REFERENCES `bkph` (`id_bkph`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_target_rph` FOREIGN KEY (`id_rph`) REFERENCES `rph` (`id_rph`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_target_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON UPDATE CASCADE,
  CONSTRAINT `chk_target_level` CHECK (`level_target` = 'BKPH' and `id_bkph` is not null and `id_rph` is null or `level_target` = 'RPH' and `id_rph` is not null and `id_bkph` is null)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `target` */

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Simpan hash bcrypt/argon2, bukan plain text',
  `role` enum('KPH','KRPH') NOT NULL,
  `id_rph` int(10) unsigned DEFAULT NULL COMMENT 'Wajib diisi jika role = KRPH. NULL untuk role KPH (akses seluruh KPH Kediri)',
  `status_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`),
  KEY `fk_user_rph` (`id_rph`),
  CONSTRAINT `fk_user_rph` FOREIGN KEY (`id_rph`) REFERENCES `rph` (`id_rph`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `chk_user_role_wilayah` CHECK (`role` = 'KRPH' and `id_rph` is not null or `role` = 'KPH')
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `user` */

insert  into `user`(`id_user`,`nama`,`username`,`password`,`role`,`id_rph`,`status_aktif`,`created_at`,`updated_at`) values 
(1,'KPH Kediri','kph.kediri','$2y$10$PLACEHOLDER_HASH_GANTI_DI_APLIKASI','KPH',NULL,1,'2026-07-01 13:21:11','2026-07-01 13:21:11'),
(2,'Asper Kalipang','asper.kalipang','$2y$10$PLACEHOLDER_HASH_GANTI_DI_APLIKASI','KRPH',1,1,'2026-07-01 13:21:11','2026-07-01 13:21:11');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
