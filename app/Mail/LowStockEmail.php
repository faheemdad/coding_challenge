<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $ingredient;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($ingredient)
    {
        $this->ingredient = $ingredient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Low ingredient stock level')
                ->view('emails.low_stock')->with('ingredient', $this->ingredient);
    }
}
