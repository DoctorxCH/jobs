<?php

namespace App\Filament\Resources\ContactRequestResource\Pages;

use App\Filament\Resources\ContactRequestResource;
use App\Models\ContactSetting;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditContactRequest extends EditRecord
{
    protected static string $resource = ContactRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sendReply')
                ->label('Send reply')
                ->icon('heroicon-o-paper-airplane')
                ->requiresConfirmation()
                ->action(function () {
                    $record = $this->record;
                    $replyBody = (string) ($record->reply_body ?? '');

                    if (trim(strip_tags($replyBody)) === '') {
                        Notification::make()
                            ->title('Reply body is empty.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $settings = ContactSetting::query()->first();
                    $fromAddress = $settings?->outbox_email;

                    Mail::html($replyBody, function ($message) use ($record, $fromAddress) {
                        $subject = $record->subject ? 'Re: ' . $record->subject : 'Reply';
                        $message->to($record->email, $record->name)->subject($subject);
                        if ($fromAddress) {
                            $message->from($fromAddress);
                        }
                    });

                    $record->update([
                        'status' => 'replied',
                        'replied_at' => now(),
                        'reply_sent_by' => auth()->id(),
                    ]);

                    Notification::make()
                        ->title('Reply sent.')
                        ->success()
                        ->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
