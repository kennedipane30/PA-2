-- JALANKAN QUERY INI DI pgAdmin
-- Login sebagai postgres
-- Pilih database: db_spectaacademy
-- Lalu jalankan semua query di bawah ini:

-- 1. Ubah owner database
ALTER DATABASE db_spectaacademy OWNER TO spekta_user;

-- 2. Ubah owner schema public
ALTER SCHEMA public OWNER TO spekta_user;

-- 3. Berikan semua privilege
GRANT ALL PRIVILEGES ON DATABASE db_spectaacademy TO spekta_user;
GRANT ALL ON SCHEMA public TO spekta_user;
GRANT CREATE ON SCHEMA public TO spekta_user;
GRANT USAGE ON SCHEMA public TO spekta_user;

-- 4. Berikan privilege pada semua objek yang ada
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO spekta_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO spekta_user;
GRANT ALL PRIVILEGES ON ALL FUNCTIONS IN SCHEMA public TO spekta_user;

-- 5. Set default privileges untuk objek yang akan dibuat
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO spekta_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO spekta_user;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON FUNCTIONS TO spekta_user;

-- 6. Berikan SUPERUSER ke spekta_user (untuk development saja)
ALTER USER spekta_user WITH SUPERUSER;
