-- SQL untuk membuat tabel coffeeshops di PhpMyAdmin
-- Jalankan script ini di PhpMyAdmin untuk membuat struktur database

CREATE TABLE IF NOT EXISTS coffeeshops (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    address TEXT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    rating DECIMAL(3, 2) NOT NULL CHECK (rating >= 0 AND rating <= 5),
    status VARCHAR(50) NOT NULL DEFAULT 'Aktif',
    phone VARCHAR(20),
    category VARCHAR(100),
    kecamatan VARCHAR(100),
    kelurahan VARCHAR(100),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_kecamatan (kecamatan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data dummy untuk testing dengan kategori
INSERT INTO coffeeshops (name, address, latitude, longitude, rating, status, phone, category, kecamatan, kelurahan, description) VALUES
('Kopi Bersaudara', 'Jl. Pendidikan No. 45, Cimahi', -6.8886, 107.5570, 4.5, 'Aktif', '0271-123456', 'Kafe Modern', 'Cimahi Tengah', 'Setiabudhi', 'Kafe modern dengan suasana hangat dan nyaman.'),
('The Coffee House', 'Jl. Raya Cimahi No. 120, Cimahi', -6.8950, 107.5480, 4.7, 'Aktif', '0271-123457', 'Specialty Coffee', 'Cimahi Utara', 'Cibabat', 'Kedai kopi spesial dengan biji kopi pilihan.'),
('Café Indah', 'Jl. Sipakubumen No. 78, Cimahi', -6.8820, 107.5620, 4.6, 'Aktif', '0271-123458', 'Kafe Tradisional', 'Cimahi Selatan', 'Leuwigajah', 'Kafe tradisional dengan cita rasa lokal.'),
('Kopi Nusantara', 'Jl. Kompas No. 32, Cimahi', -6.9000, 107.5500, 4.4, 'Aktif', '0271-123459', 'Warkop', 'Cimahi Utara', 'Cibabat', 'Warkop asli dengan suasana rakyat.'),
('Brew Station', 'Jl. Moch. Toha No. 15, Cimahi', -6.8900, 107.5550, 4.8, 'Aktif', '0271-123460', 'Specialty Coffee', 'Cimahi Tengah', 'Melong', 'Tempat brewing kopi dengan teknik modern.'),
('Warkop Seuseupan', 'Jl. Cipaganti No. 99, Cimahi', -6.8750, 107.5650, 4.3, 'Aktif', '0271-123461', 'Warkop', 'Cimahi Selatan', 'Leuwigajah', 'Warkop seuseupan khas Bandung.'),
('Coffee & Co.', 'Jl. Pasteur No. 67, Cimahi', -6.9050, 107.5420, 4.7, 'Aktif', '0271-123462', 'Kafe Modern', 'Cimahi Utara', 'Sadang Serang', 'Kafe dengan konsep minimalis dan cozy.'),
('Kopitiam', 'Jl. Cikampak No. 45, Cimahi', -6.8850, 107.5700, 4.5, 'Aktif', '0271-123463', 'Specialty Coffee', 'Cimahi Tengah', 'Setiabudhi', 'Kedai kopi dengan interior unik dan instagramable.');
