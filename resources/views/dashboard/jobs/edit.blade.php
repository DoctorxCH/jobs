<x-dashboard.layout title="{{ __('main.edit_job_title') }}">
    <form
    method="POST"
    action="{{ route('frontend.jobs.update', $job) }}"
    id="job-edit-form"
>
    @csrf
    @method('PUT')

    @include('dashboard.jobs._form', ['job' => $job])

    </form>
    @php
        $now = now();
        $isPublished = !empty($job->published_at) && !empty($job->expires_at) && $job->expires_at->gt($now);
        $isArchived = ($job->status ?? null) === 'archived';
        $isExpired = !empty($job->expires_at) && $job->expires_at->lt($now);

        // remaining days (min 0)
        $remainingDays = $isPublished ? max(0, (int) ceil($now->diffInSeconds($job->expires_at) / 86400)) : 0;

        // how many credits per day? (adjust to your system)
        $creditsPerDay = (int) ($creditsPerDay ?? 1);
    @endphp

        <div class="mt-10 pixel-outline p-4"
         data-remaining-days="{{ $remainingDays }}"
         data-credits-per-day="{{ $creditsPerDay }}"
            data-is-published="{{ $isPublished ? 1 : 0 }}"
            data-is-archived="{{ $isArchived ? 1 : 0 }}"
            data-is-expired="{{ $isExpired ? 1 : 0 }}">
        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">
            {{ $isPublished ? __('main.change_duration') : __('main.publish_job') }}
        </div>

        <div class="mt-2 text-sm text-slate-600">
            {{ __('main.available_credits') }}: <span class="font-semibold">{{ $availableCredits ?? 0 }}</span>
            @if($isPublished)
                <span class="ml-3 text-slate-500">{{ __('main.current_remaining_days', ['days' => $remainingDays]) }}</span>
            @endif
        </div>

          <form method="POST" action="{{ route('frontend.jobs.post', $job) }}"
              id="job-post-form"
              class="mt-4 grid gap-3 md:grid-cols-[200px_1fr_auto] items-end">
            @csrf

            <div>
                <label class="text-xs uppercase tracking-[0.2em]">{{ __('main.days') }}</label>
                <input type="number" name="days" id="post-days" min="1"
                       value="{{ old('days', $isPublished ? $remainingDays : 7) }}"
                       class="mt-2 w-full pixel-outline px-3 py-2" required>
            </div>

            <div class="text-xs text-slate-600 space-y-1">
                <div>{{ __('main.preview_expiry') }}: <span id="post-expiry">-</span></div>

                {{-- Only visible when extending --}}
                <div id="post-required-wrap" class="hidden">
                    {{ __('main.credits_required') }}: <span id="post-credits">-</span>
                </div>

                {{-- Only visible when shortening --}}
                <div id="post-refund-wrap" class="hidden">
                    {{ __('main.refund_50') }}: <span id="post-refund">-</span>
                </div>

                {{-- Notice when no extension --}}
                <div id="post-free-wrap" class="hidden">
                    {{ __('main.no_extra_credits') }}
                </div>
            </div>

            <button type="submit" id="job-post-submit" class="pixel-button px-6 py-3 text-xs uppercase tracking-[0.2em]">
                {{ $isPublished ? __('main.update_duration') : __('main.publish') }}
            </button>
        </form>

        @if ($isArchived)
            <form method="POST" action="{{ route('frontend.jobs.unarchive', $job) }}" class="mt-4" id="job-unarchive-form">
                @csrf
                <button type="submit" id="job-unarchive-submit" class="pixel-button-light no-hover-move px-6 py-3 text-xs uppercase tracking-[0.2em]">{{ __('main.unarchive') }}</button>
            </form>
        @else
            <form method="POST" action="{{ route('frontend.jobs.archive', $job) }}" class="mt-4" id="job-archive-form">
                @csrf
                <button type="submit" id="job-archive-submit" class="pixel-button-light no-hover-move px-6 py-3 text-xs uppercase tracking-[0.2em]">{{ __('main.archive') }}</button>
            </form>
        @endif
    </div>

    <div id="confirm-modal" class="fixed inset-0 z-50 hidden items-center justify-center">
        <div class="absolute inset-0 bg-slate-900/40"></div>
        <div class="relative w-full max-w-md pixel-frame bg-white p-6">
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.please_confirm') }}</div>
            <div id="confirm-message" class="mt-3 text-sm text-slate-700"></div>
            <div class="mt-6 flex gap-3 justify-end">
                <button type="button" id="confirm-cancel" class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">{{ __('main.cancel') }}</button>
                <button type="button" id="confirm-ok" class="pixel-button px-4 py-2 text-xs uppercase tracking-[0.2em]">{{ __('main.confirm') }}</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    (() => {
            const t = {
                archive_confirm: @json(__('main.archive_confirm')),
                unarchive_confirm: @json(__('main.unarchive_confirm')),
                unarchive_expired_confirm: @json(__('main.unarchive_expired_confirm', ['credits' => ':credits'])),
            };
            const editForm = document.getElementById('job-edit-form');
            const postForm = document.getElementById('job-post-form');
            const postBtn = document.getElementById('job-post-submit');
            const archiveForm = document.getElementById('job-archive-form');
            const archiveBtn = document.getElementById('job-archive-submit');
            const unarchiveForm = document.getElementById('job-unarchive-form');
            const unarchiveBtn = document.getElementById('job-unarchive-submit');

            const modal = document.getElementById('confirm-modal');
            const modalMsg = document.getElementById('confirm-message');
            const modalOk = document.getElementById('confirm-ok');
            const modalCancel = document.getElementById('confirm-cancel');

            const openConfirm = (message, onConfirm) => {
                if (!modal || !modalMsg || !modalOk || !modalCancel) return;
                modalMsg.innerHTML = message;
                modal.classList.remove('hidden');
                modal.classList.add('flex');

                const close = () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    modalOk.onclick = null;
                    modalCancel.onclick = null;
                };

                modalCancel.onclick = () => close();
                modalOk.onclick = () => {
                    close();
                    onConfirm?.();
                };
            };

      const box = document.querySelector('[data-remaining-days][data-credits-per-day]');
      if (!box) return;

    const remainingDays = parseInt(box.dataset.remainingDays || '0', 10);
    const creditsPerDay = parseInt(box.dataset.creditsPerDay || '1', 10);
    const isPublished   = box.dataset.isPublished === '1';
    const isArchived    = box.dataset.isArchived === '1';
    const isExpired     = box.dataset.isExpired === '1';

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

            if (postBtn && editForm && postForm) {
                postBtn.addEventListener('click', async (event) => {
                    // Save main form first, then submit duration/publish
                    event.preventDefault();

                    if (!editForm.checkValidity()) {
                        editForm.requestSubmit();
                        return;
                    }

                    try {
                        const formData = new FormData(editForm);
                        const response = await fetch(editForm.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (response.ok) {
                            postForm.submit();
                        } else {
                            editForm.submit();
                        }
                    } catch (e) {
                        editForm.submit();
                    }
                });
            }

            if (archiveBtn && archiveForm) {
                archiveBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    openConfirm(t.archive_confirm, () => {
                        archiveForm.submit();
                    });
                });
            }

            const getDays = () => Math.max(1, parseInt(daysEl?.value || '1', 10));

            if (unarchiveBtn && unarchiveForm) {
                unarchiveBtn.addEventListener('click', (event) => {
                    event.preventDefault();

                    if (isArchived && isExpired) {
                        const requiredCredits = getDays() * creditsPerDay;
                        const msg = t.unarchive_expired_confirm.replace(':credits', requiredCredits);
                        openConfirm(msg, () => {
                            postForm?.submit();
                        });
                        return;
                    }

                    openConfirm(t.unarchive_confirm, () => {
                        unarchiveForm.submit();
                    });
                });
            }
    })();
    </script>
    @endpush
</x-dashboard.layout>
