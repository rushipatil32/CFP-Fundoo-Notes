<?php

namespace App\Exceptions;

use Exception;

class FundoNotesException extends Exception
{
    public function message()
    {
        return $this->getMessage();
    }
    public function statusCode()
    {
        return $this->getCode();
    }
}
