<?php

namespace Assist\Task\Notifications;

use App\Models\User;
use Assist\Task\Models\Task;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Assist\Task\Filament\Resources\TaskResource\Pages\EditTask;
use Filament\Notifications\Notification as FilamentNotification;

class TaskAssignedToUser extends Notification implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Task $task,
    ) {}

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $truncatedTaskDescription = str($this->task->description)->limit(50);

        return (new \App\Notifications\MailMessage())
            ->emailTemplate($this->resolveEmailTemplate())
            ->subject('You have been assigned a new Task')
            ->line('You have been assigned the task: ')
            ->line("\"{$truncatedTaskDescription}\"")
            ->action('test', 'google.com');
    }

    public function toDatabase(User $notifiable): array
    {
        $url = EditTask::getUrl(['record' => $this->task]);

        $title = str($this->task->title)->limit();

        $link = new HtmlString("<a href='{$url}' target='_blank' class='underline'>{$title}</a>");

        return FilamentNotification::make()
            ->success()
            ->title("You have been assigned a new Task: {$link}")
            ->getDatabaseMessage();
    }

    protected function resolveEmailTemplate(): ?EmailTemplate
    {
        return null;//EmailTemplate::first();
    }
}
