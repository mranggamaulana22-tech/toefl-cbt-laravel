{{-- HERO SECTION --}}
<section x-cloak x-show="loaded" 
    x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-[1800ms]"
    x-transition:enter-start="opacity-0 translate-y-20 scale-[0.96]"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    class="relative min-h-[300px] sm:min-h-[400px] lg:min-h-[480px] rounded-[3.5rem] overflow-visible flex items-center px-4 lg:px-10 shadow-2xl transition-all border"
    :class="$store.theme?.isDark 
        ? 'bg-white/[0.02] border-white/10 hover:bg-white/[0.05]' 
        : 'bg-white/20 border-black/5 hover:bg-white/30 shadow-xl'">

    {{-- MASCOT + GLOW --}}
    <div class="absolute inset-0 justify-center items-end lg:items-center pointer-events-none z-0 hidden sm:flex"
         x-show="loaded"
         x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-[2200ms] delay-[400ms]"
         x-transition:enter-start="opacity-0 translate-y-16 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100">
        <div class="w-[300px] h-[300px] lg:w-[600px] lg:h-[600px] bg-purple-600/10 rounded-full blur-[100px] absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>
        
        <img src="{{ asset('images/mascot.png') }}" 
             :class="$store.theme?.isDark 
                 ? 'max-w-full w-auto sm:h-[350px] lg:h-[580px] object-contain object-bottom relative translate-y-10 lg:translate-x-40 drop-shadow-[0_20px_60px_rgba(0,0,0,0.8)]' 
                 : 'max-w-full w-auto sm:h-[350px] lg:h-[580px] object-contain object-bottom relative translate-y-10 lg:translate-x-40 drop-shadow-[0_16px_32px_rgba(40,40,40,0.32)]'"
             alt="3D Mascot Gajah">

        {{-- BUBBLE CHAT --}}
        <div class="absolute top-10 right-5 lg:top-20 lg:right-28 hidden md:block z-20 pointer-events-auto"
             x-data='{
                 messages: [
                     "Satu langkah lagi menuju skor TOEFL impianmu, tetap semangat dan jangan menyerah! 🐘",
                     "Konsistensi adalah kunci utama untuk meraih skor TOEFL terbaik, terus latihan setiap hari! 📚",
                     "Kamu hebat! Setiap usaha yang kamu lakukan hari ini akan membuahkan hasil luar biasa nanti. 💪",
                     "Jangan takut gagal, karena setiap kesalahan adalah pelajaran berharga untuk sukses! 🌟",
                     "Percaya pada dirimu sendiri, kamu pasti bisa menaklukkan semua soal TOEFL! 🚀",
                     "Ingat, proses tidak akan mengkhianati hasil. Terus berjuang sampai garis akhir! 🏁",
                     "Setiap latihan hari ini membawa kamu lebih dekat ke skor TOEFL impian. Tetap fokus! 🎯",
                     "Jangan lupa istirahat yang cukup, pikiran segar membantumu memahami soal lebih baik! 😴",
                     "Bersama TOEFL PIKSI, kamu tidak sendirian. Kami selalu mendukungmu! 🤗",
                     "Teruslah berlatih dan jangan ragu bertanya jika ada yang belum paham. Kamu pasti bisa! 🙌"
                 ],
                 displayText: "", charIndex: 0, msgIndex: 0, visible: false, typing: false, initialized: false,
                 startCycle() {
                     if (!this.initialized) return;
                     this.visible = false; this.displayText = ""; this.charIndex = 0;
                     const self = this;
                     setTimeout(function() {
                         self.visible = true; self.typing = true;
                         let interval = setInterval(function() {
                             const msg = self.messages[self.msgIndex];
                             if (self.charIndex < msg.length) {
                                 self.displayText += msg[self.charIndex];
                                 self.charIndex++;
                             } else {
                                 clearInterval(interval); self.typing = false;
                                 setTimeout(function() {
                                     self.visible = false;
                                     setTimeout(function() {
                                         self.msgIndex = (self.msgIndex + 1) % self.messages.length;
                                         self.startCycle();
                                     }, 1200);
                                 }, 5000);
                             }
                         }, 70);
                     }, 600);
                 }
             }'
             x-init="setTimeout(() => { initialized = true; startCycle(); }, 2800)">
            <div x-show="visible"
                 x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-700"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-400"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                 class="relative backdrop-blur-md border rounded-[2rem] px-6 py-5 max-w-[260px] shadow-2xl transform rotate-1"
                 :class="$store.theme?.isDark ? 'bg-[#0f1120]/80 border-white/15' : 'bg-slate-900/90 border-slate-900/10'">
                <p class="text-[13px] font-bold leading-relaxed text-white min-h-[52px] relative">
                    <span x-text="displayText"></span>
                    <span x-show="typing" class="inline-block w-0.5 h-4 bg-indigo-400 ml-0.5 align-middle" style="animation: blink 0.7s step-end infinite;"></span>
                </p>
            </div>
        </div>
    </div>

    {{-- HERO TEXT --}}
    <div class="relative z-10 w-full max-w-2xl pointer-events-none"
         x-show="loaded"
         x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-[2000ms] delay-[200ms]"
         x-transition:enter-start="opacity-0 translate-y-10"
         x-transition:enter-end="opacity-100 translate-y-0">
        <div class="pointer-events-auto">
            <span class="inline-block px-4 py-1.5 mb-6 rounded-full border text-[10px] font-black uppercase tracking-widest"
                  :class="$store.theme?.isDark ? 'bg-indigo-500/20 border-indigo-500/30 text-indigo-300' : 'bg-indigo-600/10 border-indigo-600/20 text-indigo-700'">TOEFL PIKSI v12</span>
            
            <h3 class="text-4xl lg:text-6xl font-black leading-tight tracking-tighter drop-shadow-2xl"
                :class="$store.theme?.isDark ? 'text-white' : 'text-slate-950'">
                Selamat Belajar,<br>
                <span class="bg-gradient-to-r bg-clip-text text-transparent filter drop-shadow-md"
                      :class="$store.theme?.isDark ? 'from-indigo-400 via-white to-cyan-400' : 'from-indigo-700 via-indigo-500 to-cyan-700'">
                    {{ explode(' ', auth()->user()->name)[0] }}!
                </span>
            </h3>
            
            <p class="mt-8 text-sm lg:text-base max-w-sm leading-relaxed font-bold p-6 rounded-[2rem] border shadow-xl"
               :class="$store.theme?.isDark 
                 ? 'bg-black/30 text-slate-100 border-white/5 backdrop-blur-md shadow-2xl' 
                 : 'bg-white/70 text-slate-900 border-black/5 shadow-lg backdrop-blur-sm'">
                Satu sesi latihan lagi dan kamu akan naik rank! Jangan menyerah, skor TOEFL targetmu sudah dekat.
            </p>
            
            <div class="mt-8 flex gap-4">
                <button class="group px-8 py-4 bg-indigo-600 rounded-2xl font-black text-sm text-white shadow-xl hover:scale-105 active:scale-95 transition-all">
                    Mulai Tes Sekarang
                </button>
                <button class="px-8 py-4 border rounded-2xl font-black text-sm transition-all shadow-md"
                        :class="$store.theme?.isDark 
                          ? 'bg-white/5 border-white/10 text-white hover:bg-white/10' 
                          : 'bg-white/50 border-black/10 text-slate-800 hover:bg-white/80'">
                    Review Progres
                </button>
            </div>
        </div>
    </div>
</section>
