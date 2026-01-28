<x-dashboard.layout title="Billing · Checkout">
    <div class="flex flex-col gap-6">
        <div>
            <a href="{{ route('frontend.billing.products.show', $product) }}"
               class="text-xs uppercase tracking-[0.2em] text-slate-500">← Back to product</a>
            <h1 class="mt-2 text-2xl font-bold">Checkout</h1>
            <p class="mt-2 text-sm text-slate-600">
                Confirm your order details before placing the order.
            </p>
        </div>

        <div class="pixel-outline p-6 flex flex-col gap-4">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Product</div>
            <div class="text-lg font-bold">{{ $product->name }}</div>
            <div class="text-sm text-slate-600">{{ $product->description ?? '—' }}</div>
        </div>

        <div class="pixel-outline p-6 flex flex-col gap-3 text-sm text-slate-700">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Order summary</div>
            <div>Net price: <span class="font-bold">{{ format_money_minor($price->unit_net_amount_minor, $price->currency) }}</span></div>
            <div>VAT rate: {{ number_format($taxRate, 2) }}%</div>
            <div>VAT amount: {{ format_money_minor($taxMinor, $price->currency) }}</div>
            <div>Total: <span class="font-bold">{{ format_money_minor($grossMinor, $price->currency) }}</span></div>
        </div>

        <div class="pixel-outline p-6 text-sm text-slate-600">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Tax rule</div>
            <div class="mt-2">Rule: <span class="font-bold">{{ $taxRule['tax_rule'] ?? '—' }}</span></div>
            <div>Reverse charge: <span class="font-bold">{{ ($taxRule['reverse_charge'] ?? false) ? 'Yes' : 'No' }}</span></div>
        </div>

        <form method="POST" action="{{ route('frontend.billing.products.checkout.store', $product) }}">
            @csrf
            <button type="submit"
                    class="inline-flex pixel-outline px-6 py-3 text-xs uppercase tracking-[0.2em]">
                Place order
            </button>
        </form>
    </div>
</x-dashboard.layout>
