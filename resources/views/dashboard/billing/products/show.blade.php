<x-dashboard.layout title="Billing · Product">
    <div class="flex flex-col gap-6">
        <div>
            <a href="{{ route('frontend.billing.products.index') }}"
               class="text-xs uppercase tracking-[0.2em] text-slate-500">← Back to products</a>
            <h1 class="mt-2 text-2xl font-bold">{{ $product->name }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ $product->description ?? 'No description provided yet.' }}
            </p>
        </div>

        <div class="pixel-outline p-6 flex flex-col gap-4">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">
                Pricing
            </div>
            @if ($price)
                <div class="grid gap-2 text-sm text-slate-700">
                    <div>Currency: <span class="font-bold">{{ $price->currency }}</span></div>
                    <div>Net price: <span class="font-bold">{{ format_money_minor($price->unit_net_amount_minor, $price->currency) }}</span></div>
                    <div>VAT rate: {{ number_format($taxRate, 2) }}%</div>
                    <div>VAT amount: {{ format_money_minor($taxMinor, $price->currency) }}</div>
                    <div>Total price: <span class="font-bold">{{ format_money_minor($grossMinor, $price->currency) }}</span></div>
                </div>
            @else
                <div class="text-sm text-slate-600">Pricing not available for this product.</div>
            @endif
        </div>

        <div class="pixel-outline p-6 flex flex-col gap-3 text-sm text-slate-600">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">
                Tax rule
            </div>
            <div>Rule: <span class="font-bold">{{ $taxRule['tax_rule'] ?? '—' }}</span></div>
            <div>Reverse charge: <span class="font-bold">{{ ($taxRule['reverse_charge'] ?? false) ? 'Yes' : 'No' }}</span></div>
        </div>

        @if ($price)
            <div>
                <a href="{{ route('frontend.billing.products.checkout', $product) }}"
                   class="inline-flex pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">
                    Proceed to checkout
                </a>
            </div>
        @endif
    </div>
</x-dashboard.layout>
