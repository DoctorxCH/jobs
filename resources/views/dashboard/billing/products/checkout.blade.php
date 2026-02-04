<x-dashboard.layout title="{{ __('main.billing_checkout_title') }}">
    <div class="flex flex-col gap-6">
        <div>
            <a href="{{ route('frontend.billing.products.show', $product) }}"
               class="text-xs uppercase tracking-[0.2em] text-slate-500">← {{ __('main.back_to_product') }}</a>
            <h1 class="mt-2 text-2xl font-bold">{{ __('main.checkout') }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ __('main.checkout_confirm') }}
            </p>
        </div>

        @if (session('error'))
            <div class="pixel-outline p-4 bg-red-50 text-red-700 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="pixel-outline p-6 flex flex-col gap-4">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.product') }}</div>
            <div class="text-lg font-bold">{{ $product->name }}</div>
            <div class="text-sm text-slate-600 whitespace-pre-line leading-6">{{ $product->description ?? '—' }}</div>
        </div>

        @php
            $appliedCoupons = session('checkout.coupons', []);
        @endphp

        <div class="pixel-outline p-6 flex flex-col gap-4">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.coupons') }}</div>

            <form method="POST"
                  action="{{ route('frontend.billing.coupons.apply') }}"
                  class="flex flex-col gap-3"
                  id="couponApplyForm">
                @csrf

                <div class="flex flex-col gap-3 md:flex-row md:items-end">
                    <div class="flex-1">
                        <label class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.coupon_code') }}</label>
                        <input
                            name="coupon_code"
                            class="mt-2 pixel-input w-full px-4 py-3 text-sm text-slate-900 outline-none"
                            placeholder="{{ __('main.coupon_code_placeholder') }}"
                            value="{{ old('coupon_code') }}"
                        />
                        @error('coupon_code')
                            <div class="mt-2 text-xs text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit"
                            class="pixel-button inline-flex pixel-outline px-6 py-3 text-xs uppercase tracking-[0.2em]">
                        {{ __('main.apply') }}
                    </button>
                </div>

                <input type="hidden" name="qty" id="couponQty" value="{{ (int) old('qty', $qty ?? 1) }}" />
                <input type="hidden" name="unit_net_minor" id="couponUnitNetMinor" value="{{ (int) $price->unit_net_amount_minor }}" />
                <input type="hidden" name="currency" value="{{ $price->currency }}" />
                <input type="hidden" name="product_id" value="{{ $product->id }}" />
                @if(isset($product->category_id))
                    <input type="hidden" name="category_id" value="{{ $product->category_id }}" />
                @endif
            </form>

            @if (session('coupon_applied'))
                <div class="text-xs text-green-700">{{ session('coupon_applied') }}</div>
            @endif

            @if (session('coupon_removed'))
                <div class="text-xs text-slate-600">{{ session('coupon_removed') }}</div>
            @endif

            @if (!empty($appliedCoupons))
                <div class="flex flex-col gap-2 text-sm text-slate-700">
                    @foreach ($appliedCoupons as $coupon)
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="font-semibold">{{ $coupon['code'] ?? '' }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ $coupon['name'] ?? '' }}
                                    @if (!empty($coupon['discount_type']) && isset($coupon['discount_value']))
                                        · {{ $coupon['discount_type'] === 'percent' ? $coupon['discount_value'].'%' : $coupon['discount_value'] }}
                                    @endif
                                </div>
                            </div>

                            <form method="POST" action="{{ route('frontend.billing.coupons.remove') }}">
                                @csrf
                                <input type="hidden" name="coupon_code" value="{{ $coupon['code'] ?? '' }}" />
                                <button type="submit" class="pixel-button-light text-xs uppercase tracking-[0.2em] text-slate-500 hover:text-slate-900">
                                    {{ __('main.remove') }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-sm text-slate-500">{{ __('main.no_coupons_applied') }}</div>
            @endif
        </div>

        <form method="POST"
              action="{{ route('frontend.billing.products.checkout.store', $product) }}"
              class="pixel-outline p-6 w-3/5 flex flex-col gap-5"
              id="checkoutForm"
              data-currency="{{ $price->currency }}"
              data-unit-net-minor="{{ (int) $price->unit_net_amount_minor }}"
              data-unit-tax-minor="{{ (int) ($unitTaxMinor ?? 0) }}"
              data-unit-gross-minor="{{ (int) ($unitGrossMinor ?? 0) }}"
              data-coupons='@json(array_values($appliedCoupons))'
        >
            @csrf

            @foreach ($appliedCoupons as $coupon)
                <input type="hidden" name="coupon_codes[]" value="{{ $coupon['code'] ?? '' }}" />
            @endforeach

            <div>
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.quantity') }}</div>

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
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.order_summary') }}</div>

                <div class="flex justify-between gap-3">
                    <span>{{ __('main.unit_net_price') }}</span>
                    <span class="font-bold" id="unitNetDisplay">
                        {{ format_money_minor($price->unit_net_amount_minor, $price->currency) }}
                    </span>
                </div>

                <div class="flex justify-between gap-3">
                    <span>{{ __('main.vat_rate') }}</span>
                    <span>{{ number_format($taxRate, 2) }}%</span>
                </div>

                <div class="flex justify-between gap-3">
                    <span>{{ __('main.unit_vat_amount') }}</span>
                    <span id="unitTaxDisplay">
                        {{ format_money_minor($unitTaxMinor ?? 0, $price->currency) }}
                    </span>
                </div>

                <div class="flex justify-between gap-3">
                    <span>{{ __('main.unit_gross_total') }}</span>
                    <span class="font-bold" id="unitGrossDisplay">
                        {{ format_money_minor($unitGrossMinor ?? 0, $price->currency) }}
                    </span>
                </div>

                <div class="h-px bg-slate-200 my-2"></div>

                <div class="flex justify-between gap-3">
                    <span>{{ __('main.subtotal_net_qty') }}</span>
                    <span class="font-bold" id="subtotalNetDisplay">
                        {{ format_money_minor($subtotalNetMinor ?? (int) $price->unit_net_amount_minor, $price->currency) }}
                    </span>
                </div>

                <div class="flex justify-between gap-3">
                    <span>{{ __('main.vat_amount_qty') }}</span>
                    <span id="taxDisplay">
                        {{ format_money_minor($taxMinor ?? 0, $price->currency) }}
                    </span>
                </div>

                <div class="flex justify-between gap-3">
                    <span>{{ __('main.discounts') }}</span>
                    <span id="discountDisplay">
                        {{ format_money_minor(0, $price->currency) }}
                    </span>
                </div>

                <div class="flex justify-between gap-3">
                    <span>{{ __('main.total_qty') }}</span>
                    <span class="font-bold" id="grossDisplay">
                        {{ format_money_minor($grossMinor ?? 0, $price->currency) }}
                    </span>
                </div>

                <div class="flex justify-between gap-3">
                    <span>{{ __('main.total_after_discount') }}</span>
                    <span class="font-bold" id="grossAfterDiscountDisplay">
                        {{ format_money_minor($grossMinor ?? 0, $price->currency) }}
                    </span>
                </div>
            </div>

            @php
                $taxClassKey = $product->taxClass?->key ?? $price?->taxClass?->key ?? null;
                if (! $taxClassKey) {
                    $taxClassId = $product->tax_class_id ?? $price?->tax_class_id ?? null;
                    if ($taxClassId) {
                        $taxClassKey = \Illuminate\Support\Facades\DB::table('tax_classes')
                            ->where('id', $taxClassId)
                            ->value('key');
                    }
                }
            @endphp

            @if ($taxClassKey === 'neplatca')
                <div class="pixel-outline p-4 text-sm text-slate-600">
                    {{ __('main.not_vat_payer') }}
                </div>
            @endif

            <div class="flex items-center gap-2 p-1">
                <input type="checkbox" name="legal_consent" id="legal_consent" required class="w-4 h-4 accent-indigo-600 cursor-pointer">
                <label for="legal_consent" class="text-sm text-slate-600 cursor-pointer select-none">
                    {{ __('main.legal_agree_1') }}
                    <a href="{{ route('legal.agb') }}" target="_blank" class="underline hover:text-[var(--accent)] transition-colors">
                        {{ __('main.legal_terms_link') }}
                    </a>
                </label>
            </div>

            <button type="submit"
                    class="pixel-button inline-flex pixel-outline px-6 py-3 text-xs uppercase tracking-[0.2em]">
                {{ __('main.place_order') }}
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
            const discountDisplay = document.getElementById('discountDisplay');
            const grossAfterDiscountDisplay = document.getElementById('grossAfterDiscountDisplay');
            const couponQtyInput = document.getElementById('couponQty');

            if (!form || !qty || !qtyValue || !subtotalNetDisplay || !taxDisplay || !grossDisplay) return;

            const currency = form.dataset.currency || '';
            const unitNetMinor = parseInt(form.dataset.unitNetMinor || '0', 10);
            const unitTaxMinor = parseInt(form.dataset.unitTaxMinor || '0', 10);
            const unitGrossMinor = parseInt(form.dataset.unitGrossMinor || '0', 10);
            const coupons = JSON.parse(form.dataset.coupons || '[]');

            function money(minor) {
                const v = (minor / 100).toFixed(2);
                return currency ? (v + ' ' + currency) : v;
            }

            function update() {
                const q = Math.max(1, Math.min(100, parseInt(qty.value || '1', 10)));

                qty.value = String(q);
                qtyValue.textContent = String(q);

                if (couponQtyInput) {
                    couponQtyInput.value = String(q);
                }

                const subtotalNet = unitNetMinor * q;
                const taxTotal = unitTaxMinor * q;
                const grossTotal = unitGrossMinor * q;

                subtotalNetDisplay.textContent = money(subtotalNet);
                taxDisplay.textContent = money(taxTotal);
                grossDisplay.textContent = money(grossTotal);

                const discountTotal = calculateDiscountTotal(subtotalNet, coupons);
                if (discountDisplay) {
                    discountDisplay.textContent = money(discountTotal);
                }
                if (grossAfterDiscountDisplay) {
                    const discountedGross = Math.max(0, grossTotal - discountTotal);
                    grossAfterDiscountDisplay.textContent = money(discountedGross);
                }
            }

            function calculateDiscountTotal(subtotalNetMinor, appliedCoupons) {
                let remaining = subtotalNetMinor;
                let total = 0;

                appliedCoupons.forEach((coupon) => {
                    if (remaining <= 0) return;

                    const type = coupon.discount_type || '';
                    const value = parseFloat(coupon.discount_value || 0);
                    let discount = 0;

                    if (type === 'percent') {
                        discount = Math.round(remaining * (value / 100));
                    } else if (type === 'fixed') {
                        discount = Math.round(value * 100);
                    }

                    if (coupon.max_discount_amount_minor) {
                        discount = Math.min(discount, parseInt(coupon.max_discount_amount_minor, 10));
                    }

                    discount = Math.max(0, Math.min(discount, remaining));
                    total += discount;
                    remaining -= discount;
                });

                return total;
            }

            qty.addEventListener('input', update);
            update();
        })();
    </script>
</x-dashboard.layout>
