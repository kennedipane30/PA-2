-- ============================================
-- SPEKTA ACADEMY DATABASE SETUP
-- ============================================
-- Pilih salah satu: MySQL atau PostgreSQL
-- ============================================

-- ============================================
-- UNTUK MYSQL
-- ============================================

-- 1. Buat Database
CREATE DATABASE IF NOT EXISTS spekta_academy
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- 2. Gunakan Database
USE spekta_academy;

-- 3. Buat User (Optional - untuk keamanan)
CREATE USER IF NOT EXISTS 'spekta_user'@'localhost' IDENTIFIED BY 'spekta_password_123';
GRANT ALL PRIVILEGES ON spekta_academy.* TO 'spekta_user'@'localhost';
FLUSH PRIVILEGES;

-- 4. Verifikasi Database
SHOW DATABASES LIKE 'spekta_academy';

-- 5. Cek Tables (setelah migration)
SHOW TABLES;

-- 6. Cek Data Roles
SELECT * FROM roles;

-- 7. Cek Data Users
SELECT u.id, u.name, u.email, r.nama_role 
FROM users u 
JOIN roles r ON u.role_id = r.id;

-- ============================================
-- UNTUK POSTGRESQL
-- ============================================

-- 1. Buat Database
CREATE DATABASE spekta_academy
WITH 
ENCODING = 'UTF8'
LC_COLLATE = 'en_US.UTF-8'
LC_CTYPE = 'en_US.UTF-8'
TEMPLATE = template0;

-- 2. Connect ke Database
\c spekta_academy

-- 3. Buat User (Optional - untuk keamanan)
CREATE USER spekta_user WITH PASSWORD 'spekta_password_123';
GRANT ALL PRIVILEGES ON DATABASE spekta_academy TO spekta_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO spekta_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO spekta_user;

-- 4. Verifikasi Database
\l spekta_academy

-- 5. Cek Tables (setelah migration)
\dt

-- 6. Cek Data Roles
SELECT * FROM roles;

-- 7. Cek Data Users
SELECT u.id, u.name, u.email, r.nama_role 
FROM users u 
JOIN roles r ON u.role_id = r.id;

-- ============================================
-- QUERY BERGUNA UNTUK DEVELOPMENT
-- ============================================

-- Cek semua tabel dan jumlah record
SELECT 
    table_name,
    (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'spekta_academy' AND table_name = t.table_name) as record_count
FROM information_schema.tables t
WHERE table_schema = 'spekta_academy'
ORDER BY table_name;

-- Cek struktur tabel users
DESCRIBE users; -- MySQL
-- atau
\d users -- PostgreSQL

-- Hapus semua data (HATI-HATI!)
-- Jangan jalankan di production!
SET FOREIGN_KEY_CHECKS = 0; -- MySQL only
TRUNCATE TABLE users;
TRUNCATE TABLE roles;
TRUNCATE TABLE profiles;
-- ... dst untuk semua tabel
SET FOREIGN_KEY_CHECKS = 1; -- MySQL only

-- Drop database (HATI-HATI!)
-- DROP DATABASE spekta_academy;

-- ============================================
-- BACKUP DATABASE
-- ============================================

-- MySQL Backup (via command line)
-- mysqldump -u root -p spekta_academy > backup_spekta_academy.sql

-- MySQL Restore
-- mysql -u root -p spekta_academy < backup_spekta_academy.sql

-- PostgreSQL Backup (via command line)
-- pg_dump -U postgres spekta_academy > backup_spekta_academy.sql

-- PostgreSQL Restore
-- psql -U postgres spekta_academy < backup_spekta_academy.sql

-- ============================================
-- TESTING QUERIES
-- ============================================

-- Insert test student
INSERT INTO roles (nama_role, created_at, updated_at) 
VALUES ('student', NOW(), NOW());

INSERT INTO users (role_id, name, email, password, is_active, email_verified, created_at, updated_at)
VALUES (
    (SELECT id FROM roles WHERE nama_role = 'student'),
    'Test Student',
    'student@test.com',
    '$2y$12$abcdefghijklmnopqrstuvwxyz', -- bcrypt hash
    1,
    1,
    NOW(),
    NOW()
);

-- Cek total users per role
SELECT r.nama_role, COUNT(u.id) as total_users
FROM roles r
LEFT JOIN users u ON r.id = u.role_id
GROUP BY r.id, r.nama_role;

-- Cek kelas dengan jumlah siswa
SELECT 
    c.nama_kelas,
    c.enrolled_count,
    c.kapasitas,
    (c.kapasitas - c.enrolled_count) as sisa_slot
FROM classes c
ORDER BY c.nama_kelas;

-- Cek pembayaran pending
SELECT 
    u.name,
    c.nama_kelas,
    p.total,
    p.status,
    p.created_at
FROM payments p
JOIN users u ON p.user_id = u.id
JOIN classes c ON p.class_id = c.id
WHERE p.status = 'pending'
ORDER BY p.created_at DESC;

-- Cek try out results
SELECT 
    u.name,
    t.judul,
    tr.nilai,
    tr.benar,
    tr.salah,
    tr.is_passed
FROM tryout_results tr
JOIN users u ON tr.user_id = u.id
JOIN tryouts t ON tr.tryout_id = t.id
ORDER BY tr.nilai DESC;
