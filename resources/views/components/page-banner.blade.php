{{--
    Komponen: <x-page-banner>

    Tujuan: Gradient header banner (eyebrow text + judul besar + subtitle),
    dengan opsi tombol aksi di sisi kanan (mis. tombol Edit/Kembali di halaman show).

    Cara pakai dasar (form create/edit):
        <x-page-banner
            eyebrow="Form Input"
            title="Rancang soal latihan yang rapi dan mudah dikelola"
            subtitle="Gunakan pilihan kategori yang valid agar data konsisten dengan engine latihan."
        />

    Cara pakai dengan tombol aksi di kanan (halaman detail/show):
        <x-page-banner
            eyebrow="Soal Latihan Detail"
            :title="ucfirst($practiceQuestion->category).' Session'"
            subtitle="Periksa isi soal, jawaban, dan aset pendukung sebelum melakukan perubahan."
        >
            <x-slot:actions>
                <a href="{{ route('admin.practice-questions.edit', $practiceQuestion) }}" class="inline-flex items-center rounded-xl bg-white px-4 py-2 text-sm font-bold text-indigo-700 transition hover:bg-indigo-50 active:scale-95 shadow-sm">
                    Edit Soal
                </a>
                <a href="{{ route('admin.practice-questions.index') }}" class="inline-flex items-center rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/15 active:scale-95">
                    Kembali
                </a>
            </x-slot:actions>
        </x-page-banner>

    Props:
    - eyebrow (string)  : label kecil uppercase di atas judul
    - title (string)    : judul besar banner
    - subtitle (string) : deskripsi singkat di bawah judul (opsional)
    - gradient (string) : kelas gradient tailwind, default indigo -> sky

    Slot:
    - actions : opsional, isi dengan tombol-tombol yang tampil di sisi kanan banner (layout otomatis
                jadi flex row-reverse pada desktop). Jika slot ini tidak diisi, banner tampil seperti biasa.
--}}

@props([
    'eyebrow' => '',
    'title' => '',
    'subtitle' => '',
    'gradient' => 'from-indigo-700 via-indigo-600 to-sky-600',
])

<div {{ $attributes->class(["bg-gradient-to-r $gradient px-6 py-6 text-white md:px-8"]) }}>
    @if(isset($actions))
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div class="animate-fade-in-left">
                @if($eyebrow)
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-100/80">{{ $eyebrow }}</p>
                @endif
                @if($title)
                    <h3 class="mt-2 text-2xl font-black tracking-tight">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="mt-2 text-sm text-indigo-100/90">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="flex flex-wrap gap-2 animate-fade-in-right">
                {{ $actions }}
            </div>
        </div>
    @else
        @if($eyebrow)
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-100/80 animate-fade-in-left">{{ $eyebrow }}</p>
        @endif
        @if($title)
            <h3 class="mt-2 text-2xl font-black tracking-tight animate-fade-in-left">{{ $title }}</h3>
        @endif
        @if($subtitle)
            <p class="mt-2 text-sm text-indigo-100/90 animate-fade-in-left">{{ $subtitle }}</p>
        @endif
    @endif

    {{ $slot ?? '' }}
</div>