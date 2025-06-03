<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class BookingConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Booking Confirmation - ' . $this->booking->showtime->movie->title)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your booking has been confirmed.')
            ->line('Movie: ' . $this->booking->showtime->movie->title)
            ->line('Showtime: ' . $this->booking->showtime->start_time->format('M d, Y h:i A'))
            ->line('Seats: ' . $this->booking->seats)
            ->line('Total Amount: $' . number_format($this->booking->total_amount, 2))
            ->action('View Booking', route('bookings.show', $this->booking))
            ->line('Thank you for choosing our service!');
    }
} 