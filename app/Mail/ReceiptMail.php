<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $data;
    public $date;

    public function __construct($data, $date)
    {
        $this->data = $data;
        $this->date = $date;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $totals = 0;

        foreach($this->data['rpt'] as $rpt)
        {
            $totals += $rpt['amount_to_pay'];
        }

        return $this->markdown('emails.receipt')->with(['trans_data' => $this->data, 'totals' => $totals, 'date' => $this->date]);
    }
}
