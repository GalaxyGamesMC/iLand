<?php

namespace davidglitch04\iLand\Session;

use pocketmine\world\Position;

class Session
{
    private array $data = [];

    public function __construct(string $username)
    {
        $this->data['Username'] = $username;
        $this->data['A'] = null;
        $this->data['B'] = null;
    }

    public function setPositionA(Position $A): void
    {
        $this->data['A'] = $A;
    }

    public function setPositionB(Position $B): void
    {
        $this->data['B'] = $B;
    }
}
