-- Grizm Store Full Database Schema Setup

-- 1. KULLANICILAR TABLOSU (Admin ve Müşteri Girişi İçin)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) DEFAULT 'user', -- 'admin' veya 'user'
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. ÜRÜNLER TABLOSU (Genel ve VIP/Tek Alımlık Ürünler)
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `description` TEXT,
  `image` VARCHAR(255) NOT NULL,
  `token` VARCHAR(100) DEFAULT NULL,    -- VIP link için gizli anahtar
  `is_single` TINYINT(1) DEFAULT 0,     -- 1 ise ana sayfada gizlenir
  `is_sold` TINYINT(1) DEFAULT 0,       -- 1 ise satın alınmış ve kapanmıştır
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. ANA SİPARİŞLER TABLOSU
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `total_price` DECIMAL(10,2) NOT NULL,
  `address` TEXT NOT NULL,
  `status` VARCHAR(50) DEFAULT 'Beklemede', -- 'Beklemede', 'Onaylandı', 'Reddedildi'
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. SİPARİŞ KALEMLERİ TABLOSU (Hangi siparişte hangi ürünler var?)
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;