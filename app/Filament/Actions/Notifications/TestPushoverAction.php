<?php

namespace App\Filament\Actions\Notifications;

use App\Enums\NotificationMethods;
use App\Services\Helpers\NotificationsHelper;
use Exception;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use NotificationChannels\Pushover\Pushover;

class TestPushoverAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'pushover_test';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Send test'));

        $this->successNotificationTitle(__('Test notification sent successfully'));

        $this->failureNotificationTitle(__('Error'));

        $this->icon('heroicon-m-bell');

        $this->color('gray');

        $this->action(fn () => $this->testSendingNotification());
    }

    protected function testSendingNotification(): void
    {
        if (! NotificationsHelper::isEnabled(NotificationMethods::Pushover)) {
            Notification::make()
                ->title('Error')
                ->body('Please enable and save your Pushover settings first')
                ->danger()
                ->send();

            return;
        }

        if (empty(config('services.pushover.token'))) {
            Notification::make()
                ->title('Error')
                ->body('Please save your Pushover application token first')
                ->danger()
                ->send();

            return;
        }

        $user = auth()->user();
        $userKey = $user->routeNotificationForPushover();

        if (empty($userKey)) {
            Notification::make()
                ->title('Error')
                ->body('Please set your Pushover user key in your profile first')
                ->danger()
                ->send();

            return;
        }

        try {
            app(Pushover::class)->send([
                'user' => $userKey,
                'title' => 'Test PriceBuddy notification',
                'message' => 'This is a test notification from PriceBuddy',
                'url' => url('/'),
            ], $user);

            $this->success();
        } catch (Exception $e) {
            Notification::make()
                ->title('Failed to send test notification')
                ->body('Error: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }
}
