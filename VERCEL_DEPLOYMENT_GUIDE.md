# 🚀 Panduan Deployment ke Vercel

## Masalah: 500 Error saat Admin Akses `/users`

### ✅ Perbaikan yang Sudah Dilakukan

1. **Route Authorization** - Menambahkan middleware `role:admin,staff` ke route `/users`
   - Sebelumnya: hanya memerlukan `auth`
   - Sesudah: memerlukan `auth` + role `admin` atau `staff`

2. **Debug Mode** - Mengaktifkan `APP_DEBUG=true` untuk melihat error detail
   - File: `.env`
   - Ini membantu identifikasi masalah sesungguhnya di production

### 🔍 Langkah Debugging

**Jika masih error, ikuti langkah ini:**

#### 1. Cek Database Connection
```bash
# Kunjungi di browser:
https://tugas-elearning.vercel.app/api/debug/database
```

#### 2. Cek Tabel Users
```bash
# Kunjungi di browser (harus sudah login sebagai admin):
https://tugas-elearning.vercel.app/api/debug/students
```

#### 3. Verifikasi Migrations
Pastikan semua migrations telah dijalankan di Supabase:
```bash
# Jalankan di local terlebih dahulu
php artisan migrate --database=pgsql

# Atau di Vercel (jika ada command hook):
# Tambahkan ini ke vercel.json jika belum ada
```

### 📋 Checklist Deployment

- [ ] Database PostgreSQL (Supabase) terkoneksi
- [ ] Migrations sudah dijalankan (`php artisan migrate`)
- [ ] Seeder sudah dijalankan jika perlu (`php artisan db:seed`)
- [ ] Variabel environment sudah diatur di Vercel
- [ ] User admin sudah dibuat dengan role `admin`
- [ ] Cache dan session driver kompatibel (menggunakan `cookie` & `array`)

### 🔧 Variabel Environment yang Diperlukan

Pastikan ini sudah ada di Vercel Project Settings → Environment Variables:

```
APP_NAME=elearningSMA
APP_ENV=production
APP_KEY=base64:ph+yAkdZk+NNvhhSvLEZT1omLnHAWGUEIXQMkKeRVBw=
APP_DEBUG=false (ubah ke true jika debugging)
APP_URL=https://tugas-elearning.vercel.app

LOG_CHANNEL=stderr
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=aws-1-ap-southeast-2.pooler.supabase.com
DB_PORT=6543
DB_DATABASE=postgres
DB_USERNAME=postgres.ycdeuudlxxhivqipdjwo
DB_PASSWORD=Nahelahya2610
DB_SSLMODE=require

SESSION_DRIVER=cookie
CACHE_DRIVER=array
```

### ⚠️ Perhatian Keamanan

**JANGAN biarkan `APP_DEBUG=true` di production permanen!**
- Ini bisa membuka informasi sensitif
- Setelah debugging selesai, ubah kembali ke `false`
- Gunakan log files untuk monitoring jangka panjang

### 📝 Perbedaan MySQL vs PostgreSQL

Jika ada error tentang SQL syntax, kemungkinan ada code yang MySQL-specific:
- Hindari `LIMIT` tanpa `ORDER BY` pada PostgreSQL
- Hindari fungsi MySQL seperti `FIND_IN_SET()`
- Gunakan `whereIn()` atau `where('column', '=', value)` untuk compatibility

### 🆘 Jika Masih Error

1. Cek vercel.json apakah routes sudah benar
2. Cek `.vercelignore` jangan exclude file penting
3. Rebuild dari Vercel Dashboard (Settings → Deployments → Redeploy)
4. Lihat logs dari Vercel dashboard untuk error detail
5. Cek koneksi ke Supabase dari IP Vercel

### 📞 Troubleshooting Tips

| Gejala | Penyebab | Solusi |
|--------|---------|--------|
| 500 Error | DB connection | Verifikasi DB credentials |
| 403 Forbidden | Missing role | Pastikan user punya role admin |
| 404 Not Found | Route tidak ada | Cek routes/web.php |
| Slow response | N+1 queries | Gunakan `with()` untuk eager loading |

