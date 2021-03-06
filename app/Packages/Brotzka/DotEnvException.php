<?php

/**
 * Created by PhpStorm.
 * User: Fabian
 * Date: 28.07.16
 * Time: 06:08
 */
namespace App\Packages\Brotzka;

use Exception;

class DotEnvException extends Exception
{
    public function __construct($message, $code, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ":[{$this->code}]: {$this->message}\n";
    }
}
