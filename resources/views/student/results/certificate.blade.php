<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat TOEFL - {{ auth()->user()->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@400;600;700;900&display=swap');

        /* Setup Font Khusus Sertifikat */
        .font-serif-cert { font-family: 'Playfair Display', serif; }
        .font-sans-cert { font-family: 'Inter', sans-serif; }

        /* Warna Background Sertifikat Klasik */
        .bg-certificate { background-color: #FAF8F2; }

        /* Ukuran Kertas A4 Landscape untuk Web Preview */
        .certificate-container {
            width: 297mm;
            height: 210mm;
            margin: 0 auto;
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
        }

        /* Konfigurasi Saat di-Print (CTRL+P) */
        @media print {
            body { 
                background: #FAF8F2 !important; 
                -webkit-print-color-adjust: exact; 
                print-color-adjust: exact; 
                margin: 0;
            }
            .no-print { display: none !important; }
            .certificate-container {
                width: 297mm;
                height: 210mm;
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                page-break-after: avoid;
                page-break-inside: avoid;
            }
            @page {
                size: A4 landscape;
                margin: 0;
            }
        }
    </style>
</head>
<body class="bg-slate-200 py-8 font-sans-cert text-slate-900" x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 300)">

    {{-- Tombol Aksi (Tidak Ikut Terprint) --}}
    <div class="max-w-[297mm] mx-auto flex justify-between items-center mb-6 px-4 no-print" x-show="loaded" x-cloak>
        <div>
            <h2 class="text-2xl font-black text-slate-800">Preview Sertifikat</h2>
            <p class="text-sm text-slate-500">Pastikan layout printer diatur ke <strong>Landscape</strong>.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('student.results.index') }}" class="bg-white border border-slate-300 text-slate-700 px-6 py-2.5 rounded-xl font-bold hover:bg-slate-50 transition active:scale-95 shadow-sm">
                Kembali ke Riwayat
            </a>
            <button onclick="window.print()" class="bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-black hover:bg-indigo-700 shadow-md shadow-indigo-600/20 transition active:scale-95">
                <i class="fas fa-print mr-2"></i> Cetak Sekarang
            </button>
        </div>
    </div>

    {{-- AREA KERTAS SERTIFIKAT --}}
    <div x-show="loaded" x-cloak class="certificate-container bg-certificate shadow-2xl border border-slate-300 flex flex-col items-center justify-center p-12">
        
        {{-- Watermark Logo Transparan di Tengah --}}
        <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none">
            <img src="{{ asset('images/logo.png') }}" class="w-[500px] h-[500px] object-contain" alt="Watermark" width="500" height="500" loading="lazy">
        </div>

        <div class="relative z-10 w-full flex flex-col items-center">
            
            {{-- Header: Logo & Title --}}
            <img src="{{ asset('images/logo.png') }}" alt="Logo Piksi Ganesha" class="h-24 mb-4 object-contain" width="96" height="96" loading="lazy">
            
            <h2 class="text-2xl font-black tracking-widest text-slate-900 uppercase">TOEFL PIKSI V12</h2>
            <p class="text-sm tracking-[0.2em] text-slate-600 mb-8 uppercase font-semibold">Official Score Report</p>

            {{-- Main Title --}}
            <h1 class="text-5xl font-serif-cert text-slate-900 mb-6 uppercase tracking-wider">Certificate of Achievement</h1>
            
            <p class="text-sm tracking-[0.15em] font-bold text-slate-700 mb-2 uppercase">Awarded To</p>
            
            {{-- Nama & NPM --}}
            <h3 class="text-4xl font-black text-slate-900 mb-1 uppercase">{{ auth()->user()->name }}</h3>
            <p class="text-sm tracking-widest text-slate-600 mb-8 font-medium">NPM: {{ auth()->user()->npm ?? '22010001' }}</p>

            {{-- Table Skor --}}
            <div class="w-[85%] max-w-3xl mb-4">
                <table class="w-full border-collapse border border-slate-800 text-lg">
                    <tbody>
                        <tr>
                            <td class="border border-slate-800 px-6 py-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-700">Listening Comprehension:</span> 
                                    <strong class="text-xl">{{ $result->correct_listening }}</strong>
                                </div>
                            </td>
                            <td class="border border-slate-800 px-6 py-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-700">Structure & Written Expression:</span> 
                                    <strong class="text-xl">{{ $result->correct_structure }}</strong>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-slate-800 px-6 py-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-700">Reading Comprehension:</span> 
                                    <strong class="text-xl">{{ $result->correct_reading }}</strong>
                                </div>
                            </td>
                            <td class="border border-slate-800 px-6 py-2 bg-slate-100/50">
                                <div class="flex justify-between items-center">
                                    <span class="font-black uppercase tracking-widest">Total Score:</span> 
                                    <strong class="text-2xl font-black">{{ $result->score_total }}</strong>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Footnote Table --}}
            <div class="w-[85%] max-w-3xl text-left text-[11px] text-slate-600 mb-6 leading-tight">
                * Total score scaled range: 310-677<br>
                ** Valid for institutional testing program only
            </div>

            {{-- Masa Berlaku --}}
            <p class="text-md font-medium text-slate-800 mb-auto mt-2">
                Valid until: {{ $result->submitted_at->addYears(2)->format('F d, Y') }}
            </p>

            {{-- Tanda Tangan & Stempel --}}
            <div class="flex w-full justify-between items-end px-16 mt-12">
                
                {{-- Tanda Tangan Kiri --}}
                <div class="flex flex-col items-center w-56">
                    <div class="h-16 flex items-end justify-center mb-1">
                        {{-- Dummy Cursive Sign --}}
                        <span class="font-serif-cert italic text-4xl text-slate-700 opacity-80 transform -rotate-6">Rangga M. I</span>
                    </div>
                    <div class="w-full border-t border-slate-800 pt-1 text-center">
                        <p class="text-sm font-bold text-slate-900">Signature</p>
                    </div>
                </div>

                {{-- Kotak Stempel Tengah --}}
                <div class="w-24 h-24 border border-dashed border-slate-400 flex items-center justify-center p-1 opacity-60">
                    <div class="w-full h-full border border-slate-300 flex items-center justify-center">
                        <span class="text-slate-400 tracking-[0.2em] text-xs font-bold">STAMP</span>
                    </div>
                </div>

                {{-- Tanda Tangan Kanan --}}
                <div class="flex flex-col items-center w-56">
                    <div class="h-16 flex items-end justify-center mb-1">
                        {{-- Dummy Cursive Sign --}}
                        <span class="font-serif-cert italic text-4xl text-slate-700 opacity-80 transform -rotate-6">Director</span>
                    </div>
                    <div class="w-full border-t border-slate-800 pt-1 text-center">
                        <p class="text-sm font-bold text-slate-900">Signature</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>