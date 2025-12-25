# Panduan Deployment Production - SimpleAkunting v3.1

## Hosting: ARENHOST.id
**User:** `simpleak`  
**Domain:** `v31.simpleakunting.my.id`  
**Folder Aplikasi:** `/home/simpleak/V31simpleakunting`

---

## 1. Struktur Folder di Hosting

```
/home/simpleak/
├── public_html/                    ← Document root utama (*.simpleakunting.my.id)
├── V31simpleakunting/              ← FOLDER APLIKASI LARAVEL
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/                     ← DOCUMENT ROOT SUBDOMAIN v31
│   │   ├── index.php
│   │   ├── .htaccess
│   │   ├── css/
│   │   └── js/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   └── .env
```

---

## 2. Konfigurasi Subdomain (PENTING!)

### Di cPanel ARENHOST:

1. Buka **Subdomains**
2. Cari subdomain `v31.simpleakunting.my.id`
3. Klik **Manage** atau **Edit**
4. Ubah **Document Root** menjadi:
   ```
   /home/simpleak/V31simpleakunting/public
   ```
5. Klik **Save**

> ⚠️ **PENTING:** Document Root harus mengarah ke folder `public`, BUKAN folder utama aplikasi!

Setelah diubah, akses langsung: `http://v31.simpleakunting.my.id/` (tanpa /public/)

---

## 3. Konfigurasi .env

Edit file `/home/simpleak/V31simpleakunting/.env`:

```env
APP_NAME="Simple Akunting v3.1"
APP_ENV=production
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
APP_DEBUG=false
APP_URL=http://v31.simpleakunting.my.id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=simpleak_v31
DB_USERNAME=simpleak_user
DB_PASSWORD=5@8@12Yaa

SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

---

## 4. Set Permission

Via File Manager (klik kanan → Change Permissions) atau SSH:

```bash
chmod -R 775 /home/simpleak/V31simpleakunting/storage
chmod -R 775 /home/simpleak/V31simpleakunting/bootstrap/cache
```

---

## 5. Jalankan Migration & Seeder

### Via SSH:
```bash
cd /home/simpleak/V31simpleakunting
php artisan migrate --force
php artisan db:seed --class=CoaDagangSeeder
# atau
php artisan db:seed --class=CoaSimpanPinjamSeeder
```

### Alternatif Via Web (jika tidak ada SSH):

Tambahkan sementara di `routes/web.php`:
```php
Route::get('/run-setup', function() {
    Artisan::call('migrate', ['--force' => true]);
    Artisan::call('db:seed', ['--class' => 'CoaDagangSeeder']);
    return 'Setup selesai!';
});
```

Akses: `http://v31.simpleakunting.my.id/run-setup`  
**Hapus route setelah selesai!**

---

## 6. Optimasi Production

```bash
cd /home/simpleak/V31simpleakunting
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 7. Checklist

- [ ] Subdomain document root → `/home/simpleak/V31simpleakunting/public`
- [ ] File `.env` sudah dikonfigurasi
- [ ] `APP_DEBUG=false`
- [ ] Database terkoneksi
- [ ] Permission storage & cache = 775
- [ ] Migration berhasil
- [ ] COA Seeder dijalankan
- [ ] Bisa login tanpa error

---

## 8. Troubleshooting

### Masih muncul /public/ di URL
- Pastikan Document Root subdomain sudah diset ke folder `public`
- Clear cache browser (Ctrl+Shift+R)

### Error 500
```bash
cat /home/simpleak/V31simpleakunting/storage/logs/laravel.log
```

### Error Permission Denied
```bash
chmod -R 775 storage bootstrap/cache
```

### Clear All Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 9. Update dari GitHub

```bash
cd /home/simpleak/V31simpleakunting
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
