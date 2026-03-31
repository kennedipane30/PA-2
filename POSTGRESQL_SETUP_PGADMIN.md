# Setup PostgreSQL dengan pgAdmin - Spekta Academy

## 📋 Langkah-Langkah Lengkap

### STEP 1: Install PostgreSQL

1. **Download PostgreSQL:**
   - Link: https://www.postgresql.org/download/windows/
   - Pilih versi terbaru (16.x atau 15.x)
   - Download installer Windows

2. **Install PostgreSQL:**
   - Jalankan installer
   - **PENTING:** Catat password yang Anda set untuk user `postgres`!
   - Port default: 5432 (biarkan default)
   - Locale: Default locale
   - Centang "Stack Builder" (optional)
   - Klik Next sampai selesai

3. **Verifikasi Instalasi:**
   - Buka Start Menu
   - Cari "pgAdmin 4"
   - Jika ada, instalasi berhasil!

---

### STEP 2: Buka pgAdmin

1. **Launch pgAdmin:**
   - Buka pgAdmin 4 dari Start Menu
   - Tunggu browser terbuka (pgAdmin berbasis web)

2. **Set Master Password (First Time):**
   - Akan diminta set master password
   - Set password yang mudah diingat
   - Password ini untuk akses pgAdmin, bukan PostgreSQL

3. **Connect ke Server:**
   - Di sidebar kiri, expand "Servers"
   - Klik "PostgreSQL 16" (atau versi Anda)
   - Masukkan password PostgreSQL yang Anda set saat install
   - Centang "Save password" agar tidak perlu input lagi
   - Klik OK

---

### STEP 3: Buat Database

#### Cara 1: Via GUI (Mudah)

1. **Buat Database:**
   - Di sidebar kiri, expand "Servers" → "PostgreSQL 16"
   - Right-click pada "Databases"
   - Pilih "Create" → "Database..."

2. **Isi Form:**
   - **Database:** `spekta_academy`
   - **Owner:** `postgres`
   - **Encoding:** `UTF8`
   - Tab "Definition":
     - Template: `template0`
     - Collation: `en_US.UTF-8` (atau `C`)
     - Character type: `en_US.UTF-8` (atau `C`)
   - Klik "Save"

3. **Verifikasi:**
   - Database `spekta_academy` akan muncul di sidebar
   - Expand untuk melihat Schemas → public → Tables

#### Cara 2: Via Query Tool (Cepat)

1. **Buka Query Tool:**
   - Right-click pada "PostgreSQL 16"
   - Pilih "Query Tool"

2. **Copy-Paste Query Ini:**
   ```sql
   -- Buat database
   CREATE DATABASE spekta_academy
   WITH 
   ENCODING = 'UTF8'
   LC_COLLATE = 'en_US.UTF-8'
   LC_CTYPE = 'en_US.UTF-8'
   TEMPLATE = template0;
   ```

3. **Execute:**
   - Klik tombol "Execute/Refresh" (icon play ▶)
   - Atau tekan F5
   - Jika berhasil, akan muncul "CREATE DATABASE"

4. **Refresh:**
   - Right-click "Databases" → "Refresh"
   - Database `spekta_academy` akan muncul

---

### STEP 4: Konfigurasi Laravel

1. **Edit File .env:**
   - Buka file: `PA-2/SPECTA_ACADEMY/.env`
   - Cari bagian database
   - Ubah menjadi:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=spekta_academy
DB_USERNAME=postgres
DB_PASSWORD=password_anda_disini
```

**PENTING:** Ganti `password_anda_disini` dengan password PostgreSQL Anda!

2. **Save File .env**

---

### STEP 5: Install PHP PostgreSQL Extension

1. **Cek php.ini Location:**
   ```bash
   php --ini
   ```
   Catat lokasi `Loaded Configuration File`

2. **Edit php.ini:**
   - Buka file php.ini dengan text editor (as Administrator)
   - Cari baris: `;extension=pdo_pgsql`
   - Hapus tanda `;` di depannya menjadi: `extension=pdo_pgsql`
   - Cari baris: `;extension=pgsql`
   - Hapus tanda `;` di depannya menjadi: `extension=pgsql`
   - Save file

3. **Restart Terminal/Command Prompt**

4. **Verifikasi:**
   ```bash
   php -m | findstr pgsql
   ```
   Harus muncul: `pdo_pgsql` dan `pgsql`

---

### STEP 6: Jalankan Migration Laravel

1. **Buka Terminal:**
   ```bash
   cd PA-2/SPECTA_ACADEMY
   ```

2. **Test Connection:**
   ```bash
   php artisan db:show
   ```
   Jika berhasil, akan muncul info database PostgreSQL

3. **Jalankan Migration:**
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Tunggu Proses:**
   - Akan membuat 18 tabel
   - Akan insert data roles dan admin
   - Jika berhasil, akan muncul "DONE" untuk setiap tabel

---

### STEP 7: Verifikasi di pgAdmin

1. **Refresh Database:**
   - Di pgAdmin, expand `spekta_academy`
   - Expand `Schemas` → `public` → `Tables`
   - Right-click `Tables` → `Refresh`

2. **Lihat Tabel:**
   - Anda akan melihat 18+ tabel:
     - announcements
     - attendances
     - categories
     - classes
     - enrollments
     - galleries
     - materials
     - otp_codes
     - payments
     - profiles
     - promos
     - questions
     - roles
     - schedules
     - student_answers
     - tryout_results
     - tryouts
     - users

3. **Lihat Data:**
   - Right-click tabel `roles`
   - Pilih "View/Edit Data" → "All Rows"
   - Anda akan melihat 3 roles: admin, teacher, student

4. **Lihat Data Users:**
   - Right-click tabel `users`
   - Pilih "View/Edit Data" → "All Rows"
   - Anda akan melihat admin user

---

### STEP 8: Query Tool di pgAdmin

1. **Buka Query Tool:**
   - Right-click database `spekta_academy`
   - Pilih "Query Tool"

2. **Test Query:**
   ```sql
   -- Lihat semua roles
   SELECT * FROM roles;

   -- Lihat users dengan role
   SELECT u.id, u.name, u.email, r.nama_role 
   FROM users u 
   JOIN roles r ON u.role_id = r.id;

   -- Hitung total tabel
   SELECT COUNT(*) as total_tables
   FROM information_schema.tables
   WHERE table_schema = 'public';
   ```

3. **Execute:**
   - Klik icon play ▶ atau tekan F5
   - Hasil akan muncul di bawah

---

## 🎯 Troubleshooting

### Error: "could not connect to server"

**Penyebab:** PostgreSQL service tidak berjalan

**Solusi:**
1. Buka Services (Win + R → `services.msc`)
2. Cari "postgresql-x64-16" (atau versi Anda)
3. Right-click → Start
4. Set Startup type: Automatic

### Error: "password authentication failed"

**Penyebab:** Password salah di .env

**Solusi:**
1. Cek password PostgreSQL Anda
2. Update di file `.env`
3. Pastikan tidak ada spasi

### Error: "extension pdo_pgsql not found"

**Penyebab:** PHP extension belum aktif

**Solusi:**
1. Edit php.ini
2. Uncomment: `extension=pdo_pgsql` dan `extension=pgsql`
3. Restart terminal

### Error: "database spekta_academy does not exist"

**Penyebab:** Database belum dibuat

**Solusi:**
1. Buka pgAdmin
2. Buat database `spekta_academy`
3. Jalankan migration lagi

---

## 📊 Useful Queries untuk pgAdmin

Copy queries ini ke Query Tool:

```sql
-- 1. Lihat semua tabel
SELECT table_name 
FROM information_schema.tables 
WHERE table_schema = 'public'
ORDER BY table_name;

-- 2. Lihat struktur tabel users
SELECT 
    column_name, 
    data_type, 
    character_maximum_length,
    is_nullable
FROM information_schema.columns
WHERE table_name = 'users'
ORDER BY ordinal_position;

-- 3. Hitung record per tabel
SELECT 
    schemaname,
    tablename,
    n_live_tup as row_count
FROM pg_stat_user_tables
ORDER BY n_live_tup DESC;

-- 4. Lihat foreign keys
SELECT
    tc.table_name, 
    kcu.column_name,
    ccu.table_name AS foreign_table_name,
    ccu.column_name AS foreign_column_name 
FROM information_schema.table_constraints AS tc 
JOIN information_schema.key_column_usage AS kcu
    ON tc.constraint_name = kcu.constraint_name
JOIN information_schema.constraint_column_usage AS ccu
    ON ccu.constraint_name = tc.constraint_name
WHERE tc.constraint_type = 'FOREIGN KEY'
ORDER BY tc.table_name;

-- 5. Lihat indexes
SELECT
    tablename,
    indexname,
    indexdef
FROM pg_indexes
WHERE schemaname = 'public'
ORDER BY tablename, indexname;

-- 6. Database size
SELECT 
    pg_size_pretty(pg_database_size('spekta_academy')) as database_size;

-- 7. Table sizes
SELECT
    schemaname,
    tablename,
    pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) AS size
FROM pg_tables
WHERE schemaname = 'public'
ORDER BY pg_total_relation_size(schemaname||'.'||tablename) DESC;
```

---

## 🔐 Create User Database (Optional - Untuk Keamanan)

Jika ingin membuat user khusus (bukan pakai `postgres`):

```sql
-- 1. Buat user baru
CREATE USER spekta_user WITH PASSWORD 'spekta_password_123';

-- 2. Beri akses ke database
GRANT ALL PRIVILEGES ON DATABASE spekta_academy TO spekta_user;

-- 3. Connect ke database spekta_academy dulu
\c spekta_academy

-- 4. Beri akses ke semua tabel
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO spekta_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO spekta_user;

-- 5. Set default privileges untuk tabel baru
ALTER DEFAULT PRIVILEGES IN SCHEMA public 
GRANT ALL ON TABLES TO spekta_user;

ALTER DEFAULT PRIVILEGES IN SCHEMA public 
GRANT ALL ON SEQUENCES TO spekta_user;
```

Kemudian update `.env`:
```env
DB_USERNAME=spekta_user
DB_PASSWORD=spekta_password_123
```

---

## 📝 Backup & Restore

### Backup via pgAdmin:

1. Right-click database `spekta_academy`
2. Pilih "Backup..."
3. Filename: `backup_spekta_academy.sql`
4. Format: Plain
5. Klik "Backup"

### Restore via pgAdmin:

1. Right-click database `spekta_academy`
2. Pilih "Restore..."
3. Pilih file backup
4. Klik "Restore"

### Backup via Command Line:

```bash
pg_dump -U postgres -d spekta_academy -f backup.sql
```

### Restore via Command Line:

```bash
psql -U postgres -d spekta_academy -f backup.sql
```

---

## ✅ Checklist Setup

- [ ] PostgreSQL terinstall
- [ ] pgAdmin bisa dibuka
- [ ] Database `spekta_academy` sudah dibuat
- [ ] File `.env` sudah dikonfigurasi
- [ ] PHP extension `pdo_pgsql` aktif
- [ ] Migration berhasil dijalankan
- [ ] 18 tabel terlihat di pgAdmin
- [ ] Data roles dan users ada
- [ ] Query tool bisa digunakan

---

## 🚀 Next Steps

Setelah database PostgreSQL ready:

1. ✅ Database sudah jalan
2. ⏳ Start backend server: `php artisan serve`
3. ⏳ Buat API endpoints (register, login, OTP)
4. ⏳ Test dari Flutter app

---

## 📞 Quick Reference

**pgAdmin Login:**
- Master Password: (yang Anda set saat first time)
- PostgreSQL Password: (yang Anda set saat install)

**Database Info:**
- Host: 127.0.0.1
- Port: 5432
- Database: spekta_academy
- Username: postgres
- Password: (password Anda)

**Laravel .env:**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=spekta_academy
DB_USERNAME=postgres
DB_PASSWORD=your_password_here
```

**Useful Commands:**
```bash
# Test connection
php artisan db:show

# Run migration
php artisan migrate:fresh --seed

# Check tables
php artisan db:table users

# Tinker
php artisan tinker
```

---

**Selamat! Database PostgreSQL Anda siap digunakan! 🎉**

Lanjut ke pembuatan API endpoints? 🚀
