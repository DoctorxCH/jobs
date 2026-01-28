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

   

    <script>
        const daysInput = document.getElementById("post-days");
        const expiryEl = document.getElementById("post-expiry");
        const creditsEl = document.getElementById("post-credits");

        const currentExpiresAt = @json(optional($job->expires_at)->toDateString());

        function updatePreview() {
            const days = parseInt(daysInput.value || "0", 10);
            if (!days || days < 1) {
                expiryEl.textContent = "-";
                creditsEl.textContent = "-";
                return;
            }

            const now = new Date();
            const newExpiry = new Date(now);
            newExpiry.setDate(newExpiry.getDate() + days);
            const newExpiryStr = newExpiry.toISOString().slice(0, 10);
            expiryEl.textContent = newExpiryStr;

            // credits only for extending beyond current expiry
            let required = days;
            if (currentExpiresAt) {
                const cur = new Date(currentExpiresAt + "T00:00:00Z");
                const base = cur > now ? cur : now;
                const ms = newExpiry - base;
                required = Math.max(0, Math.ceil(ms / (1000 * 60 * 60 * 24)));
            }

            creditsEl.textContent = required;
        }

        daysInput?.addEventListener("input", updatePreview);
        updatePreview();
    </script>
</x-dashboard.layout>
