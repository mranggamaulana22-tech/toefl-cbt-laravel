<x-app-layout>
    @include('student.partials.shared-utils-styles')
    @php
        $isStudent = auth()->user()->role === 'student';
    @endphp

    <style>
        /* ============================================================
           STYLE UMUM (Berlaku untuk Keduanya)
           ============================================================ */
        @keyframes heroReveal {
            from { opacity: 0; transform: translateY(20px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes textFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .student-profile-card {
            animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
            transition: box-shadow 0.35s ease, transform 0.3s ease;
        }

        .student-profile-card:hover {
            transform: translateY(-3px);
        }

        /* Focus lift effect untuk input */
        .student-profile-card input,
        .student-profile-card select,
        .student-profile-card textarea {
            transition: box-shadow 0.25s ease, border-color 0.25s ease, transform 0.2s ease;
        }
        .student-profile-card input:focus,
        .student-profile-card select:focus,
        .student-profile-card textarea:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.12);
        }

        /* ============================================================
           STYLE KHUSUS STUDENT (Hanya muncul jika User = Student)
           ============================================================ */
        @if($isStudent)
        .profile-bg-img {
            position: fixed; inset: 0; z-index: 0; width: 100vw; height: 100vh;
            background-image: url('{{ asset('images/classroom_light.png') }}');
            background-size: cover; background-position: center; background-repeat: no-repeat;
            background-attachment: scroll; opacity: 0.95; transition: opacity 0.5s;
        }
        .dark .profile-bg-img {
            background-image: url('{{ asset('images/classroom_dark.png') }}');
            opacity: 0.7;
        }
        .profile-bg-gradient {
            position: fixed; inset: 0; z-index: 1; pointer-events: none;
            background: linear-gradient(to top, rgba(255,255,255,0.7), rgba(255,255,255,0.1), transparent);
            transition: opacity 0.5s;
        }
        .dark .profile-bg-gradient {
            background: linear-gradient(to top right, #0b0d13, transparent, transparent);
        }

        @keyframes ambientFloat {
            0%, 100% { transform: translateY(0px) scale(1); }
            50%      { transform: translateY(-18px) scale(1.04); }
        }

        .profile-glow-top { animation: ambientFloat 9s ease-in-out infinite; }
        .profile-glow-bottom { animation: ambientFloat 12s ease-in-out infinite reverse; }

        .student-profile-hero {
            animation: heroReveal 0.65s cubic-bezier(0.16, 1, 0.3, 1) both;
            position: relative; overflow: hidden;
            background: linear-gradient(to right, #4338ca, #4f46e5, #0ea5e9);
        }

        .student-profile-hero::after {
            content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.4) 40%, rgba(255,255,255,0.4) 60%, transparent 100%);
            background-size: 200% 100%; animation: shimmer 3.5s linear infinite; opacity: 0.7;
        }

        .student-profile-hero p, .student-profile-hero h3 {
            animation: textFadeIn 0.5s ease both;
        }
        @endif
    </style>

    <div class="relative min-h-screen transition-colors duration-500" 
         :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-gray-50'">

        @if($isStudent)
            <div class="profile-glow-top fixed top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 blur-[120px] rounded-full pointer-events-none z-0"></div>
            <div class="profile-glow-bottom fixed bottom-0 left-0 w-[300px] h-[300px] bg-purple-600/10 blur-[100px] rounded-full pointer-events-none z-0"></div>
            <div class="profile-bg-img"></div>
            <div class="profile-bg-gradient"></div>
        @endif

        <div class="student-profile-page py-12 relative z-10" style="background:transparent!important;">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                {{-- HERO SECTION --}}
                <div class="rounded-3xl p-8 md:p-10 shadow-xl transition-all border
                     {{ $isStudent ? 'student-profile-hero text-white border-transparent' : '' }}"
                     :class="!{{ $isStudent ? 'true' : 'false' }} && ($store.theme?.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-slate-200')">
                    
                    <p class="text-xs font-bold tracking-[0.25em] uppercase {{ $isStudent ? 'text-indigo-100' : 'text-indigo-500' }}">
                        {{ $isStudent ? 'Student Profile Center' : 'Admin Control Panel' }}
                    </p>
                    <h3 class="mt-3 text-3xl font-black leading-tight {{ !$isStudent ? 'text-slate-900 dark:text-white' : '' }}">
                        {{ $isStudent ? 'Kelola data akun Anda dengan cepat.' : 'Pengaturan Profil Administrator' }}
                    </h3>
                    <p class="mt-3 max-w-2xl {{ $isStudent ? 'text-indigo-100' : 'text-slate-500 dark:text-slate-400' }}">
                        Perbarui identitas, keamanan password, dan pengaturan akun {{ $isStudent ? 'mahasiswa' : 'sistem' }} Anda dari satu halaman terpusat.
                    </p>
                </div>

                {{-- FORM CARDS --}}
                <div class="space-y-6">
                    {{-- 1. Info Profil --}}
                    <div :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10 shadow-none' : 'bg-white border-gray-100 shadow-sm'"
                         class="student-profile-card p-5 sm:p-8 rounded-2xl border transition-all"
                         style="animation-delay: 0.20s">
                        <div class="max-w-2xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    {{-- 2. Update Password --}}
                    <div :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10 shadow-none' : 'bg-white border-gray-100 shadow-sm'"
                         class="student-profile-card p-5 sm:p-8 rounded-2xl border transition-all"
                         style="animation-delay: 0.35s">
                        <div class="max-w-2xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    {{-- 3. Delete Akun --}}
                    <div :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10 shadow-none' : 'bg-white border-gray-100 shadow-sm'"
                         class="student-profile-card p-5 sm:p-8 rounded-2xl border transition-all"
                         style="animation-delay: 0.50s">
                        <div class="max-w-2xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>