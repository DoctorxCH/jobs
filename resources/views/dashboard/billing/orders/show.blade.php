<x-dashboard.layout title="Billing · Order">
    <div class="flex flex-col gap-6">
        <div>
            <a href="{{ route('frontend.billing.orders.index') }}"
               class="text-xs uppercase tracking-[0.2em] text-slate-500">← Back to orders</a>
            <h1 class="mt-2 text-2xl font-bold">Order #{{ $order->id }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                Created {{ $order->created_at?->format('d.m.Y H:i') ?? '—' }} · Status {{ $order->status }}
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Total</div>
                <div class="mt-2 text-lg font-bold">{{ format_money_minor($order->total_gross_minor, $order->currency) }}</div>
            </div>
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Tax</div>
                <div class="mt-2">{{ format_money_minor($order->tax_minor, $order->currency) }}</div>
            </div>
            <div class="pixel-outline p-4 text-sm text-slate-600">
                <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Invoice</div>
                <div class="mt-2">
                    @if ($invoice)
                        <a href="{{ route('frontend.billing.invoices.show', $invoice) }}" class="underline">
                            View invoice #{{ $invoice->id }}
                        </a>
                    @else
                        —
                    @endif
                </div>
            </div>
        </div>

        <div class="pixel-outline p-6">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Items</div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-xs uppercase tracking-[0.2em] text-slate-500">
                            <th class="py-2">Item</th>
                            <th class="py-2">Qty</th>
                            <th class="py-2">Unit</th>
                            <th class="py-2">Tax</th>
                            <th class="py-2">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($order->items as $item)
                            <tr>
                                <td class="py-3 font-bold">{{ $item->name_snapshot }}</td>
                                <td class="py-3">{{ $item->qty }}</td>
                                <td class="py-3">{{ format_money_minor($item->unit_net_minor, $order->currency) }}</td>
                                <td class="py-3">{{ format_money_minor($item->tax_minor, $order->currency) }}</td>
                                <td class="py-3">{{ format_money_minor($item->total_gross_minor, $order->currency) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 text-slate-600" colspan="5">No items recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pixel-outline p-6">
            <div class="text-[10px] uppercase tracking-[0.28em] text-slate-500">Status history</div>
            <div class="mt-4 space-y-2 text-sm text-slate-600">
                @forelse ($order->statusHistory as $history)
                    <div class="flex flex-col gap-1">
                        <div class="font-bold">{{ $history->to_status }}</div>
                        <div>{{ $history->changed_at?->format('d.m.Y H:i') ?? '—' }}</div>
                        @if ($history->note)
                            <div>{{ $history->note }}</div>
                        @endif
                    </div>
                @empty
                    <div>No status history recorded.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-dashboard.layout>
