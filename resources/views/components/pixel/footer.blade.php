<footer class="px-6 pb-12 text-xs text-slate-500">
    <div class="mx-auto w-full max-w-6xl border-t border-slate-300 pt-6">
        <div class="grid gap-6 md:grid-cols-3">
            <div class="space-y-2">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.footer_company') }}</div>
                <div class="text-sm font-semibold text-slate-700">M&amp;M Media s. r. o.</div>
                <div>Pražská 11, 811 04 Bratislava – Staré Mesto</div>
                <div>IČO: 48 090 727</div>
                <div>
                    <a href="mailto:support@365jobs.sk" class="underline hover:text-slate-700">support@365jobs.sk</a>
                </div>
            </div>

            <div class="space-y-2">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.footer_legal') }}</div>
                <div class="flex flex-col gap-2 text-sm">
                    <a href="{{ route('legal.agb') }}" class="underline hover:text-slate-700">{{ __('main.footer_terms') }}</a>
                    <a href="{{ route('legal.privacy') }}" class="underline hover:text-slate-700">{{ __('main.footer_privacy') }}</a>
                </div>
            </div>

            <div class="space-y-2">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.footer_info') }}</div>
                <div class="text-sm">
                    365jobs.sk
                </div>
                <div class="text-sm uppercase tracking-[0.2em]">Remote • Local • Clear</div>
            </div>
        </div>
    </div>
</footer>
