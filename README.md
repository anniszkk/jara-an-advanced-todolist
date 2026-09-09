# Jara — An Advanced Todolist

Aplikasi web untuk mengelola tugas pribadi dan tim. Pengguna dapat membuat dan mengelompokkan tugas ke dalam beberapa daftar (list/projek), menetapkan prioritas dan tenggat waktu, serta menandai tugas sebagai selesai.

Pemilik daftar dapat mengundang pengguna lain ke dalam daftarnya agar dikerjakan bersama dan memantau progres penyelesaian tugas di daftar tersebut. Admin bertanggung jawab menambah dan menghapus akun pengguna dalam sistem.

Dibuat untuk Praktikum PPK — Pertemuan 2.

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
- [Tim](#tim)

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

Menghapus akun pengguna otomatis menghapus daftar dan tugas miliknya, sesuai kebutuhan SRS002. Ini ditangani di level database lewat `cascadeOnDelete()`, jadi tidak perlu logika tambahan di controller.

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

| Kode | Deskripsi | Acceptance Criteria | PIC |
|---|---|---|---|
| SRS001 | Autentikasi user | Registrasi, login, logout jalan; password ter-hash; halaman internal diproteksi middleware `auth` | A |
| SRS002 | Dashboard admin kelola akun | Admin melihat semua user, bisa tambah & hapus akun; hapus akun ikut menghapus daftar dan tugas miliknya | A |
| SRS003 | CRUD daftar | Buat, lihat, edit, hapus daftar; user tanpa hak akses tidak bisa membuka daftar orang lain | B |
| SRS004 | Keanggotaan & kepemilikan daftar | Pemilik bisa mengundang user lain, mengeluarkan anggota, dan mengalihkan kepemilikan ke salah satu anggota | B |
| SRS005 | CRUD tugas | Tugas punya judul, deskripsi, prioritas, tenggat; bisa ditambah, diedit, dihapus; terikat satu daftar | C |
| SRS006 | Status tugas & progress daftar | Status bisa dipindah belum → dikerjakan → selesai; tiap daftar menampilkan jumlah selesai / total dan persentase yang ikut berubah | C |

Urutan merge mengikuti dependensi: SRS001 → SRS003 → SRS005 → SRS002 → SRS004 → SRS006.

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

## Aturan Kerja Tim

1. **Migration dan model tidak dibuat sendiri.** Semua sudah disiapkan PM di commit awal. Kalau butuh kolom baru di tengah jalan, koordinasi ke PM dulu supaya anggota lain tahu dan ikut menjalankan migration barunya.
2. **Jangan edit migration lama.** Kalau perlu ubah skema, buat migration baru: `php artisan make:migration add_kolom_x_to_xxx_table`. Migration bersifat append-only.
3. **Model daftar bernama `TaskList`, bukan `List`.** `list` adalah keyword PHP dan akan menyebabkan error.
4. **Route ditulis di blok komentar masing-masing** di `routes/web.php`. Jangan menyisipkan route di tengah blok orang lain — ini titik conflict paling sering.
5. **Kerja di branch sendiri.** Jangan `git push origin main`. PM yang menangani merge dan resolve conflict.
6. **Satu commit = satu perubahan logis.** Jangan menggabungkan beberapa fitur dalam satu commit.
7. **Tidak boleh pakai co-author.** Commit harus murni dari yang mengerjakan.
8. **`.env` wajib tetap di-gitignore.** Yang di-commit hanya `.env.example`.
9. **Kerjakan SRS yang ditugaskan saja** — tidak lebih, tidak kurang.

---

## Troubleshooting

**`SQLSTATE ... table already exists`**

Tabel sudah ada di database tapi belum tercatat di tabel `migrations`. Reset total:

```bash
php artisan migrate:fresh
```

**Ada migration baru dari teman setelah `git pull`**

```bash
git pull origin main
php artisan migrate
```

`php artisan migrate` menyimpan catatan migration mana yang sudah dijalankan, jadi aman dijalankan berkali-kali dan tidak akan membuat tabel dobel.

**Database berantakan saat development**

```bash
php artisan migrate:fresh          # drop semua tabel, migrate ulang dari nol
php artisan migrate:fresh --seed   # sekalian isi data contoh dari seeder
```

**Error `Class "List" not found` atau syntax error di model**

Pastikan memakai `TaskList`, bukan `List`.

**Error foreign key saat migrate**

Pastikan urutan timestamp file migration benar: `create_users_table` paling awal, lalu `create_lists_table`, baru `create_tasks_table` dan `create_list_user_table`.

**Perubahan di `.env` tidak terbaca**

Restart server (`Ctrl+C` lalu `php artisan serve` lagi), atau jalankan `php artisan config:clear`.

---

## Tim

| Nama | Peran | SRS |
|---|---|---|
| — | Annis Fakhiroh Akbar | Setup project, migration, model, merge |
| — | Varissa Nabila Kifli | SRS001, SRS002 |
| — | Shafa Aqilla Zahira | SRS003, SRS004 |
| — | Binar Ridha Wiritanaya | SRS005, SRS006 |

Peran PM bergilir tiap pertemuan.