<?php

namespace davidglitch04\iLand\Listeners;

use davidglitch04\iLand\Form\BuyForm;
use davidglitch04\iLand\iLand;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
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

    public function onInteract(PlayerInteractEvent $event): void{
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        if($event->getAction() == PlayerInteractEvent::LEFT_CLICK_BLOCK){
            $statusA = $this->iland->getSessionManager()->getSession($player)->isNull("A");
            $statusB = $this->iland->getSessionManager()->getSession($player)->isNull("B");
            if(!$statusA and !$statusB){
                new BuyForm($player);
                return;
            }
            if($this->iland->getSessionManager()->inSession($player)){
                if($item->equals($this->iland->getTool(), false, false)){
                    $player->sendTip(iLand::getLanguage()->translateString('title.rangeselector.pointed', [
                        $this->iland->getSessionManager()->getSession($player)->setNextPosition($player->getPosition()),
                        $player->getWorld()->getFolderName(),
                        $player->getLocation()->getX(),
                        $player->getLocation()->getY(),
                        $player->getLocation()->getZ(), ])
                    );
                }
            }
        }
    }
}