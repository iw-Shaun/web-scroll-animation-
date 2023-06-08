<?php

namespace App\Http\Controllers;

use LINE\LINEBot\MessageBuilder;

class LineCustomMessageBuilder implements MessageBuilder
{
    private $message = [];

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function buildMessage()
    {
        return $this->message;
    }
}