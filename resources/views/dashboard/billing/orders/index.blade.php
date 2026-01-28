<x-dashboard.layout title="Billing · Orders">
    <div class="flex flex-col gap-6">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Billing</div>
            <h1 class="mt-2 text-2xl font-bold">Orders</h1>
            <p class="mt-2 text-sm text-slate-600">
                Review orders placed by your company.
            </p>
        </div>

        @if ($orders->isEmpty())
            <div class="pixel-outline p-6 text-sm text-slate-600">
                No orders yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-xs uppercase tracking-[0.2em] text-slate-500">
                            <th class="py-2">Order</th>
                            <th class="py-2">Date</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Total</th>
                            <th class="py-2">Invoice</th>
                            <th class="py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($orders as $order)
                            @php
                                $invoice = $invoices->get($order->id);
                            @endphp
                            <tr>
                                <td class="py-3 font-bold">#{{ $order->id }}</td>
                                <td class="py-3">{{ $order->created_at?->format('d.m.Y') ?? '—' }}</td>
                                <td class="py-3">{{ $order->status }}</td>
                                <td class="py-3">{{ format_money_minor($order->total_gross_minor, $order->currency) }}</td>
                                <td class="py-3">
                                    @if ($invoice)
                                        <a href="{{ route('frontend.billing.invoices.show', $invoice) }}"
                                           class="underline">
                                            #{{ $invoice->id }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('frontend.billing.orders.show', $order) }}"
                                       class="inline-flex pixel-outline px-3 py-1 text-xs uppercase tracking-[0.2em]">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-dashboard.layout>
