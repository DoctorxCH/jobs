<x-dashboard.layout title="{{ __('main.billing_product_title') }}">
    <div class="flex flex-col gap-6">
        <div>
            <a href="{{ route('frontend.billing.products.index') }}"
               class="text-xs uppercase tracking-[0.2em] text-slate-500">← {{ __('main.back_to_products') }}</a>
            <h1 class="mt-2 text-2xl font-bold">{{ $product->name }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ $product->description ?? __('main.no_description_yet') }}
            </p>
        </div>

        <div class="pixel-outline p-6 flex flex-col gap-4">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">
                {{ __('main.pricing') }}
            </div>
            @if ($price)
                <div class="grid gap-2 text-sm text-slate-700">
                    <div>{{ __('main.currency') }}: <span class="font-bold">{{ $price->currency }}</span></div>
                    <div>{{ __('main.net_price') }}: <span class="font-bold">{{ format_money_minor($price->unit_net_amount_minor, $price->currency) }}</span></div>
                    <div>{{ __('main.vat_rate') }}: {{ number_format($taxRate, 2) }}%</div>
                    <div>{{ __('main.vat_amount') }}: {{ format_money_minor($taxMinor, $price->currency) }}</div>
                    <div>{{ __('main.total_price') }}: <span class="font-bold">{{ format_money_minor($grossMinor, $price->currency) }}</span></div>
                </div>
            @else
                <div class="text-sm text-slate-600">{{ __('main.pricing_not_available_for_product') }}</div>
            @endif
        </div>

        <div class="pixel-outline p-6 flex flex-col gap-3 text-sm text-slate-600">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">
                {{ __('main.tax_rule') }}
            </div>
            <div>{{ __('main.rule') }}: <span class="font-bold">{{ $taxRule['tax_rule'] ?? '—' }}</span></div>
            <div>{{ __('main.reverse_charge') }}: <span class="font-bold">{{ ($taxRule['reverse_charge'] ?? false) ? __('main.yes') : __('main.no') }}</span></div>
        </div>

        @if ($price)
            <div>
                <a href="{{ route('frontend.billing.products.checkout', $product) }}"
                   class="inline-flex pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">
                    {{ __('main.proceed_to_checkout') }}
                </a>
            </div>
        @endif
    </div>
</x-dashboard.layout>
