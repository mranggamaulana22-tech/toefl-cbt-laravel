{{-- ===== QUESTION BODY ===== --}}
<div class="min-h-[400px] p-6 transition-all duration-200 ease-out md:p-8"
    :class="questionAnimating ? 'opacity-70 translate-y-1' : 'opacity-100 translate-y-0'">
    @foreach($questions as $index => $q)
    <template x-if="activeQuestion === {{ $index }}">
    <div class="space-y-6">
        
        @if($q->audio_path)
        <div class="flex items-center gap-4 rounded-2xl border border-white/10 bg-white/[0.04] p-4">
            <i class="fas fa-headphones text-2xl text-indigo-400"></i>
            <audio controls preload="none" class="w-full">
                <source src="{{ asset('storage/' . $q->audio_path) }}" type="audio/mpeg">
            </audio>
        </div>
        @endif

        @if($q->passage)
        <div x-data="{ openPassage: true }" class="mb-4 rounded-2xl border border-white/10 bg-white/[0.03] p-4">
            <button type="button" @click="openPassage = !openPassage" class="flex w-full items-center justify-between rounded-xl border border-white/10 bg-white/[0.05] px-4 py-3 text-left text-sm font-semibold text-white/70 transition hover:border-indigo-500/30 hover:text-white">
                <span>Teks Reading</span>
                <span x-text="openPassage ? 'Tutup' : 'Buka'"></span>
            </button>

            <div x-show="openPassage" x-transition.opacity.duration.150ms class="mt-4 rounded-xl border border-white/10 bg-white/[0.04] p-5 text-sm italic leading-relaxed text-white/60">
                {!! nl2br(e($q->passage)) !!}
            </div>
        </div>
        @endif

        <p class="text-xl font-medium leading-relaxed text-white/90">{{ $q->question_text }}</p>

        <div class="grid grid-cols-1 gap-3">
            @foreach(['A', 'B', 'C', 'D'] as $opt)
            @php $optionField = 'option_' . strtolower($opt); @endphp
                <label :class="answers[activeQuestion] === '{{ $opt }}' ? 'border-indigo-500 bg-indigo-500/10 shadow-sm shadow-indigo-500/10' : 'border-white/10 hover:border-indigo-500/30 hover:bg-white/[0.03]'" 
                    class="flex items-center rounded-2xl border-2 p-4 cursor-pointer transition-all">
                <input type="radio" name="q{{ $index }}" value="{{ $opt }}" class="hidden" 
                     @click="answers[activeQuestion] = '{{ $opt }}'; saveProgress()">
                <span class="mr-4 flex h-8 w-8 items-center justify-center rounded-full border-2 font-bold"
                      :class="answers[activeQuestion] === '{{ $opt }}' ? 'bg-indigo-600 text-white border-indigo-600' : 'text-white/30 border-white/20'">
                    {{ $opt }}
                </span>
                <span class="font-medium text-white/75">{{ $q->$optionField }}</span>
            </label>
            @endforeach
        </div>
    </div>
    </template>
    @endforeach
</div>
