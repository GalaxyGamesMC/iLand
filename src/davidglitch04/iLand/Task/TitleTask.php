<?php

namespace davidglitch04\iLand\Task;

use davidglitch04\iLand\iLand;
use pocketmine\player\Player;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;

class TitleTask extends Task{

    protected Player $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    public function onRun(): void
    {
        if(!$this->player->isConnected()){
            return;
        }
        $status = '';
        $statusA = iLand::getInstance()->getSessionManager()->getSession($this->player)->isNull("A");
        $statusB = iLand::getInstance()->getSessionManager()->getSession($this->player)->isNull("B");
        if($statusA){
            $status = "A";
        } elseif($statusB){
            $status = "B";
        }
        $this->player->sendTitle(iLand::getLanguage()->translateString("title.rangeselector.inmode"), iLand::getLanguage()->translateString("title.rangeselector.selectpoint", [$status]));
        if(!$statusA and !$statusB and iLand::getInstance()->getSessionManager()->inSession($this->player)){
            $this->player->sendTitle(iLand::getLanguage()->translateString("title.selectland.complete1"), iLand::getLanguage()->translateString("title.selectland.complete2"));
        }
        if(!iLand::getInstance()->getSessionManager()->inSession($this->player)){
            throw new CancelTaskException();
        }
    }
}