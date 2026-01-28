<x-dashboard.layout title="Edit job">
    <form method="POST" action="{{ route('frontend.jobs.update', $job) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('dashboard.jobs._form')

        <div class="flex flex-wrap gap-3 justify-end">
            <button type="submit" class="pixel-outline px-6 py-2 text-xs uppercase tracking-[0.2em]">Save changes</button>
        </div>
    </form>

    <div class="mt-10 pixel-outline p-4">
        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Publish job</div>
        <div class="mt-2 text-sm text-slate-600">Available credits: <span class="font-semibold">{{ $availableCredits }}</span></div>

        <form method="POST" action="{{ route('frontend.jobs.post', $job) }}" class="mt-4 grid gap-3 md:grid-cols-[200px_1fr_auto] items-end">
            @csrf
            <div>
                <label class="text-xs uppercase tracking-[0.2em]">Days</label>
                <input type="number" name="days" id="post-days" min="1" value="{{ old('days', 7) }}" class="mt-2 w-full pixel-outline px-3 py-2" required>
            </div>
            <div class="text-xs text-slate-600">
                <div>Preview expiry: <span id="post-expiry">-</span></div>
                <div>Credits required: <span id="post-credits">-</span></div>
            </div>
            <button type="submit" class="pixel-outline px-6 py-2 text-xs uppercase tracking-[0.2em]">Publish</button>
        </form>

        <form method="POST" action="{{ route('frontend.jobs.archive', $job) }}" class="mt-4">
            @csrf
            <button type="submit" class="pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">Archive</button>
        </form>
    </div>

    <script>
        const daysInput = document.getElementById('post-days');
        const expiryEl = document.getElementById('post-expiry');
        const creditsEl = document.getElementById('post-credits');

        function updatePreview() {
            const days = parseInt(daysInput.value || '0', 10);
            if (!days || days < 1) {
                expiryEl.textContent = '-';
                creditsEl.textContent = '-';
                return;
            }
            const expires = new Date();
            expires.setDate(expires.getDate() + days);
            expiryEl.textContent = expires.toISOString().slice(0, 10);
            creditsEl.textContent = days;
        }

        daysInput?.addEventListener('input', updatePreview);
        updatePreview();
    </script>
</x-dashboard.layout>
