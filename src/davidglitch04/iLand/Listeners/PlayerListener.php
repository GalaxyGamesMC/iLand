<?php

namespace davidglitch04\iLand\Listeners;

use davidglitch04\iLand\iLand;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerListener implements Listener{

    protected iLand $iland;

    public function __construct(iLand $iland)
    {
        $this->iland = $iland;
    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        if($this->iland->getSessionManager()->inSession($player)){
            $this->iland->getSessionManager()->removePlayer($player);
        }
    }
}