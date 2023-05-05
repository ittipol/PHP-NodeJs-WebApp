<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccountVarify extends Mailable
{
    use Queueable, SerializesModels;

    // public $email;
    public $key;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      $address = '';
      $name = 'DealDee';
      $subject = 'ยืนยันบัญชีของคุณ';

      $data = array(
        'key' => $this->key,
        // 'email' => $this->email,
        'link' => url('account/verify').'?key='.$this->key
      );

      return $this->view('emails.account_verify', $data)
      ->from($address, $name)
      ->subject($subject);
      
    }
}
