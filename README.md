# Jara — An Advanced Todolist

Aplikasi web untuk mengelola tugas pribadi dan tim. Pengguna dapat membuat dan mengelompokkan tugas ke dalam beberapa daftar (list/projek), menetapkan prioritas dan tenggat waktu, serta menandai tugas sebagai selesai.

Pemilik daftar dapat mengundang pengguna lain ke dalam daftarnya agar dikerjakan bersama dan memantau progres penyelesaian tugas di daftar tersebut. Admin bertanggung jawab menambah dan menghapus akun pengguna dalam sistem.

Dibuat untuk Praktikum PPK — Pertemuan 2 & 3.

---

## Daftar Isi

- [Fitur](#fitur)
- [Stack](#stack)
- [Kebutuhan Sistem](#kebutuhan-sistem)
- [Instalasi](#instalasi)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Struktur Database](#struktur-database)
- [Model & Relasi](#model--relasi)
- [Peran Pengguna](#peran-pengguna)
- [Daftar SRS & Pembagian Tugas](#daftar-srs--pembagian-tugas)
- [Alur Kerja Git](#alur-kerja-git)
- [Aturan Kerja Tim](#aturan-kerja-tim)
- [Troubleshooting](#troubleshooting)
- [Peran Praktikum 2](#peran-praktikum-2)
- [Peran Praktikum 3](#peran-praktikum-3)

---

## Fitur

- Registrasi, login, dan logout pengguna
- Membuat, mengubah, dan menghapus daftar tugas (list/projek)
- Membuat tugas dengan judul, deskripsi, prioritas, dan tenggat waktu
- Mengubah status tugas: belum → dikerjakan → selesai
- Mengundang pengguna lain sebagai anggota daftar
- Mengalihkan kepemilikan daftar ke anggota lain
- Ringkasan progres penyelesaian tugas per daftar
- Dashboard admin untuk menambah dan menghapus akun pengguna
- Pembuatan daftar dengan penetapan pemilik otomatis
- Penghapusan daftar beserta seluruh tugas dan keanggotaan di dalamnya secara atomic (rollback penuh jika salah satu langkah gagal)
- Penolakan permintaan dari user yang tidak berwenang/tidak memiliki izin
- Validasi input pengguna secara menyeluruh, seluruh query diproses menggunakan query terparameterisasi/prepared statement untuk mencegah SQL Injection

---

## Stack

| Komponen | Teknologi |
|---|---|
| Framework | Laravel |
| Database | MySQL |
| Template engine | Blade |
| Dependency manager | Composer |

---

## Kebutuhan Sistem

- PHP (sesuai versi minimum Laravel yang dipakai)
- Composer
- MySQL / MariaDB (XAMPP, Laragon, atau sejenisnya)
- Git

---

## Instalasi

Jalankan urut dari atas.

**1. Clone repository**

```bash
git clone https://github.com/anniszkk/jara-an-advanced-todolist.git
cd jara-an-advanced-todolist
```

**2. Install dependency**

```bash
composer install
```

**3. Siapkan file environment**

```bash
cp .env.example .env
php artisan key:generate
```

Di Windows PowerShell, ganti `cp` dengan `copy`:

```powershell
copy .env.example .env
```

**4. Buat database kosong di MySQL lokal**

Lewat phpMyAdmin atau client MySQL lain:

```sql
CREATE DATABASE jara_todolist;
```

**5. Sambungkan Laravel ke database**

Buka `.env`, sesuaikan bagian berikut dengan setting MySQL di laptop masing-masing:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jara_todolist
DB_USERNAME=root
DB_PASSWORD=
```

`DB_USERNAME` dan `DB_PASSWORD` boleh berbeda antar anggota tim — file `.env` memang di-gitignore dan tidak pernah disamakan lewat git.

**6. Jalankan migration**

```bash
php artisan migrate
```

Perintah ini membaca semua file di `database/migrations/` dan membuat tabelnya di database kamu. Struktur tabel akan identik dengan anggota tim lain, walaupun database fisiknya terpisah di laptop masing-masing.

---

## Menjalankan Aplikasi

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000` di browser.

Alur development: edit kode → simpan → refresh browser. Tidak perlu restart server untuk perubahan PHP biasa; restart hanya diperlukan kalau mengubah `.env` atau file di `config/`.

Jika project memakai asset build (Tailwind/Vite), buka terminal kedua:

```bash
npm install
npm run dev
```

`npm run build` tidak perlu dijalankan untuk praktikum — itu hanya untuk deploy ke server production.

---

## Struktur Database

Yang di-share lewat Git adalah **file migration**, bukan database fisiknya. Setiap anggota menjalankan migration yang sama di database lokalnya sendiri.

| Tabel | Keterangan |
|---|---|
| `users` | Akun pengguna. Punya kolom `role` bernilai `admin` atau `user` |
| `lists` | Daftar tugas. `owner_id` menunjuk ke pemilik daftar |
| `tasks` | Tugas, terikat ke satu `list_id` |
| `list_user` | Tabel pivot keanggotaan daftar (many-to-many antara user dan list) |

### Kolom penting

**`users`**

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint | Primary key |
| `name` | string | Nama pengguna |
| `email` | string, unique | Email untuk login |
| `role` | enum | `admin` / `user`, default `user` |
| `password` | string | Otomatis di-hash |

**`lists`**

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint | Primary key |
| `name` | string | Nama daftar |
| `description` | text, nullable | Deskripsi opsional |
| `owner_id` | foreign key → `users` | Pemilik daftar, cascade on delete |

**`tasks`**

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint | Primary key |
| `list_id` | foreign key → `lists` | Daftar induk, cascade on delete |
| `title` | string | Judul tugas |
| `description` | text, nullable | Deskripsi opsional |
| `priority` | enum | `rendah` / `sedang` / `tinggi`, default `sedang` |
| `status` | enum | `belum` / `dikerjakan` / `selesai`, default `belum` |
| `due_date` | date, nullable | Tenggat waktu |
| `assignee_id` | foreign key → `users`, nullable | Penanggung jawab, null on delete |

**`list_user`**

| Kolom | Tipe | Keterangan |
|---|---|---|
| `list_id` | foreign key → `lists` | Cascade on delete |
| `user_id` | foreign key → `users` | Cascade on delete |

Kombinasi `list_id` + `user_id` bersifat unique, sehingga satu pengguna tidak bisa diundang dua kali ke daftar yang sama.

### Perilaku cascade

Menghapus akun pengguna otomatis menghapus daftar dan tugas miliknya, sesuai kebutuhan SRS002. Ini ditangani di level database lewat `cascadeOnDelete()`.

Untuk penghapusan daftar (SRS008/SRS009), cascade di level database dipakai sebagai jaring pengaman, tetapi proses penghapusan **tetap wajib dibungkus `DB::transaction()`** di controller supaya seluruh langkah (hapus tugas → hapus keanggotaan → hapus daftar) benar-benar atomic dan bisa di-rollback kalau salah satu langkah gagal — bukan hanya mengandalkan constraint database.

---

## Model & Relasi

| Model | Tabel | Catatan |
|---|---|---|
| `User` | `users` | Bawaan Laravel, ditambah kolom `role` |
| `TaskList` | `lists` | **Bukan** `List` — `list` adalah keyword PHP |
| `Task` | `tasks` | — |

```
User  ──< TaskList        (ownedLists / owner)
User  >──< TaskList       (lists / members, via list_user)
TaskList ──< Task         (tasks / list)
User  ──< Task            (assignee)
```

Method relasi yang tersedia:

- `TaskList::owner()` — pemilik daftar
- `TaskList::members()` — anggota daftar
- `TaskList::tasks()` — tugas dalam daftar
- `Task::list()` — daftar induk
- `Task::assignee()` — penanggung jawab tugas
- `User::ownedLists()` — daftar yang dimiliki
- `User::lists()` — daftar yang diikuti sebagai anggota

---

## Peran Pengguna

| Peran | Hak akses |
|---|---|
| Admin | Melihat semua akun, menambah dan menghapus akun pengguna |
| Pemilik daftar | Mengelola daftar miliknya, mengundang dan mengeluarkan anggota, mengalihkan kepemilikan |
| Anggota daftar | Mengakses dan mengerjakan tugas di daftar yang mengundangnya |

"Pemilik" bukan role global di tabel `users`, melainkan status pada satu daftar tertentu (kolom `owner_id` di tabel `lists`). Satu pengguna bisa jadi pemilik di satu daftar sekaligus anggota biasa di daftar lain.

---

## Daftar SRS & Pembagian Tugas

### Praktikum 2

| Kode | Deskripsi | Acceptance Criteria | PIC |
|---|---|---|---|
| SRS001 | Autentikasi user | Registrasi, login, logout jalan; password ter-hash; halaman internal diproteksi middleware `auth` | A |
| SRS002 | Dashboard admin kelola akun | Admin melihat semua user, bisa tambah & hapus akun; hapus akun ikut menghapus daftar dan tugas miliknya | A |
| SRS003 | CRUD daftar | Buat, lihat, edit, hapus daftar; user tanpa hak akses tidak bisa membuka daftar orang lain | B |
| SRS004 | Keanggotaan & kepemilikan daftar | Pemilik bisa mengundang user lain, mengeluarkan anggota, dan mengalihkan kepemilikan ke salah satu anggota | B |
| SRS005 | CRUD tugas | Tugas punya judul, deskripsi, prioritas, tenggat; bisa ditambah, diedit, dihapus; terikat satu daftar | C |
| SRS006 | Status tugas & progress daftar | Status bisa dipindah belum → dikerjakan → selesai; tiap daftar menampilkan jumlah selesai / total dan persentase yang ikut berubah | C |

Urutan merge mengikuti dependensi: SRS001 → SRS003 → SRS005 → SRS002 → SRS004 → SRS006.

### Praktikum 3

User story tambahan: *"Pengguna dapat membuat daftar tugas baru dengan otomatis menjadi pemiliknya, serta menghapus daftar yang dimilikinya beserta seluruh tugas dan keanggotaan di dalamnya. Tiap proses ini harus berjalan secara atomic sehingga jika salah satu langkah gagal, maka seluruh perubahan dibatalkan. Permintaan dari user yang tidak berwenang/tidak memiliki izin harus ditolak. Seluruh input pengguna wajib divalidasi serta diproses menggunakan query terparameterisasi/prepared statement untuk mencegah SQL Injection."*

| Kode | Deskripsi | Acceptance Criteria | PIC |
|---|---|---|---|
| SRS007 | Pembuatan daftar + penetapan pemilik | Daftar baru otomatis menetapkan user yang membuat sebagai `owner_id` | Varissa |
| SRS008 | Penghapusan daftar milik pengguna | Owner bisa menghapus daftar miliknya; seluruh tugas & keanggotaan di dalamnya ikut terhapus | Varissa |
| SRS009 | Penghapusan tugas + keanggotaan | Penghapusan tugas dan keanggotaan berjalan konsisten, tidak menyisakan data yatim (orphan) | Annis |
| SRS010 | Atomic transaction | Proses hapus daftar/tugas/keanggotaan dibungkus `DB::transaction()`; jika satu langkah gagal, seluruh perubahan di-rollback | Annis |
| SRS011 | Authorization + permission | Setiap request ke resource (list/task/membership) divalidasi kepemilikan/keanggotaan; user tanpa hak akses ditolak (403) | Binar |
| SRS012 | Input validation + SQL Injection prevention | Seluruh input divalidasi lewat `$request->validate()`; seluruh query lewat Eloquent/prepared statement, tidak ada raw query rawan injeksi | Binar |

---

## Alur Kerja Git

**Programmer**

```bash
# 1. Bikin branch sesuai SRS yang dipegang
git checkout -b feature/nama-fitur

# 2. Ngoding sesuai SRS itu saja

# 3. Commit
git add .
git commit -m "feat(scope): deskripsi singkat"

# 4. Push ke branch sendiri, bukan ke main
git push origin feature/nama-fitur
```

**Naming convention branch**

| Prefix | Kapan dipakai | Contoh |
|---|---|---|
| `feature/` | Fitur baru | `feature/crud-daftar` |
| `fix/` | Perbaikan bug non-urgent | `fix/validasi-email` |
| `chore/` | Non-fitur (config, setup) | `chore/setup-project` |

**Format commit message (Conventional Commits)**

```
<type>(<scope>): <deskripsi singkat, imperative, huruf kecil>

<body opsional: apa & kenapa berubah>
```

Type yang sering dipakai: `feat`, `fix`, `docs`, `style`, `refactor`, `chore`.

Contoh:

```bash
git commit -m "feat(task): tambah form buat tugas baru"
git commit -m "fix(list): perbaiki cek hak akses pemilik daftar"
```

**PM — merge di akhir**

```bash
git checkout main
git pull origin main
git fetch origin
git merge origin/feature/nama-fitur
git push origin main
```

Merge satu branch dulu sampai bersih, baru lanjut branch berikutnya.

---

## Peran Praktikum 2

| Nama | Peran | SRS |
|---|---|---|
| Annis Fakhiroh Akbar | Setup project, migration, model, merge | — |
| Varissa Nabila Kifli | Programmer | SRS001, SRS002 |
| Shafa Aqilla Zahira | Programmer | SRS003, SRS004 |
| Binar Ridha Wiritanaya | Programmer | SRS005, SRS006 |

## Peran Praktikum 3

| Nama | Peran | SRS |
|---|---|---|
| Shafa Aqilla Zahira | PM (setup, koordinasi, merge) | — |
| Varissa Nabila Kifli | Programmer | SRS007, SRS008 |
| Annis Fakhiroh Akbar | Programmer | SRS009, SRS010 |
| Binar Ridha Wiritanaya | Programmer | SRS011, SRS012 |
