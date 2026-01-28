<x-dashboard.layout title="Billing · Invoices">
    <div class="flex flex-col gap-6">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Billing</div>
            <h1 class="mt-2 text-2xl font-bold">Invoices</h1>
            <p class="mt-2 text-sm text-slate-600">
                Track issued invoices and their payment status.
            </p>
        </div>

        @if ($invoices->isEmpty())
            <div class="pixel-outline p-6 text-sm text-slate-600">
                No invoices yet.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-xs uppercase tracking-[0.2em] text-slate-500">
                            <th class="py-2">Invoice</th>
                            <th class="py-2">Issued</th>
                            <th class="py-2">Due</th>
                            <th class="py-2">Status</th>
                            <th class="py-2">Total</th>
                            <th class="py-2">Sync</th>
                            <th class="py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($invoices as $invoice)
                            <tr>
                                <td class="py-3 font-bold">#{{ $invoice->id }}</td>
                                <td class="py-3">{{ $invoice->issued_at?->format('d.m.Y') ?? '—' }}</td>
                                <td class="py-3">{{ $invoice->due_at?->format('d.m.Y') ?? '—' }}</td>
                                <td class="py-3">{{ $invoice->status }}</td>
                                <td class="py-3">{{ format_money_minor($invoice->total_gross_minor, $invoice->currency) }}</td>
                                <td class="py-3">{{ $invoice->external?->sync_status ?? '—' }}</td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('frontend.billing.invoices.show', $invoice) }}"
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
