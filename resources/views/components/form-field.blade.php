{{--
    Komponen: <x-form-field>

    Tujuan: Satu komponen untuk label + input/textarea/select + pesan error,
    lengkap dengan styling dark mode, supaya tiap field form (kategori, passage,
    transcript, pertanyaan, opsi A-D, dst) tidak menulis ulang markup yang sama.

    Cara pakai - INPUT TEXT:
        <x-form-field
            type="text"
            name="option_a"
            label="Opsi A"
            :required="true"
            :value="old('option_a', $practiceQuestion->option_a)"
            placeholder="Masukkan opsi A..."
        />

    Cara pakai - TEXTAREA:
        <x-form-field
            type="textarea"
            name="question_text"
            label="Pertanyaan"
            :required="true"
            :value="old('question_text', $practiceQuestion->question_text)"
            rows="3"
            placeholder="Masukkan pertanyaan..."
        />

    Cara pakai - SELECT:
        <x-form-field
            type="select"
            name="category"
            label="Kategori"
            :required="true"
            :value="old('category', $practiceQuestion->category)"
            :options="['listening' => 'Listening', 'structure' => 'Structure', 'reading' => 'Reading']"
            placeholder="Pilih kategori..."
        />

    Cara pakai - FILE:
        <x-form-field
            type="file"
            name="audio_path"
            label="File Audio (Opsional)"
            accept="audio/*"
            hint="Format: MP3, WAV, OGG (maks. 20MB)"
        />

    Cara pakai - VARIAN DANGER (untuk field penting seperti Jawaban Benar):
        <x-form-field ... :danger="true" />

    Props:
    - type (string)     : 'text' | 'textarea' | 'select' | 'file'
    - name (string)     : atribut name & id field (wajib)
    - label (string)    : teks label
    - required (bool)   : tampilkan tanda bintang merah & atribut required
    - value (mixed)     : nilai terisi (biasanya dari old(...))
    - options (array)   : untuk type=select, format ['value' => 'Label']
    - placeholder (str) : placeholder / opsi kosong pertama untuk select
    - rows (int)        : jumlah baris untuk textarea (default 4)
    - accept (string)   : atribut accept untuk type=file
    - hint (string)     : teks kecil bantuan di bawah field (mis. format file)
    - danger (bool)     : varian merah untuk field kritikal (mis. jawaban benar)
    - delay (string)    : kelas tailwind delay animasi, mis. 'delay-100' (opsional)
--}}

@props([
    'type' => 'text',
    'name' => '',
    'label' => '',
    'required' => false,
    'value' => null,
    'options' => [],
    'placeholder' => '',
    'rows' => 4,
    'accept' => null,
    'hint' => '',
    'danger' => false,
    'delay' => '',
])

@php
    $labelColor = $danger
        ? 'text-red-600'
        : ($attributes->has('data-dark') ? '' : null);

    $borderColor = $danger ? 'border-red-900/30' : 'border-white/10';
    $borderColorLight = $danger ? 'border-red-300' : 'border-slate-200';
@endphp

<div class="transition-all duration-500 {{ $delay }}">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-semibold mb-2"
            :class="$store.theme?.isDark ? '{{ $danger ? "text-slate-300" : "text-slate-300" }}' : '{{ $danger ? "text-red-600" : "text-slate-900" }}'">
            {{ $label }}
            @if($required)
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif

    @if($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}"
            @if($required) required @endif
            :class="$store.theme?.isDark ? '!bg-[#1e293b] !text-white border-white/10 placeholder-slate-500' : 'bg-white text-slate-900 border-slate-200 placeholder-slate-400'"
            class="w-full rounded-xl px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500 transition"
            placeholder="{{ $placeholder }}">{{ $value }}</textarea>

    @elseif($type === 'select')
        <select id="{{ $name }}" name="{{ $name }}"
            @if($required) required @endif
            :class="$store.theme?.isDark ? '!bg-[#1e293b] !text-white {{ $borderColor }}' : 'bg-white text-slate-900 {{ $borderColorLight }}'"
            class="w-full rounded-xl px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500 transition">
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" @selected((string) $value === (string) $optValue)>{{ $optLabel }}</option>
            @endforeach
        </select>

    @elseif($type === 'file')
        <input type="file" id="{{ $name }}" name="{{ $name }}"
            @if($accept) accept="{{ $accept }}" @endif
            :class="$store.theme?.isDark ? 'text-slate-400 border-white/10' : 'text-slate-900 border-slate-200'"
            class="w-full rounded-xl border px-4 py-3 transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
        @if($hint)
            <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
        @endif
        {{-- Slot untuk info tambahan, mis. nama file audio yang sudah ada saat edit --}}
        {{ $slot ?? '' }}

    @else
        <input type="text" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}"
            @if($required) required @endif
            :class="$store.theme?.isDark ? '!bg-[#1e293b] !text-white border-white/10 placeholder-slate-600' : 'bg-white text-slate-900 border-slate-200 placeholder-slate-400'"
            class="w-full rounded-xl px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500 transition"
            placeholder="{{ $placeholder }}">
    @endif

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>