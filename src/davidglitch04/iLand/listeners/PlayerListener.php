<?php

namespace davidglitch04\iLand\listeners;

use davidglitch04\iLand\form\BuyForm;
use davidglitch04\iLand\iLand;
use davidglitch04\iLand\item\ItemUtils;
use davidglitch04\iLand\libs\Vecnavium\FormsUI\SimpleForm;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\player\PlayerBucketEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\player\Player;

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
        if($event->getAction() == PlayerInteractEvent::LEFT_CLICK_BLOCK
         and $this->iland->getSessionManager()->inSession($player)){
            $statusA = $this->iland->getSessionManager()->getSession($player)->isNull("A");
            $statusB = $this->iland->getSessionManager()->getSession($player)->isNull("B");
            $x = $player->getPosition()->getX();
            $z = $player->getPosition()->getZ();
            if (iLand::getInstance()->getProvider()->isOverlap($x, $z, $x, $z, $player->getWorld())) {
                $event->cancel();
                $form = new SimpleForm(function (Player $player, $data){
                    if(!isset($data)){
                        return false;
                    }
                });
                $form->setTitle(iLand::getLanguage()->translateString("gui.overlap.title"));
                $form->setContent(iLand::getLanguage()->translateString("gui.overlap.content"));
                $form->addButton(iLand::getLanguage()->translateString("gui.general.close"));
                $player->sendForm($form);
                return;
            }
            if($this->iland->getSessionManager()->inSession($player)){
                if($item->equals(ItemUtils::getItem(), false, false)){
                    if(!$statusA and !$statusB){
                        new BuyForm($player);
                        return;
                    }
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
        if (!$this->iland->getProvider()->testPlayer($event)){
            $event->cancel();
        }
    }

    public function onBucket(PlayerBucketEvent $event): void{
        if (!$this->iland->getProvider()->testPlayer($event)){
            $event->cancel();
        }
    }

    public function onDrop(PlayerDropItemEvent $event): void{
        if (!$this->iland->getProvider()->testPlayer($event)){
            $event->cancel();
        }
    }

    public function onPickup(EntityItemPickupEvent $event): void{
        if($event->getEntity() instanceof Player){
            if (!$this->iland->getProvider()->testPlayer($event)){
                $event->cancel();
            }
        }
    }
}