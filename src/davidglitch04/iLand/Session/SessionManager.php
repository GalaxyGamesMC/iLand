<?php

namespace davidglitch04\iLand\Session;

use pocketmine\player\Player;

class SessionManager
{
    private $session = [];

    public function __construct()
    {
        //NOTHING
    }

    public function addPlayer(Player $player): void
    {
        $name = strtolower($player->getName());
        if (!isset($this->session[$name])) {
            $this->session[$name] = new Session($name);
        }
    }

    public function inSession(Player $player): bool
    {
        $name = strtolower($player->getName());
        if (isset($this->session[$name])) {
            return true;
        } else {
            return false;
        }
    }

    public function removePlayer(Player $player): void{
        $name = strtolower($player->getName());
        if (isset($this->session[$name])) {
            unset($this->session[$name]);
        }
    }

    public function getSession(Player $player): Session
    {
        $name = strtolower($player->getName());

        return $this->session[$name];
    }
}
