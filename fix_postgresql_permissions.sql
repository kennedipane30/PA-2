-- Jalankan query ini di pgAdmin dengan koneksi sebagai user postgres atau superuser
-- PENTING: Pilih database db_spectaacademy di pgAdmin sebelum menjalankan query ini

-- 1. Berikan semua privilege pada database
GRANT ALL PRIVILEGES ON DATABASE db_spectaacademy TO spekta_user;

-- 2. Berikan privilege pada schema public
GRANT ALL ON SCHEMA public TO spekta_user;
GRANT CREATE ON SCHEMA public TO spekta_user;
GRANT USAGE ON SCHEMA public TO spekta_user;

-- 3. Ubah owner schema public ke spekta_user
ALTER SCHEMA public OWNER TO spekta_user;

-- 4. Berikan privilege pada semua tabel yang ada dan yang akan dibuat
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO spekta_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO spekta_user;
GRANT ALL PRIVILEGES ON ALL FUNCTIONS IN SCHEMA public TO spekta_user;

-- 5. Set default privileges untuk tabel dan sequence yang akan dibuat
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO spekta_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO spekta_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON FUNCTIONS TO spekta_user;

-- 6. Jika masih error, coba ubah owner database
ALTER DATABASE db_spectaacademy OWNER TO spekta_user;
