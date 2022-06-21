<?php

namespace Abolfazlrastegar\LaravelSms\Exception;
use Exception;
class createMessageErrorException extends Exception
{
    public static function notClass ($sms, $namespace)
    {
        return new static('class ' .$sms . ' is not found ' .  $namespace);
    }

}
