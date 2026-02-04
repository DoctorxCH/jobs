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
            Actions\Action::make('save')
                ->label('Save')
                ->action(fn () => $this->save()),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            Actions\Action::make('sendReply')
                ->label('Send reply')
                ->icon('heroicon-o-paper-airplane')
                ->requiresConfirmation()
                ->action(function () {
                    $record = $this->record;
                    $state = $this->form->getState();
                    $replyBody = (string) ($state['reply_body'] ?? $record->reply_body ?? '');

                    if (trim(strip_tags($replyBody)) === '') {
                        Notification::make()
                            ->title('Reply body is empty.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $settings = ContactSetting::query()->first();
                    $fromAddress = $settings?->outbox_email;

                    $subject = $record->subject ? 'Re: ' . $record->subject : 'Reply';

                    Mail::send('mails.contact-reply', [
                        'replyBody' => $replyBody,
                        'subject' => $subject,
                        'footer' => '365jobs · Reply from support',
                    ], function ($message) use ($record, $fromAddress, $subject) {
                        $message->to($record->email, $record->name)->subject($subject);
                        if ($fromAddress) {
                            $message->from($fromAddress);
                        }
                    });

                    $record->update([
                        'reply_body' => $replyBody,
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
