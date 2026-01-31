<x-dashboard.layout title="Edit job">
    <form
    method="POST"
    action="{{ route('frontend.jobs.update', $job) }}"
>
    @csrf
    @method('PUT')

    @include('dashboard.jobs._form', ['job' => $job])

    <div class="mt-6 flex gap-3">
        <button
            type="submit"
            class="pixel-button px-6 py-3 text-xs uppercase tracking-[0.2em]"
        >
            Save changes
        </button>
    </div>
</form>
    @php
        $now = now();
        $isPublished = !empty($job->published_at) && !empty($job->expires_at) && $job->expires_at->gt($now);

        // remaining days (min 0)
        $remainingDays = $isPublished ? max(0, (int) ceil($now->diffInSeconds($job->expires_at) / 86400)) : 0;

        // how many credits per day? (adjust to your system)
        $creditsPerDay = (int) ($creditsPerDay ?? 1);
    @endphp

    <div class="mt-10 pixel-outline p-4"
         data-remaining-days="{{ $remainingDays }}"
         data-credits-per-day="{{ $creditsPerDay }}"
         data-is-published="{{ $isPublished ? 1 : 0 }}">
        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">
            {{ $isPublished ? 'Change duration' : 'Publish job' }}
        </div>

        <div class="mt-2 text-sm text-slate-600">
            Available credits: <span class="font-semibold">{{ $availableCredits ?? 0 }}</span>
            @if($isPublished)
                <span class="ml-3 text-slate-500">Current remaining: <span class="font-semibold">{{ $remainingDays }}</span> days</span>
            @endif
        </div>

        <form method="POST" action="{{ route('frontend.jobs.post', $job) }}"
              class="mt-4 grid gap-3 md:grid-cols-[200px_1fr_auto] items-end">
            @csrf

            <div>
                <label class="text-xs uppercase tracking-[0.2em]">Days</label>
                <input type="number" name="days" id="post-days" min="1"
                       value="{{ old('days', $isPublished ? $remainingDays : 7) }}"
                       class="mt-2 w-full pixel-outline px-3 py-2" required>
            </div>

            <div class="text-xs text-slate-600 space-y-1">
                <div>Preview expiry: <span id="post-expiry">-</span></div>

                {{-- Only visible when extending --}}
                <div id="post-required-wrap" class="hidden">
                    Credits required: <span id="post-credits">-</span>
                </div>

                {{-- Only visible when shortening --}}
                <div id="post-refund-wrap" class="hidden">
                    Refund (50%): <span id="post-refund">-</span>
                </div>

                {{-- Notice when no extension --}}
                <div id="post-free-wrap" class="hidden">
                    No extra credits needed.
                </div>
            </div>

            <button type="submit" class="pixel-outline px-6 py-2 text-xs uppercase tracking-[0.2em]">
                {{ $isPublished ? 'Update duration' : 'Publish' }}
            </button>
        </form>

        <form method="POST" action="{{ route('frontend.jobs.archive', $job) }}" class="mt-4">
            @csrf
            <button type="submit" class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">Archive</button>
        </form>
    </div>

    @push('scripts')
    <script>
    (() => {
      const box = document.querySelector('[data-remaining-days][data-credits-per-day]');
      if (!box) return;

      const remainingDays = parseInt(box.dataset.remainingDays || '0', 10);
      const creditsPerDay = parseInt(box.dataset.creditsPerDay || '1', 10);
      const isPublished   = box.dataset.isPublished === '1';

      const daysEl = document.getElementById('post-days');
      const expiryEl = document.getElementById('post-expiry');

      const reqWrap = document.getElementById('post-required-wrap');
      const reqEl   = document.getElementById('post-credits');

      const refundWrap = document.getElementById('post-refund-wrap');
      const refundEl   = document.getElementById('post-refund');

      const freeWrap = document.getElementById('post-free-wrap');

      const fmtDate = (d) => {
        const pad = (n) => String(n).padStart(2,'0');
        return `${pad(d.getDate())}.${pad(d.getMonth()+1)}.${d.getFullYear()}`;
      };

      const calc = () => {
        const newDays = Math.max(1, parseInt(daysEl.value || '1', 10));
        const baseDays = isPublished ? remainingDays : 0;

        // preview expiry from today
        const expiry = new Date();
        expiry.setDate(expiry.getDate() + newDays);
        expiryEl.textContent = fmtDate(expiry);

        // delta to remaining duration
        const delta = newDays - baseDays;

        reqWrap.classList.add('hidden');
        refundWrap.classList.add('hidden');
        freeWrap.classList.add('hidden');

        if (isPublished) {
          if (delta > 0) {
            const required = delta * creditsPerDay;
            reqEl.textContent = required;
            reqWrap.classList.remove('hidden');
          } else if (delta < 0) {
            const refundDays = Math.abs(delta);
            const refundCredits = Math.ceil((refundDays * creditsPerDay) * 0.5);
            refundEl.textContent = refundCredits;
            refundWrap.classList.remove('hidden');
          } else {
            freeWrap.classList.remove('hidden');
          }
        } else {
          // for initial publish: show credits normally (optional)
          const required = newDays * creditsPerDay;
          reqEl.textContent = required;
          reqWrap.classList.remove('hidden');
        }
      };

      daysEl.addEventListener('input', calc);
      calc();
    })();
    </script>
    @endpush
</x-dashboard.layout>
