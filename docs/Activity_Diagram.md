# Activity Diagram

Activity Diagram digunakan untuk menggambarkan alur kerja sistem, termasuk urutan aktivitas, percabangan keputusan, dan proses yang berjalan paralel. Pada sistem TOEFL Piksi, activity diagram yang paling relevan adalah alur login, alur ujian, alur latihan, dan alur admin.

## 1. Activity Diagram - Login dan Routing Dashboard

Diagram ini menunjukkan alur dasar ketika pengguna membuka sistem, melakukan autentikasi, lalu diarahkan ke dashboard sesuai perannya.

```plantuml
@startuml
title Activity Diagram - Login dan Routing Dashboard

start
:Pengguna membuka halaman login;
:Sistem menampilkan form login;
:Pengguna memasukkan email/username dan password;
:Pengguna menekan tombol Submit;
:Sistem memvalidasi data ke database;

if (Data valid?) then (Ya)
	:Sistem membuat session pengguna;
	:Sistem menentukan role pengguna;
	if (Role pengguna?) then (Admin)
		:Sistem memuat dashboard admin;
	else (Mahasiswa)
		:Sistem memuat dashboard mahasiswa;
	endif
else (Tidak)
	:Sistem menampilkan pesan error;
	:Pengguna kembali ke form login;
endif

stop
@enduml
```

## 2. Activity Diagram - Proses Ujian Mahasiswa

Diagram ini menggambarkan alur utama saat mahasiswa mengerjakan ujian sampai hasil ujian tersimpan dan analisis AI diproses secara otomatis.

```plantuml
@startuml
title Activity Diagram - Proses Ujian Mahasiswa

start
:Mahasiswa membuka dashboard;
:Mahasiswa memilih menu Ujian;
:Sistem mengecek status sesi ujian;

if (Sesi ujian aktif?) then (Ya)
	:Sistem menampilkan daftar/halaman ujian;
	:Mahasiswa memulai ujian;
	:Sistem memuat soal ujian;

	repeat
		:Mahasiswa menjawab soal;
		:Sistem menyimpan jawaban sementara;
	repeat while (Masih ada soal?) is (Ya)

	:Mahasiswa menekan tombol Kirim Ujian;
	:Sistem memvalidasi jawaban dan waktu pengerjaan;
	:Sistem menyimpan hasil ujian;

	fork
		:Sistem mengirim pekerjaan analisis AI ke queue;
		:Scheduler menjalankan proses analisis AI;
		:AI menghasilkan ringkasan dan skor analisis;
		:Sistem menyimpan hasil analisis AI;
	fork again
		:Sistem menyiapkan hasil ujian untuk ditampilkan;
	end fork

	:Sistem menampilkan hasil ujian;
	:Mahasiswa dapat melihat hasil dan mengunduh sertifikat;
else (Tidak)
	:Sistem menampilkan informasi bahwa sesi ujian belum dibuka;
endif

stop
@enduml
```

## 3. Activity Diagram - Proses Latihan dan Review

Diagram ini menunjukkan alur latihan mandiri mahasiswa, termasuk penyimpanan progress, sinkronisasi data, dan review latihan setelah selesai.

```plantuml
@startuml
title Activity Diagram - Proses Latihan dan Review

start
:Mahasiswa membuka dashboard;
:Mahasiswa memilih menu Latihan;
:Sistem menampilkan daftar latihan;
:Mahasiswa memilih latihan;
:Sistem memuat soal latihan;

fork
	:Sistem mengaktifkan autosave progress;
fork again
	:Mahasiswa mulai mengerjakan latihan;
end fork

repeat
	:Mahasiswa menjawab soal latihan;
	fork
		:Autosave progress lokal;
	fork again
		:Sinkronisasi progress ke server;
	end fork
repeat while (Masih ada soal?) is (Ya)

:Mahasiswa menekan selesai;
:Sistem menyimpan hasil latihan;

if (Mahasiswa melihat review?) then (Ya)
	:Sistem menampilkan review latihan;
	:Mahasiswa memeriksa jawaban, pembahasan, dan status progress;
else (Tidak)
	:Sistem menutup sesi latihan;
endif

stop
@enduml
```

## 4. Activity Diagram - Kelola Soal Ujian oleh Admin

Diagram ini menunjukkan alur admin saat menambah, mengubah, atau menghapus soal ujian.

```plantuml
@startuml
title Activity Diagram - Kelola Soal Ujian oleh Admin

start
:Admin membuka halaman pengelolaan soal;
:Sistem menampilkan daftar soal;
:Admin memilih tambah, ubah, atau hapus soal;
:Sistem menampilkan form atau detail soal;

:Admin mengelola soal ujian;
:Sistem memvalidasi input;
:Sistem menyimpan perubahan ke database;
:Sistem memperbarui daftar soal;

stop
@enduml
```

## 5. Activity Diagram - Kelola Soal Latihan oleh Admin

Diagram ini menunjukkan alur admin saat menambah, mengubah, atau menghapus soal latihan.

```plantuml
@startuml
title Activity Diagram - Kelola Soal Latihan oleh Admin

start
:Admin membuka halaman pengelolaan soal;
:Sistem menampilkan daftar soal latihan;
:Admin memilih tambah, ubah, atau hapus soal;
:Sistem menampilkan form atau detail soal;
:Admin mengelola soal latihan;
:Sistem memvalidasi input;
:Sistem menyimpan perubahan ke database;
:Sistem memperbarui daftar soal latihan;

stop
@enduml
```

## 6. Activity Diagram - Ekspor CSV oleh Admin

Diagram ini menunjukkan alur ekspor data yang menghasilkan file CSV.

```plantuml
@startuml
title Activity Diagram - Ekspor CSV oleh Admin

start
:Admin membuka menu ekspor;
:Sistem menampilkan pilihan ekspor;
:Admin memilih jenis data yang diekspor;
:Admin menekan tombol ekspor;
:Sistem mengambil data dari database;
:Sistem membentuk file CSV;
:Sistem mengirim file ke browser;
:Admin mengunduh file CSV;

stop
@enduml
```

## 7. Activity Diagram - Kelola Mahasiswa oleh Admin

Diagram ini menggambarkan alur admin saat melihat dan menghapus data mahasiswa.

```plantuml
@startuml
title Activity Diagram - Kelola Mahasiswa oleh Admin

start
:Admin membuka halaman data mahasiswa;
:Sistem menampilkan daftar mahasiswa;
:Admin memilih salah satu mahasiswa;
:Admin meninjau detail data mahasiswa;

if (Hapus mahasiswa?) then (Ya)
	:Admin menekan tombol hapus;
	:Sistem meminta konfirmasi;
	if (Konfirmasi hapus?) then (Ya)
		:Sistem menghapus data mahasiswa;
		:Sistem memperbarui daftar mahasiswa;
	else (Tidak)
		:Sistem membatalkan penghapusan;
	endif
else (Tidak)
	:Sistem menampilkan kembali daftar mahasiswa;
endif

stop
@enduml
```


