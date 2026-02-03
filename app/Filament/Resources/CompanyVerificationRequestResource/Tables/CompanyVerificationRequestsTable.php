<?php

namespace App\Filament\Resources\CompanyVerificationRequestResource\Tables;

use App\Filament\Resources\Billing\InvoiceResource;
use App\Filament\Resources\Billing\PaymentResource;
use App\Models\CompanyVerificationRequest;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CompanyVerificationRequestsTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.legal_name')
                    ->label('Company')
                    ->searchable(),
                Tables\Columns\TextColumn::make('method')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('requestedBy.name')
                    ->label('Requested by')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('requested_by_email')
                    ->label('Requested email')
                    ->copyable(),
                Tables\Columns\TextColumn::make('code_sent_to_email')
                    ->label('Code sent to')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('auto_verified_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('invoice_id')
                    ->label('Invoice')
                    ->formatStateUsing(fn ($state) => $state ? "#{$state}" : '-')
                    ->url(fn (CompanyVerificationRequest $record) => $record->invoice_id ? InvoiceResource::getUrl('view', ['record' => $record->invoice_id]) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('payment_id')
                    ->label('Payment')
                    ->formatStateUsing(fn ($state) => $state ? "#{$state}" : '-')
                    ->url(fn (CompanyVerificationRequest $record) => $record->payment_id ? PaymentResource::getUrl('view', ['record' => $record->payment_id]) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('ack_status')
                    ->badge(),
                Tables\Columns\TextColumn::make('ack_at')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('ack_status')
                    ->options([
                        'pending' => 'Pending',
                        'ok' => 'OK',
                        'flagged' => 'Flagged',
                    ]),
                SelectFilter::make('method')
                    ->options([
                        'code' => 'Code',
                        'invoice' => 'Invoice',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'code_sent' => 'Code sent',
                        'auto_verified' => 'Auto verified',
                        'awaiting_payment' => 'Awaiting payment',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'expired' => 'Expired',
                        'canceled' => 'Canceled',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('markOk')
                    ->label('Mark OK')
                    ->visible(fn (CompanyVerificationRequest $record) => $record->ack_status !== 'ok')
                    ->action(function (CompanyVerificationRequest $record): void {
                        $record->update([
                            'ack_status' => 'ok',
                            'ack_at' => now(),
                            'ack_by' => auth()->id(),
                        ]);

                        if ($record->company) {
                            $record->company->update([
                                'verification_ack_status' => 'ok',
                                'verification_ack_at' => now(),
                                'verification_ack_by' => auth()->id(),
                            ]);
                        }
                    }),
                Tables\Actions\Action::make('flag')
                    ->label('Flag')
                    ->form([
                        Textarea::make('admin_note')
                            ->label('Admin note')
                            ->required(),
                    ])
                    ->action(function (CompanyVerificationRequest $record, array $data): void {
                        $record->update([
                            'ack_status' => 'flagged',
                            'ack_at' => now(),
                            'ack_by' => auth()->id(),
                            'admin_note' => $data['admin_note'] ?? null,
                        ]);

                        if ($record->company) {
                            $record->company->update([
                                'verification_ack_status' => 'flagged',
                                'verification_ack_at' => now(),
                                'verification_ack_by' => auth()->id(),
                                'verification_ack_note' => $data['admin_note'] ?? null,
                            ]);
                        }
                    }),
                Tables\Actions\Action::make('approveInvoice')
                    ->label('Approve Invoice & Verify')
                    ->visible(fn (CompanyVerificationRequest $record) => $record->method === 'invoice')
                    ->action(function (CompanyVerificationRequest $record): void {
                        $record->update([
                            'status' => 'approved',
                            'ack_status' => 'ok',
                            'ack_at' => now(),
                            'ack_by' => auth()->id(),
                        ]);

                        if ($record->company) {
                            $record->company->forceFill([
                                'verified_at' => $record->company->verified_at ?? now(),
                                'verified_method' => 'invoice',
                                'verified_by_user_id' => $record->requested_by_user_id,
                                'verified_by_email' => $record->requested_by_email,
                                'verification_ack_status' => 'ok',
                                'verification_ack_at' => now(),
                                'verification_ack_by' => auth()->id(),
                            ])->save();
                        }
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
