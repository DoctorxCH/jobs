<x-dashboard.layout title="{{ __('main.billing_payments_title') }}">
    <div class="flex flex-col gap-6">
        <div>
            <div class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('main.billing') }}</div>
            <h1 class="mt-2 text-2xl font-bold">{{ __('main.payments') }}</h1>
            <p class="mt-2 text-sm text-slate-600">
                {{ __('main.payments_intro') }}
            </p>
        </div>

        @if ($payments->isEmpty())
            <div class="pixel-outline p-6 text-sm text-slate-600">
                {{ __('main.no_payments_yet') }}
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-xs uppercase tracking-[0.2em] text-slate-500">
                            <th class="py-2">{{ __('main.payment') }}</th>
                            <th class="py-2">{{ __('main.date') }}</th>
                            <th class="py-2">{{ __('main.status') }}</th>
                            <th class="py-2">{{ __('main.amount') }}</th>
                            <th class="py-2">{{ __('main.invoice') }}</th>
                            <th class="py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($payments as $payment)
                            <tr>
                                <td class="py-3 font-bold">#{{ $payment->id }}</td>
                                <td class="py-3">{{ $payment->created_at?->format('d.m.Y') ?? '—' }}</td>
                                <td class="py-3">{{ $payment->status }}</td>
                                <td class="py-3">{{ format_money_minor($payment->amount_minor, $payment->currency) }}</td>
                                <td class="py-3">
                                    @if ($payment->invoice)
                                        <a href="{{ route('frontend.billing.invoices.show', $payment->invoice) }}" class="underline">
                                            #{{ $payment->invoice->id }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('frontend.billing.payments.show', $payment) }}"
                                       class="inline-flex pixel-outline px-3 py-1 text-xs uppercase tracking-[0.2em]">
                                        {{ __('main.view') }}
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
