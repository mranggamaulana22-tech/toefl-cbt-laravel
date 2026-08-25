<div id="students-results">
    <div class="bg-white dark:bg-[#111827] sm:rounded-xl shadow-sm border border-gray-100 dark:border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-white/5 border-b border-gray-100 dark:border-white/10 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-slate-400">
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4">Nama Peserta</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">NPM</th>
                        <th class="px-6 py-4">Jurusan</th>
                        <th class="px-6 py-4">Angkatan</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-500 dark:text-slate-400">{{ $students->firstItem() + $loop->index }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $student->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 dark:text-slate-400">{{ $student->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 dark:text-slate-400">{{ $student->npm ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 dark:text-slate-400">{{ $student->class ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 dark:text-slate-400">{{ $student->angkatan ?? '-' }}</div>
                            </td>
                            
                            <!-- Kolom Aksi dengan Flexbox (Gap-2) -->
                            <td class="px-6 py-4 text-right whitespace-nowrap flex justify-end items-center gap-2">
                                
                                <!-- Tombol Edit / Reset Password (SVG) -->
                                <button type="button" 
                                        onclick="document.getElementById('edit-modal-{{ $student->id }}').classList.remove('hidden')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold transition shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                    Edit
                                </button>

                                <!-- Tombol Hapus (SVG) -->
                                <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="m-0 flex" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center p-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition shadow-sm" title="Hapus Peserta">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                                
                                <!-- Modal Edit & Reset Password -->
                                <div id="edit-modal-{{ $student->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto text-left whitespace-normal">
                                    
                                    <!-- Inner Wrapper untuk Flexbox & Background -->
                                    <div class="flex items-center justify-center min-h-screen p-4 bg-black/50 backdrop-blur-sm">
                                        
                                        <div class="bg-white dark:bg-[#111827] border dark:border-white/10 rounded-xl max-w-md w-full p-6 shadow-xl relative">
                                            <div class="flex justify-between items-center mb-5">
                                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Data Mahasiswa</h3>
                                                <button type="button" onclick="document.getElementById('edit-modal-{{ $student->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <form action="{{ route('students.update', $student->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-400">Nama Lengkap</label>
                                                    <input type="text" name="name" value="{{ $student->name }}" required
                                                           class="w-full rounded-lg bg-white border-gray-300 focus:ring-indigo-500 text-gray-900 dark:!bg-[#1e293b] dark:!text-white dark:border-white/10">
                                                </div>

                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-400">Email</label>
                                                    <input type="email" name="email" value="{{ $student->email }}" required
                                                           class="w-full rounded-lg bg-white border-gray-300 focus:ring-indigo-500 text-gray-900 dark:!bg-[#1e293b] dark:!text-white dark:border-white/10">
                                                </div>

                                                <div class="mb-4">
                                                    <label class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-400">NPM</label>
                                                    <input type="text" name="npm" value="{{ $student->npm }}"
                                                           class="w-full rounded-lg bg-white border-gray-300 focus:ring-indigo-500 text-gray-900 dark:!bg-[#1e293b] dark:!text-white dark:border-white/10">
                                                </div>

                                                <div class="mb-4 grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-400">Jurusan</label>
                                                        <select name="class"
                                                               class="w-full rounded-lg bg-white border-gray-300 focus:ring-indigo-500 text-gray-900 dark:!bg-[#1e293b] dark:!text-white dark:border-white/10">
                                                            <option value="">-</option>
                                                            @foreach(\App\Enums\Jurusan::labels() as $value => $label)
                                                                <option value="{{ $value }}" @selected($student->class === $value)>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-400">Angkatan</label>
                                                        <select name="angkatan"
                                                               class="w-full rounded-lg bg-white border-gray-300 focus:ring-indigo-500 text-gray-900 dark:!bg-[#1e293b] dark:!text-white dark:border-white/10">
                                                            <option value="">-</option>
                                                            @for($year = now()->year + 1; $year >= now()->year - 5; $year--)
                                                                <option value="{{ $year }}" @selected((string) $student->angkatan === (string) $year)>{{ $year }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mb-6">
                                                    <label class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-400">
                                                        Password Baru <span class="text-amber-500 normal-case">(Kosongkan jika tidak ingin direset)</span>
                                                    </label>
                                                    <input type="password" name="password" placeholder="Ketik password baru..."
                                                           class="w-full rounded-lg bg-white border-gray-300 focus:ring-indigo-500 text-gray-900 dark:!bg-[#1e293b] dark:!text-white dark:border-white/10 placeholder-gray-400">
                                                </div>

                                                <div class="flex justify-end gap-2 mt-6">
                                                    <button type="button" onclick="document.getElementById('edit-modal-{{ $student->id }}').classList.add('hidden')"
                                                            class="px-4 py-2 bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10 rounded-lg text-xs font-bold uppercase transition">
                                                        Batal
                                                    </button>
                                                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold uppercase transition">
                                                        Simpan Perubahan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto mb-3 opacity-50 block">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 12.839a2.25 2.25 0 0 0-.1.661Z" />
                                </svg>
                                Belum ada data peserta ujian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($students->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10 bg-gray-50/50 dark:bg-white/5">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>