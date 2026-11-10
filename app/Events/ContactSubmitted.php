<?php

namespace App\Events;

use App\Models\Contact;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(public Contact $contact)
    {
    }
}
