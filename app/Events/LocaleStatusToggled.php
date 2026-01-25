<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LocaleStatusToggled
{
    use Dispatchable, SerializesModels;

    public string $key;
    public string $newStatus;

    public function __construct(string $key, string $newStatus)
    {
        $this->key = $key;
        $this->newStatus = $newStatus;
    }
}