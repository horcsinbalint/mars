<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EvaluationFormAvailable extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $deadline;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(?Carbon $deadline)
    {
        $this->deadline = $deadline?->format('Y-m-d');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.evaluation_form_available')
                    ->subject('Az év végi értékelő form elérhető!');
    }
}
