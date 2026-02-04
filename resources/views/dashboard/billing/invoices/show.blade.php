<x-dashboard.layout title="{{ __('main.billing_invoice_title') }}">
    <div class="flex flex-col gap-6">
        <div>
            <a href="{{ route('frontend.billing.invoices.index') }}"
               class="text-xs uppercase tracking-[0.2em] text-slate-500">← {{ __('main.back_to_invoices') }}</a>
            <h1 class="mt-2 text-2xl font-bold">{{ __('main.invoice_number', ['id' => $invoice->id]) }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ __('main.invoice_issued_status', ['date' => $invoice->issued_at?->format('d.m.Y') ?? '—', 'status' => $invoice->status]) }}
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.total') }}</div>
                <div class="mt-2 text-lg font-bold">{{ format_money_minor($invoice->total_gross_minor, $invoice->currency) }}</div>
            </div>
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.due_date') }}</div>
                <div class="mt-2">{{ $invoice->due_at?->format('d.m.Y') ?? '—' }}</div>
            </div>
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.payment_reference') }}</div>
                <div class="mt-2">{{ $invoice->payment_reference }}</div>
            </div>
        </div>

        <div class="pixel-outline p-6 text-sm text-slate-700">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.payment_instructions') }}</div>
            <div class="mt-2">{{ __('main.amount') }}: <span class="font-bold">{{ format_money_minor($invoice->total_gross_minor, $invoice->currency) }}</span></div>
            <div>{{ __('main.reference') }}: <span class="font-bold">{{ $invoice->payment_reference }}</span></div>
            <div>{{ __('main.due') }}: <span class="font-bold">{{ $invoice->due_at?->format('d.m.Y') ?? '—' }}</span></div>
        </div>

        <div class="pixel-outline p-6 flex flex-col gap-2 text-sm text-slate-600">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">PDF</div>
            @if ($invoice->pdf_path || $invoice->external?->external_pdf_url)
                <a href="{{ route('frontend.billing.invoices.download', $invoice) }}"
                   class="inline-flex pixel-outline px-4 py-2 text-xs uppercase tracking-[0.2em]">
                    {{ __('main.download_pdf') }}
                </a>
            @else
                <div>{{ __('main.pdf_not_available') }}</div>
            @endif
        </div>

        @if ($invoice->order)
            <div class="pixel-outline p-6 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.order') }}</div>
                <a href="{{ route('frontend.billing.orders.show', $invoice->order) }}" class="underline">
                    {{ __('main.view_order', ['id' => $invoice->order->id]) }}
                </a>
            </div>
        @endif

        <div class="pixel-outline p-6">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.items') }}</div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-xs uppercase tracking-[0.2em] text-slate-500">
                            <th class="py-2">{{ __('main.item') }}</th>
                            <th class="py-2">{{ __('main.qty') }}</th>
                            <th class="py-2">{{ __('main.unit') }}</th>
                            <th class="py-2">{{ __('main.tax') }}</th>
                            <th class="py-2">{{ __('main.total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($invoice->items as $item)
                            <tr>
                                <td class="py-3 font-bold">{{ $item->name_snapshot }}</td>
                                <td class="py-3">{{ $item->qty }}</td>
                                <td class="py-3">{{ format_money_minor($item->unit_net_minor, $invoice->currency) }}</td>
                                <td class="py-3">{{ format_money_minor($item->tax_minor, $invoice->currency) }}</td>
                                <td class="py-3">{{ format_money_minor($item->total_gross_minor, $invoice->currency) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 text-slate-600" colspan="5">{{ __('main.no_items_recorded') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pixel-outline p-6">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.status_history') }}</div>
            <div class="mt-4 space-y-2 text-sm text-slate-600">
                @forelse ($invoice->statusHistory as $history)
                    <div class="flex flex-col gap-1">
                        <div class="font-bold">{{ $history->to_status }}</div>
                        <div>{{ $history->changed_at?->format('d.m.Y H:i') ?? '—' }}</div>
                        @if ($history->note)
                            <div>{{ $history->note }}</div>
                        @endif
                    </div>
                @empty
                    <div>{{ __('main.no_status_history') }}</div>
                @endforelse
            </div>
        </div>

        <div class="pixel-outline p-6">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">{{ __('main.payments') }}</div>
            <div class="mt-4 space-y-2 text-sm text-slate-600">
                @forelse ($invoice->payments as $payment)
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-bold">{{ format_money_minor($payment->amount_minor, $payment->currency) }}</span>
                            <span class="text-slate-500">· {{ $payment->status }}</span>
                        </div>
                        <a href="{{ route('frontend.billing.payments.show', $payment) }}" class="underline text-xs uppercase tracking-[0.2em]">
                            {{ __('main.view_payment') }}
                        </a>
                    </div>
                @empty
                    <div>{{ __('main.no_payments_recorded') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</x-dashboard.layout>
