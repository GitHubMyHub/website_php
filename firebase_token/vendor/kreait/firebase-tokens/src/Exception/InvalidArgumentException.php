<?php

namespace Firebase\Auth\Token\Exception;

//use Lcobucci\JWT\Token;

class InvalidArgumentException  extends \LogicException
{
    public function __construct()
    {
        //$expiredSince = \DateTimeImmutable::createFromFormat('U', $token->getClaim('exp'));

        $message = sprintf('This token-syntax is invalid');

        parent::__construct($message);
    }
}
