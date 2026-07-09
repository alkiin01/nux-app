# NUX App

Portal ERP Epicor internal berbasis Laravel untuk operasional manufaktur, terintegrasi dengan backend Epicor ERP. Aplikasi ini menangani proses approval dokumen, delivery confirmation, scheduling, shipment, receipt, dan proses inventory terkait.

## Ringkasan Fitur

- Approval Purchase Order (PO)
- Approval Purchase Requisition (PR)
- Approval Surat Jalan (SJ)
- Delivery Confirmation
- Shipment Preparation (FG dan RM)
- Customer Shipment
- Production Schedule
- Time Entry
- Receipt Entry dan sinkronisasi status ke ERP
- Ekspor data (Excel) dan cetak dokumen/label (PDF)

## Teknologi

- Backend: Laravel 9, PHP 8.0+
- Database: MySQL
- Frontend build: Laravel Mix 6, Bootstrap 5, SweetAlert2
- Integrasi API: Guzzle HTTP Client
- PDF: TCPDF
- Excel: PhpSpreadsheet
- QR Code: simplesoftwareio/simple-qrcode
- Auth API token: Laravel Sanctum

## Kebutuhan Sistem

- PHP 8.0 atau lebih baru
- Composer
- Node.js + npm
- MySQL
- XAMPP (direkomendasikan untuk local run sesuai pola tim)

## Menjalankan Proyek Secara Lokal

### 1. Clone dan masuk folder proyek

```bash
git clone <repo-url>
cd nux-app
```

### 2. Install dependency backend

```bash
composer install
```

### 3. Install dependency frontend

```bash
npm install
```

### 4. Siapkan file environment

Jika file `.env.example` tersedia:

```bash
copy .env.example .env
```

Jika `.env.example` tidak tersedia di repository ini, minta template `.env` dari tim internal lalu simpan sebagai `.env`.

### 5. Generate app key

```bash
php artisan key:generate
```

### 6. Konfigurasi database di `.env`

Atur minimal parameter berikut:

- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

### 7. Jalankan migrasi (opsional, sesuai lingkungan)

```bash
php artisan migrate
```

Catatan: pada beberapa environment internal, skema database sudah disediakan oleh tim DBA sehingga migrasi tidak selalu dijalankan lokal.

### 8. Build aset frontend

```bash
npm run dev
```

Untuk development mode watch:

```bash
npm run watch
```

Untuk build production:

```bash
npm run prod
```

### 9. Jalankan aplikasi

Project ini umumnya dijalankan lewat XAMPP (Apache) dengan lokasi folder:

`c:/xampp/htdocs/nux-app`

Akses melalui browser:

`http://localhost/nux-app/public`

## Perintah Penting

```bash
# Menampilkan daftar route
php artisan route:list

# Menjalankan test
php artisan test

# Alternatif test
vendor/bin/phpunit
```

## Struktur Proyek (Ringkas)

- `app/Http/Controllers/`: controller bisnis utama
- `app/Http/Controllers/Api/`: endpoint/proxy integrasi ERP
- `app/Models/`: model berbasis query raw
- `resources/views/`: Blade templates (beserta banyak inline JS)
- `routes/web.php`: route web utama (auth-gated)
- `routes/api.php`: route API terbatas
- `database/migrations/`: histori skema
- `config/`: konfigurasi aplikasi

## Konvensi Kode di Proyek Ini

- Banyak query menggunakan `DB::table()` / `DB::select()` (bukan pola Eloquent penuh).
- Sebagian besar logika interaksi UI berada di Blade + JavaScript inline.
- Naming route banyak mengikuti pola `module.action`.
- Menu dan akses berbasis data menu di database (RBAC dinamis).

## Integrasi ERP

- Integrasi ERP dilakukan melalui endpoint internal dan controller API proyek.
- Host API saat ini dapat dilihat pada method `get_host_api()` di base controller.
- Di source saat ini, host API mengarah ke:

`https://localhost:7263/`

Pastikan endpoint ini sesuai environment Anda (development, staging, production).

## Kolaborasi Dengan Repository EPAPI (Detail)

Secara praktik, NUX App dan EPAPI berjalan sebagai dua repository yang saling melengkapi:

- NUX App: aplikasi portal ERP (UI, workflow approval, proses bisnis).
- EPAPI: backend API yang menangani operasi ERP inti.

NUX App mengirim request HTTP ke EPAPI untuk operasi tertentu, lalu meneruskan hasilnya ke layer UI atau proses bisnis internal.

### Alur Integrasi Tingkat Tinggi

1. User melakukan aksi di UI NUX App (misal approval, update PO, atau shipment action).
2. Controller NUX App memvalidasi input.
3. NUX App memanggil endpoint EPAPI melalui host dari `get_host_api()`.
4. EPAPI memproses logika ERP dan mengembalikan response.
5. NUX App memetakan response ke JSON untuk frontend atau ke status dokumen internal.

### Endpoint Kolaborasi yang Terlihat di Kode Saat Ini

Endpoint API di NUX App (incoming dari client/internal service):

- `POST /api/purchase-order/update-header`
- `POST /api/purchase-order/update-detail`
- `POST /api/update-delivery-status`
- `POST /api/shipment/ship-complete`

Contoh pemetaan ke EPAPI (outgoing dari NUX App):

- `POST {host_api}/PO/UpdatePOHeader`
- `POST {host_api}/PO/UpdatePODetail`

`{host_api}` saat ini diambil dari `Controller::get_host_api()`.

### Kontrak Antar-Repo yang Harus Sinkron

Saat ada perubahan endpoint di EPAPI, item berikut harus disinkronkan di NUX App:

- Path endpoint
- HTTP method
- Nama field request body
- Tipe data field (string, numeric, date)
- Format response sukses dan error
- Kode status HTTP

Jika salah satu berubah tanpa sinkronisasi, biasanya gejala yang muncul adalah validasi gagal, field tidak terbaca, atau request 500 dari proxy layer.

### Workflow Pengembangan Dua Repository

Untuk perubahan yang menyentuh integrasi NUX App + EPAPI, disarankan:

1. Finalkan kontrak API (request/response) di EPAPI terlebih dulu.
2. Update adapter/pemanggilan API pada controller NUX App.
3. Uji endpoint langsung via Postman/cURL.
4. Uji end-to-end dari UI NUX App.
5. Dokumentasikan perubahan kontrak di README atau changelog internal.

### Checklist Saat Deploy Perubahan Integrasi

- Base URL `host_api` sesuai environment deploy.
- Endpoint baru di EPAPI sudah aktif dan dapat diakses dari server NUX App.
- Kredensial/secret tidak di-hardcode.
- Timeout dan error handling sudah sesuai SLA internal.
- Log error mudah ditelusuri (cek `storage/logs`).

### Catatan Keamanan

Jangan menyimpan kredensial API langsung di source code. Gunakan environment variable (`.env`) untuk data sensitif dan load melalui config aplikasi.

## Modul Utama yang Umum Dipakai

- PO Approval
- PR Approval
- SJ Approval
- Delivery Confirmation
- Shipment Preparation
- Customer Shipment
- Production Schedule
- Time Entry
- Receipt Entry

## Catatan Penting dan Pitfall

- Repository mengandung file backup seperti `.bak` dan `- Copy.bak`; file tersebut bukan source aktif.
- Terdapat indikasi route duplikat di beberapa area; selalu validasi dengan `php artisan route:list` saat menambah route baru.
- Ada lebih dari satu package captcha yang terpasang; cek implementasi form sebelum menambah validasi captcha baru.
- Test coverage masih minim; lakukan verifikasi manual untuk flow kritikal setelah perubahan.

## Troubleshooting Singkat

### Aset frontend tidak ter-update

Jalankan ulang:

```bash
npm run dev
```

Atau mode watch:

```bash
npm run watch
```

### Error koneksi database

- Validasi konfigurasi DB di `.env`
- Pastikan service MySQL berjalan
- Pastikan user DB punya akses ke schema target

### Integrasi ERP gagal

- Cek konfigurasi host API di base controller
- Pastikan service ERP endpoint sedang aktif
- Cek log Laravel pada folder `storage/logs`

## Kontribusi Internal

Sebelum membuat perubahan besar:

1. Validasi route agar tidak bentrok.
2. Uji alur kritikal modul terkait secara manual.
3. Pastikan build aset berhasil.
4. Tambahkan test jika memungkinkan.

## Lisensi

Proyek ini bersifat internal perusahaan.
