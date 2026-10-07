<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MoneyMovementAlert extends Notification implements ShouldQueue
{
    use Queueable;

    protected ?string $clientName = '';
    protected ?string $direction = '';
    protected ?string $amount = '';
    protected ?string $mode = '';


    public function __construct(string $clientName, string $direction, string $amount, string $mode)
    {
        $this->clientName = $clientName;
        $this->direction = $direction;
        $this->amount = $amount;
        $this->mode = $mode;
    }


    public function via(object $notifiable): array
    {
        return ['mail'];
    }


    public function toMail(): MailMessage
    {
        if ($this->mode === 'failure') {
            return (new MailMessage)
                ->subject('Transaction Failed')
                ->greeting('Hello ' . $this->clientName . ',')
                ->line("Your transaction for ₦{$this->amount} failed.")
                ->line('Please try again or contact support.')
                ->theme('default');
        }

        if ($this->direction === 'credit') {
            return (new MailMessage)
                ->subject('Money Received')
                ->greeting('Hello ' . $this->clientName . ',')
                ->line("Your account has been credited with ₦{$this->amount}.")
                ->line('Thank you for using our application!')
                ->theme('default');
        }

        return (new MailMessage)
            ->subject('Debit Transaction')
            ->greeting('Hello ' . $this->clientName . ',')
            ->line("₦{$this->amount} has been debited from your account.")
            ->line('Your transaction was successful.')
            ->line('Thank you for using our application!')
            ->theme('default');
    }


    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
