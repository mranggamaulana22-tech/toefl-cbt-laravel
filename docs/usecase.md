# Use Case Diagram Sistem TOEFL CBT

## Contoh Kasus: Sistem TOEFL CBT

**Actor:**
- Pengunjung / Calon Mahasiswa
- Mahasiswa
- Admin

**Use Case:**
- Pengunjung / Calon Mahasiswa dapat melakukan **Register**, **Login**, **Lupa Password**, dan **Verifikasi Email**.
- Mahasiswa dapat melakukan **Lihat Dashboard**, **Mulai Ujian**, **Kerjakan Ujian**, **Kirim Ujian**, **Mulai Latihan**, **Kerjakan Latihan**, **Lihat Riwayat Ujian**, **Lihat Riwayat Latihan**, **Lihat Analisis AI**, **Lihat Review Latihan**, **Unduh Sertifikat**, dan **Kelola Profil**.
- Admin dapat melakukan **Kelola Soal Ujian**, **Ekspor Soal Ujian**, **Kelola Soal Latihan**, **Ekspor Soal Latihan**, **Lihat Riwayat Latihan Mahasiswa**, **Lihat Gradebook**, **Kelola Data Mahasiswa**, **Buka / Tutup Sesi Ujian**, dan **Kelola Profil**.

## Penjelasan

Use Case Diagram adalah diagram UML (Unified Modeling Language) yang menggambarkan interaksi antara pengguna (disebut *actor*) dengan sistem. Diagram ini fokus pada apa yang bisa dilakukan oleh sistem dari sudut pandang pengguna, bukan pada bagaimana sistem tersebut diimplementasikan.

## Ringkasan Use Case per Actor

### 1. Pengunjung / Calon Mahasiswa
- Register akun
- Login ke sistem
- Lupa password
- Reset password
- Verifikasi email

### 2. Mahasiswa
- Melihat dashboard
- Mengikuti ujian TOEFL
- Mengikuti latihan TOEFL
- Melihat hasil ujian
- Melihat riwayat ujian
- Melihat riwayat latihan
- Melihat analisis AI untuk hasil ujian
- Melihat review AI pada soal latihan
- Mengunduh sertifikat
- Mengubah profil akun
- Mengubah password
- Menghapus akun

### 3. Admin
- Mengelola bank soal ujian
- Mengelola bank soal latihan
- Menambahkan, mengubah, dan menghapus soal
- Mengekspor data soal ke CSV
- Melihat riwayat latihan mahasiswa
- Melihat gradebook / laporan hasil ujian
- Melihat daftar mahasiswa
- Menghapus data mahasiswa
- Membuka dan menutup sesi ujian
- Mengubah profil admin
- Mengubah password admin
- Menghapus akun admin

## Versi Diagram (PlantUML)

```plantuml
@startuml
left to right direction

actor "Pengunjung / Calon Mahasiswa" as Guest
actor Mahasiswa
actor Admin

rectangle "Sistem TOEFL CBT" {
  usecase "Register" as UC1
  usecase "Login" as UC2
  usecase "Lupa Password" as UC3
  usecase "Verifikasi Email" as UC4

  usecase "Lihat Dashboard" as UC5
  usecase "Mulai Ujian" as UC6
  usecase "Kerjakan Ujian" as UC7
  usecase "Kirim Ujian" as UC8
  usecase "Mulai Latihan" as UC9
  usecase "Kerjakan Latihan" as UC10
  usecase "Lihat Riwayat Ujian" as UC11
  usecase "Lihat Riwayat Latihan" as UC12
  usecase "Lihat Analisis AI" as UC13
  usecase "Lihat Review Latihan" as UC14
  usecase "Unduh Sertifikat" as UC15
  usecase "Kelola Profil" as UC16

  usecase "Kelola Soal Ujian" as UC17
  usecase "Ekspor Soal Ujian" as UC18
  usecase "Kelola Soal Latihan" as UC19
  usecase "Ekspor Soal Latihan" as UC20
  usecase "Lihat Riwayat Latihan Mahasiswa" as UC21
  usecase "Lihat Gradebook" as UC22
  usecase "Kelola Data Mahasiswa" as UC23
  usecase "Buka / Tutup Sesi Ujian" as UC24
}

Guest --> UC1
Guest --> UC2
Guest --> UC3
Guest --> UC4

Mahasiswa --> UC5
Mahasiswa --> UC6
Mahasiswa --> UC7
Mahasiswa --> UC8
Mahasiswa --> UC9
Mahasiswa --> UC10
Mahasiswa --> UC11
Mahasiswa --> UC12
Mahasiswa --> UC13
Mahasiswa --> UC14
Mahasiswa --> UC15
Mahasiswa --> UC16

Admin --> UC16
Admin --> UC17
Admin --> UC18
Admin --> UC19
Admin --> UC20
Admin --> UC21
Admin --> UC22
Admin --> UC23
Admin --> UC24

@enduml
```

## Catatan

- Jika dokumen ini dipakai untuk laporan, Anda bisa menampilkan diagram PlantUML di atas sebagai versi visualnya.
- Jika ingin versi yang lebih sederhana, actor dapat dipadatkan menjadi 3: **Guest**, **Mahasiswa**, dan **Admin**.
- Jika ingin versi lebih lengkap, aktor eksternal seperti layanan AI dapat ditambahkan sebagai actor tambahan untuk proses analisis dan review otomatis.