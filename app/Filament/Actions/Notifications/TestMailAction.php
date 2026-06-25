<?php

namespace App\Filament\Actions\Notifications;

use App\Enums\NotificationMethods;
use App\Mail\TestMail;
use App\Services\Helpers\NotificationsHelper;
use Exception;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

class TestMailAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'mail_test';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Send test email'));

        $this->successNotificationTitle(__('Test email sent successfully'));

        $this->failureNotificationTitle(__('Error'));

        $this->icon('heroicon-m-bell');

        $this->color('gray');

        $this->action(fn () => $this->testSendingEmail());
    }

    protected function testSendingEmail(): void
    {
        if (! NotificationsHelper::isEnabled(NotificationMethods::Mail)) {
            Notification::make()
                ->title('Error')
                ->body('Please enable and save your Email settings first')
                ->danger()
                ->send();

            return;
        }

        try {
            Mail::to(auth()->user()->email)->send(new TestMail);

            $this->success();
        } catch (Exception $e) {
            Notification::make()
                ->title('Failed to send test email')
                ->body('Error: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }
}
