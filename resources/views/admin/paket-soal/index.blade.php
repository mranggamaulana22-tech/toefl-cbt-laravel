<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-0.5">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-indigo-500">Bank Soal</p>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Paket Soal</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola paket soal ujian. Setiap paket wajib berisi 140 soal (50 Listening, 40 Structure, 50 Reading) sebelum bisa dipakai untuk sesi ujian.</p>
        </div>
    </x-slot>

    <div class="py-8 min-h-screen bg-slate-50 dark:bg-[#0b0d13]"
         x-data="{
            showCreateModal: false,
            showDeleteModal: false,
            deleteFormId: null,
            deleteName: '',
            challengeCode: '',
            typedCode: '',
            openDeleteModal(formId, name) {
                this.deleteFormId = formId;
                this.deleteName = name;
                this.challengeCode = String(Math.floor(1000 + Math.random() * 9000));
                this.typedCode = '';
                this.showDeleteModal = true;
            },
            confirmDelete() {
                if (this.typedCode !== this.challengeCode) return;
                document.getElementById(this.deleteFormId).submit();
            }
         }">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Action Bar --}}
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    {{ $pakets->count() }} paket soal terdaftar
                </p>
                <button type="button" @click="showCreateModal = true"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                    <i class="fas fa-plus"></i> Tambah Paket Baru
                </button>
            </div>

            {{-- Grid Kartu Paket --}}
            @if($pakets->isEmpty())
                <div class="border rounded-2xl p-12 text-center bg-white border-slate-200 dark:bg-[#111827] dark:border-white/10">
                    <p class="text-slate-500 dark:text-slate-400 italic">Belum ada paket soal. Buat paket pertama untuk mulai mengisi bank soal.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($pakets as $item)
                        @php
                            $paket = $item['model'];
                            $isComplete = $item['is_complete'];
                            $available = $item['available_total'];
                            $required = $item['required_total'];
                            $percent = $required > 0 ? min(100, round(($available / $required) * 100)) : 0;
                            $deleteFormId = 'delete-paket-form-' . $paket->id;
                        @endphp
                        <div class="relative">
                            <a href="{{ route('paket-soal.questions.index', $paket) }}"
                               class="group block border rounded-2xl p-5 shadow-sm transition-all hover:shadow-md bg-white border-slate-200 hover:border-indigo-300 dark:bg-[#111827] dark:border-white/10 dark:hover:border-indigo-500/40">

                                <div class="flex items-start justify-between gap-2 mb-3 pr-7">
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                        {{ $paket->nama }}
                                    </h3>
                                    @if($isComplete)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 flex-shrink-0">
                                            <i class="fas fa-check-circle"></i> Lengkap
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 flex-shrink-0">
                                            <i class="fas fa-exclamation-triangle"></i> Belum Lengkap
                                        </span>
                                    @endif
                                </div>

                                {{-- Progress bar --}}
                                <div class="mb-3">
                                    <div class="flex items-center justify-between text-xs mb-1.5">
                                        <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $available }} / {{ $required }} soal</span>
                                        <span class="text-slate-400">{{ $percent }}%</span>
                                    </div>
                                    <div class="h-1.5 rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden">
                                        <div class="h-full rounded-full {{ $isComplete ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>

                                {{-- Breakdown per kategori --}}
                                <div class="grid grid-cols-3 gap-2 text-center">
                                    @foreach($item['sections'] as $section => $data)
                                        <div class="rounded-lg px-2 py-2 bg-slate-50 dark:bg-white/5">
                                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400 mb-0.5">{{ ucfirst($section) }}</p>
                                            <p class="text-sm font-bold {{ $data['shortage'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-700 dark:text-slate-200' }}">
                                                {{ $data['available'] }}/{{ $data['required'] }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </a>

                            {{-- Tombol hapus: elemen terpisah dari <a> (sibling, bukan nested) supaya
                                 klik hapus tidak ikut memicu navigasi. Selalu terlihat (bukan hover-only)
                                 karena hover tidak ada artinya di layar sentuh / tidak kelihatan di screenshot. --}}
                            <button type="button"
                                title="Hapus paket"
                                @click="openDeleteModal('{{ $deleteFormId }}', @js($paket->nama))"
                                class="absolute top-4 right-4 inline-flex items-center justify-center w-7 h-7 rounded-lg text-slate-300 hover:text-red-600 hover:bg-red-50 dark:text-slate-600 dark:hover:text-red-400 dark:hover:bg-red-500/10 transition-colors">
                                <i class="fas fa-trash text-xs"></i>
                            </button>

                            <form id="{{ $deleteFormId }}" action="{{ route('paket-soal.destroy', $paket) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- MODAL: Tambah Paket Baru --}}
        <div x-show="showCreateModal"
             x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
             @keydown.escape.window="showCreateModal = false">
            <div @click.away="showCreateModal = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="w-full max-w-md rounded-2xl border shadow-2xl bg-white border-slate-200 dark:bg-[#111827] dark:border-white/10">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-white/10">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tambah Paket Soal Baru</h3>
                    <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition text-xl leading-none">&times;</button>
                </div>
                <form action="{{ route('paket-soal.store') }}" method="POST" class="p-6">
                    @csrf
                    <label for="nama" class="block text-xs font-bold uppercase tracking-[0.14em] text-slate-500 mb-2">Nama Paket</label>
                    <input type="text" name="nama" id="nama" required autofocus
                        placeholder="Contoh: Paket B"
                        value="{{ old('nama') }}"
                        class="w-full px-4 py-2.5 rounded-lg text-sm font-medium border bg-white border-slate-200 text-slate-900 dark:bg-[#1e293b] dark:border-white/10 dark:text-white">
                    <p class="mt-2 text-xs text-slate-400">
                        Paket baru dibuat kosong. Setelah dibuat, kamu akan diarahkan untuk mengisi 140 soalnya.
                    </p>

                    <div class="mt-6 flex gap-2">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                            Buat Paket
                        </button>
                        <button type="button" @click="showCreateModal = false"
                            class="px-5 py-2.5 rounded-lg font-semibold text-sm transition bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL: Konfirmasi Hapus dengan kode verifikasi acak --}}
        <div x-show="showDeleteModal"
             x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
             @keydown.escape.window="showDeleteModal = false">
            <div @click.away="showDeleteModal = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="w-full max-w-sm rounded-2xl border shadow-2xl bg-white border-slate-200 dark:bg-[#111827] dark:border-white/10">
                <div class="px-6 pt-6 pb-2 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-red-50 dark:bg-red-500/10">
                        <i class="fas fa-trash text-red-600 dark:text-red-400"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Hapus paket ini?</h3>
                    <p class="mt-1.5 text-sm text-slate-500 dark:text-slate-400">
                        <span class="font-semibold text-slate-700 dark:text-slate-200" x-text="deleteName"></span>
                        beserta semua soal di dalamnya akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
                    </p>
                </div>

                <div class="px-6 pb-6 pt-3">
                    <p class="text-center text-[11px] font-bold uppercase tracking-[0.14em] text-slate-400 mb-1.5">
                        Ketik kode berikut untuk konfirmasi
                    </p>
                    <p class="text-center text-3xl font-black font-mono tracking-[0.3em] text-red-600 dark:text-red-400 select-none mb-3" x-text="challengeCode"></p>

                    <input type="text" x-model="typedCode" inputmode="numeric" maxlength="4" autocomplete="off"
                        placeholder="0000"
                        class="w-full px-4 py-2.5 rounded-lg text-center text-lg font-mono tracking-[0.3em] border bg-white border-slate-200 text-slate-900 dark:bg-[#1e293b] dark:border-white/10 dark:text-white">

                    <div class="mt-4 flex gap-2">
                        <button type="button"
                            @click="confirmDelete()"
                            :disabled="typedCode !== challengeCode"
                            :class="typedCode === challengeCode
                                ? 'bg-red-600 hover:bg-red-700 text-white cursor-pointer'
                                : 'bg-slate-200 text-slate-400 cursor-not-allowed dark:bg-white/5 dark:text-slate-500'"
                            class="flex-1 inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-semibold transition">
                            Hapus Permanen
                        </button>
                        <button type="button" @click="showDeleteModal = false"
                            class="px-5 py-2.5 rounded-lg font-semibold text-sm transition bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>