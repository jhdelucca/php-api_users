<?php

namespace App\Message;

use Symfony\Component\HttpFoundation\Request;

final class CreateUserMessage
{
   private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }




}
