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

        <form method="POST"
              action="{{ route('frontend.billing.products.checkout.store', $product) }}"
              class="pixel-outline p-6 flex flex-col gap-5"
              id="checkoutForm"
              data-currency="{{ $price->currency }}"
              data-unit-net-minor="{{ (int) $price->unit_net_amount_minor }}"
              data-unit-tax-minor="{{ (int) ($unitTaxMinor ?? 0) }}"
              data-unit-gross-minor="{{ (int) ($unitGrossMinor ?? 0) }}"
        >
            @csrf

            <div>
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Quantity</div>

                <div class="mt-2 flex items-center gap-3">
                    <input
                        id="qty"
                        name="qty"
                        type="range"
                        min="1"
                        max="100"
                        step="1"
                        value="{{ (int) old('qty', $qty ?? 1) }}"
                        class="w-full"
                    />

                    <div class="min-w-[3rem] text-right tabular-nums">
                        <span id="qtyValue">{{ (int) old('qty', $qty ?? 1) }}</span>x
                    </div>
                </div>

                @error('qty')
                    <div class="mt-2 text-xs text-red-600">{{ $message }}</div>
                @enderror

                <div class="mt-1 text-xs text-slate-500">1–100</div>
            </div>

            <div class="pixel-outline p-4 flex flex-col gap-2 text-sm text-slate-700">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Order summary</div>

                <div class="flex justify-between gap-3">
                    <span>Unit net price</span>
                    <span class="font-bold" id="unitNetDisplay">
                        {{ format_money_minor($price->unit_net_amount_minor, $price->currency) }}
                    </span>
                </div>

                <div class="flex justify-between gap-3">
                    <span>VAT rate</span>
                    <span>{{ number_format($taxRate, 2) }}%</span>
                </div>

                <div class="flex justify-between gap-3">
                    <span>Unit VAT amount</span>
                    <span id="unitTaxDisplay">
                        {{ format_money_minor($unitTaxMinor ?? 0, $price->currency) }}
                    </span>
                </div>

                <div class="flex justify-between gap-3">
                    <span>Unit gross total</span>
                    <span class="font-bold" id="unitGrossDisplay">
                        {{ format_money_minor($unitGrossMinor ?? 0, $price->currency) }}
                    </span>
                </div>

                <div class="h-px bg-slate-200 my-2"></div>

                <div class="flex justify-between gap-3">
                    <span>Subtotal net (qty)</span>
                    <span class="font-bold" id="subtotalNetDisplay">
                        {{ format_money_minor($subtotalNetMinor ?? (int) $price->unit_net_amount_minor, $price->currency) }}
                    </span>
                </div>

                <div class="flex justify-between gap-3">
                    <span>VAT amount (qty)</span>
                    <span id="taxDisplay">
                        {{ format_money_minor($taxMinor ?? 0, $price->currency) }}
                    </span>
                </div>

                <div class="flex justify-between gap-3">
                    <span>Total (qty)</span>
                    <span class="font-bold" id="grossDisplay">
                        {{ format_money_minor($grossMinor ?? 0, $price->currency) }}
                    </span>
                </div>
            </div>

            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Tax rule</div>
                <div class="mt-2">Rule: <span class="font-bold">{{ $taxRule['tax_rule'] ?? '—' }}</span></div>
                <div>Reverse charge: <span class="font-bold">{{ ($taxRule['reverse_charge'] ?? false) ? 'Yes' : 'No' }}</span></div>
            </div>

            <button type="submit"
                    class="inline-flex pixel-outline px-6 py-3 text-xs uppercase tracking-[0.2em]">
                Place order
            </button>
        </form>
    </div>

    <script>
        (function () {
            const form = document.getElementById('checkoutForm');
            const qty = document.getElementById('qty');
            const qtyValue = document.getElementById('qtyValue');

            const subtotalNetDisplay = document.getElementById('subtotalNetDisplay');
            const taxDisplay = document.getElementById('taxDisplay');
            const grossDisplay = document.getElementById('grossDisplay');

            if (!form || !qty || !qtyValue || !subtotalNetDisplay || !taxDisplay || !grossDisplay) return;

            const currency = form.dataset.currency || '';
            const unitNetMinor = parseInt(form.dataset.unitNetMinor || '0', 10);
            const unitTaxMinor = parseInt(form.dataset.unitTaxMinor || '0', 10);
            const unitGrossMinor = parseInt(form.dataset.unitGrossMinor || '0', 10);

            function money(minor) {
                const v = (minor / 100).toFixed(2);
                return currency ? (v + ' ' + currency) : v;
            }

            function update() {
                const q = Math.max(1, Math.min(100, parseInt(qty.value || '1', 10)));

                qty.value = String(q);
                qtyValue.textContent = String(q);

                subtotalNetDisplay.textContent = money(unitNetMinor * q);
                taxDisplay.textContent = money(unitTaxMinor * q);
                grossDisplay.textContent = money(unitGrossMinor * q);
            }

            qty.addEventListener('input', update);
            update();
        })();
    </script>
</x-dashboard.layout>
