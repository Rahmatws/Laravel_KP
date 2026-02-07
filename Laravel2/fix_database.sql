-- Auto-fix SQL untuk menambah kolom has_viewed_stock dan reset onboarding
-- Jalankan di phpMyAdmin atau MySQL client

-- 1. Tambah kolom has_viewed_stock jika belum ada
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS has_viewed_stock TINYINT(1) NOT NULL DEFAULT 0 
AFTER has_viewed_details;

-- 2. Hapus admin lain selain rahmat@gmail.com
DELETE FROM users 
WHERE role = 'admin' AND email != 'rahmat@gmail.com';

-- 3. Reset onboarding flags untuk rahmat@gmail.com
UPDATE users 
SET has_imported = 0, 
    has_viewed_details = 0, 
    has_viewed_stock = 0 
WHERE email = 'rahmat@gmail.com';

-- 4. Verifikasi hasil
SELECT id, name, email, role, has_imported, has_viewed_details, has_viewed_stock 
FROM users 
WHERE email = 'rahmat@gmail.com';
